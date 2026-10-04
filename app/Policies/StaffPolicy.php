<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;

class StaffPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('staff.view');
    }

    public function view(User $user, Staff $staff): bool
    {
        return $user->can('staff.view');
    }

    public function viewSensitive(User $user, Staff $staff): bool
    {
        return $user->can('staff.view_sensitive');
    }

    public function create(User $user): bool
    {
        return $user->can('staff.create');
    }

    public function update(User $user, Staff $staff): bool
    {
        return $user->can('staff.edit');
    }

    public function delete(User $user, Staff $staff): bool
    {
        if (! $user->can('staff.delete')) {
            return false;
        }

        // Cannot delete a staff member who has linked clinical records.
        // They must be terminated instead, preserving history.
        if ($this->hasLinkedRecords($staff)) {
            return false;
        }

        return true;
    }

    public function restore(User $user, Staff $staff): bool
    {
        return $user->can('staff.delete');
    }

    public function forceDelete(User $user, Staff $staff): bool
    {
        return false; // never hard-delete
    }

    /**
     * Later modules will attach assessments, therapy records, etc.
     * For now, this checks nothing — it becomes meaningful as those
     * tables are added.
     */
    private function hasLinkedRecords(Staff $staff): bool
    {
        // Placeholder — will grow as modules land.
        return false;
    }
}