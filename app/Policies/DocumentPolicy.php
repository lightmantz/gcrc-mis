<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('children.view');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->can('children.view');
    }

    public function create(User $user): bool
    {
        return $user->can('children.edit');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->can('children.edit');
    }

    public function delete(User $user, Document $document): bool
    {
        if (! $user->can('children.edit')) {
            return false;
        }

        // Uploader can delete their own document; admins can delete anything.
        return $document->uploaded_by === $user->id
            || $user->hasRole('System Administrator');
    }
}