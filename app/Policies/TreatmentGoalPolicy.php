<?php

namespace App\Policies;

use App\Models\TreatmentGoal;
use App\Models\User;

class TreatmentGoalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('treatment_plans.view');
    }

    public function view(User $user, TreatmentGoal $goal): bool
    {
        return $user->can('treatment_plans.view');
    }

    public function create(User $user): bool
    {
        // Goals are always created within a plan — the route is scoped to
        // a specific plan, and the plan's policy has already been checked
        // by middleware. This is a second line of defense.
        return $user->can('treatment_plans.edit');
    }

    public function update(User $user, TreatmentGoal $goal): bool
    {
        if (! $user->can('treatment_plans.edit')) {
            return false;
        }

        // Goals on closed plans are frozen.
        if ($goal->plan->isClosed()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, TreatmentGoal $goal): bool
    {
        if (! $user->can('treatment_plans.edit')) {
            return false;
        }

        // Active plans shouldn't lose their goals silently — require a
        // reason by closing the goal with status "discontinued" instead.
        // Draft plans may freely add/remove goals.
        return $goal->plan->isDraft();
    }

    public function recordProgress(User $user, TreatmentGoal $goal): bool
    {
        if (! $user->can('treatment_plans.edit')) {
            return false;
        }

        // Cannot add progress notes to a closed plan.
        if ($goal->plan->isClosed()) {
            return false;
        }

        return true;
    }
}