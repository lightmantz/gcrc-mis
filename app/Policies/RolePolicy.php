<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Seeded roles that must never be deleted.
     * Every role created by RoleSeeder is protected.
     */
    private const PROTECTED_ROLES = [
        'System Administrator',
        'Center Manager/Director',
        'Clinical/Medical Staff',
        'Physiotherapist',
        'Vocational Training Expert',
        'Occupational Therapist',
        'Speech/Language Therapist',
        'Nurse',
        'Teacher',
        'Head Teacher / Education Coordinator',
        'Special Education Teacher',
        'Teaching Assistant',
        'School Records Officer',
        'School Administrator',
        'Examination/Assessment Officer',
        'Social Worker',
        'Psychologist/Counselor',
        'Matron/Patron',
        'Reception/Records Officer',
        'Pharmacy/Store Officer',
        'Finance/Accounts Officer',
        'Data/Reporting Officer',
    ];

    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        if (! $user->can('roles.edit')) {
            return false;
        }

        // System Administrator permissions cannot be reduced.
        // Prevents an admin from locking themselves out.
        if ($role->name === 'System Administrator') {
            return false;
        }

        return true;
    }

    public function delete(User $user, Role $role): bool
    {
        if (! $user->can('roles.delete')) {
            return false;
        }

        // Seeded roles are permanent.
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return false;
        }

        // A role with users assigned cannot be deleted.
        // Reassign users first, or the accounts will lose their role.
        if ($role->users()->count() > 0) {
            return false;
        }

        return true;
    }
}