<?php

namespace App\Policies;

use App\Models\Child;
use App\Models\User;

class ChildPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('children.view');
    }

    public function view(User $user, Child $child): bool
    {
        return $user->can('children.view');
    }

    /**
     * Controls whether the user sees the clinical fields:
     * disability_summary, primary_condition, chronic_conditions,
     * current_medications, allergies, and the "Medical" card on the profile.
     */
    public function viewMedical(User $user, Child $child): bool
    {
        return $user->can('children.view_medical');
    }

    public function create(User $user): bool
    {
        return $user->can('children.create');
    }

    public function update(User $user, Child $child): bool
    {
        return $user->can('children.edit');
    }

    public function delete(User $user, Child $child): bool
    {
        if (! $user->can('children.delete')) {
            return false;
        }

        // Once clinical records exist, only status change is allowed.
        // The check will grow as modules land.
        return ! $this->hasClinicalRecords($child);
    }

    public function restore(User $user, Child $child): bool
    {
        return $user->can('children.delete');
    }

    public function forceDelete(User $user, Child $child): bool
    {
        return false; // never hard-delete clinical records
    }

    private function hasClinicalRecords(Child $child): bool
    {
        // Placeholder — becomes meaningful as clinical modules land.
        return false;
    }
}