<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('assessments.view');
    }

    public function view(User $user, Assessment $assessment): bool
    {
        return $user->can('assessments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('assessments.create');
    }

    public function update(User $user, Assessment $assessment): bool
    {
        if (! $user->can('assessments.edit')) {
            return false;
        }

        // Finalized assessments are immutable.
        if ($assessment->isFinalized()) {
            return false;
        }

        // Same-category editing: user must have a linked Staff record
        // whose category matches the assessment type's expected category.
        return $this->userMatchesAssessorCategory($user, $assessment);
    }

    public function finalize(User $user, Assessment $assessment): bool
    {
        if (! $user->can('assessments.approve')) {
            return false;
        }

        if ($assessment->isFinalized()) {
            return false;
        }

        return $this->userMatchesAssessorCategory($user, $assessment);
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        if (! $user->can('assessments.delete')) {
            return false;
        }

        // Finalized assessments cannot be deleted — they're clinical records.
        // If an error must be corrected, it's superseded by a new assessment.
        if ($assessment->isFinalized()) {
            return false;
        }

        return true;
    }

    public function restore(User $user, Assessment $assessment): bool
    {
        return $user->can('assessments.delete') && $assessment->isDraft();
    }

    public function forceDelete(User $user, Assessment $assessment): bool
    {
        return false; // never hard-delete clinical records
    }

    /**
     * The user must have at least one Staff record whose category matches
     * the assessment type's expected assessor category.
     */
    private function userMatchesAssessorCategory(User $user, Assessment $assessment): bool
    {
        $expected = $assessment->assessor_category;

        if (! $expected) {
            return false;
        }

        return $user->staff()
            ->where('category', $expected)
            ->where('status', 'active')
            ->exists();
    }
}