<?php

namespace App\Policies;

use App\Models\EmergencyContact;
use App\Models\User;

class EmergencyContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('children.view');
    }

    public function view(User $user, EmergencyContact $contact): bool
    {
        return $user->can('children.view');
    }

    public function create(User $user): bool
    {
        return $user->can('children.edit');
    }

    public function update(User $user, EmergencyContact $contact): bool
    {
        return $user->can('children.edit');
    }

    public function delete(User $user, EmergencyContact $contact): bool
    {
        return $user->can('children.edit');
    }
}