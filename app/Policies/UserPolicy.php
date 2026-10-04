<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        // A user cannot edit themselves through this module —
        // self-service edits go through the profile page.
        if ($user->id === $model->id) {
            return false;
        }

        return $user->can('users.edit');
    }

    public function delete(User $user, User $model): bool
    {
        // Never allow deleting yourself.
        if ($user->id === $model->id) {
            return false;
        }

        // Never delete the last remaining System Administrator.
        if ($model->hasRole('System Administrator')
            && User::role('System Administrator')->count() <= 1) {
            return false;
        }

        return $user->can('users.delete');
    }

    public function activate(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->can('users.activate');
    }

    public function deactivate(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole('System Administrator')
            && User::role('System Administrator')->where('is_active', true)->count() <= 1) {
            return false;
        }

        return $user->can('users.deactivate');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('users.delete');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false; // Never hard-delete users
    }
}