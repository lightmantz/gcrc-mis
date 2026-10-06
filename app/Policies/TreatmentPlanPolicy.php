<?php

namespace App\Policies;

use App\Models\TreatmentPlan;
use App\Models\User;

class TreatmentPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('treatment_plans.view');
    }

    public function view(User $user, TreatmentPlan $plan): bool
    {
        return $user->can('treatment_plans.view');
    }

    public function create(User $user): bool
    {
        if (! $user->can('treatment_plans.create')) {
            return false;
        }

        // Only staff in a clinical, therapy, or education category may
        // author treatment plans. Admins and support staff cannot.
        return $user->staff()
            ->whereIn('category', ['clinical', 'therapy', 'education'])
            ->where('status', 'active')
            ->exists();
    }

    public function update(User $user, TreatmentPlan $plan): bool
    {
        if (! $user->can('treatment_plans.edit')) {
            return false;
        }

        // Closed plans cannot be edited.
        if ($plan->isClosed()) {
            return false;
        }

        return $this->isOnTeam($user, $plan);
    }

    public function activate(User $user, TreatmentPlan $plan): bool
    {
        if (! $user->can('treatment_plans.approve')) {
            return false;
        }

        // Only drafts can be activated.
        if (! $plan->isDraft()) {
            return false;
        }

        return $this->isOnTeam($user, $plan);
    }

    public function close(User $user, TreatmentPlan $plan): bool
    {
        if (! $user->can('treatment_plans.approve')) {
            return false;
        }

        // Already closed? No-op.
        if ($plan->isClosed()) {
            return false;
        }

        return $this->isOnTeam($user, $plan);
    }

    public function delete(User $user, TreatmentPlan $plan): bool
    {
        if (! $user->can('treatment_plans.delete')) {
            return false;
        }

        // Only draft plans may be deleted. Active and closed plans are
        // clinical records and must not disappear.
        return $plan->isDraft();
    }

    public function restore(User $user, TreatmentPlan $plan): bool
    {
        return $user->can('treatment_plans.delete') && $plan->isDraft();
    }

    public function forceDelete(User $user, TreatmentPlan $plan): bool
    {
        return false;
    }

    /**
     * The user must have a linked Staff record that is either the lead
     * or a listed team member on the plan.
     */
    private function isOnTeam(User $user, TreatmentPlan $plan): bool
    {
        $staffIds = $user->staff()->pluck('staff.id')->all();

        if (empty($staffIds)) {
            return false;
        }

        if (in_array($plan->lead_staff_id, $staffIds, true)) {
            return true;
        }

        return $plan->teamMembers()
            ->whereIn('staff.id', $staffIds)
            ->exists();
    }
}