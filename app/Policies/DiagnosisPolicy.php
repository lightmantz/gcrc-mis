<?php

namespace App\Policies;

use App\Models\Diagnosis;
use App\Models\User;

class DiagnosisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('diagnoses.view');
    }

    public function view(User $user, Diagnosis $diagnosis): bool
    {
        return $user->can('diagnoses.view');
    }

    public function create(User $user): bool
    {
        if (! $user->can('diagnoses.create')) {
            return false;
        }

        // The user must have a linked staff record in a clinical category.
        // Same rule as assessments — diagnoses are made by clinicians,
        // not by administrative staff.
        return $user->staff()
            ->whereIn('category', ['clinical', 'therapy'])
            ->where('status', 'active')
            ->exists();
    }

    public function update(User $user, Diagnosis $diagnosis): bool
    {
        if (! $user->can('diagnoses.edit')) {
            return false;
        }

        // Non-active diagnoses cannot be edited — history is frozen.
        if (! $diagnosis->isActive()) {
            return false;
        }

        // Same clinical-category rule as create.
        return $user->staff()
            ->whereIn('category', ['clinical', 'therapy'])
            ->where('status', 'active')
            ->exists();
    }

    public function close(User $user, Diagnosis $diagnosis): bool
    {
        if (! $user->can('diagnoses.approve')) {
            return false;
        }

        return $diagnosis->isActive();
    }

    public function delete(User $user, Diagnosis $diagnosis): bool
    {
        if (! $user->can('diagnoses.delete')) {
            return false;
        }

        // Only provisional or inactive diagnoses may be deleted.
        // A confirmed active diagnosis must be closed, not deleted.
        return ! $diagnosis->confirmed || ! $diagnosis->isActive();
    }

    public function restore(User $user, Diagnosis $diagnosis): bool
    {
        return $user->can('diagnoses.delete') && ! $diagnosis->confirmed;
    }

    public function forceDelete(User $user, Diagnosis $diagnosis): bool
    {
        return false;
    }
}