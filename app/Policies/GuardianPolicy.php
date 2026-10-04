<?php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\User;

class GuardianPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('guardians.view');
    }

    public function view(User $user, Guardian $guardian): bool
    {
        return $user->can('guardians.view');
    }

    public function create(User $user): bool
    {
        return $user->can('guardians.create');
    }

    public function update(User $user, Guardian $guardian): bool
    {
        return $user->can('guardians.edit');
    }

    public function delete(User $user, Guardian $guardian): bool
    {
        if (! $user->can('guardians.delete')) {
            return false;
        }

        // A guardian with children linked cannot be deleted.
        // Detach from children first if truly needed.
        return $guardian->children()->count() === 0;
    }

    public function restore(User $user, Guardian $guardian): bool
    {
        return $user->can('guardians.delete');
    }

    public function forceDelete(User $user, Guardian $guardian): bool
    {
        return false;
    }
}