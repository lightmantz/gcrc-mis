<?php

namespace App\Policies;

use App\Models\TherapyRecord;
use App\Models\User;

class TherapyRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('therapy_records.view');
    }

    public function view(User $user, TherapyRecord $record): bool
    {
        return $user->can('therapy_records.view');
    }

    public function create(User $user): bool
    {
        if (! $user->can('therapy_records.create')) {
            return false;
        }

        // Only clinicians can author sessions.
        return $user->staff()
            ->whereIn('category', ['clinical', 'therapy', 'education'])
            ->where('status', 'active')
            ->exists();
    }

    public function update(User $user, TherapyRecord $record): bool
    {
        if (! $user->can('therapy_records.edit')) {
            return false;
        }

        // Finalized sessions are locked.
        if ($record->isFinalized()) {
            return false;
        }

        // Same-category editing: a therapist can edit a draft they
        // belong to, or another therapist in the same discipline can.
        return $this->userMatchesDiscipline($user, $record);
    }

    public function finalize(User $user, TherapyRecord $record): bool
    {
        if (! $user->can('therapy_records.approve')) {
            return false;
        }

        if ($record->isFinalized()) {
            return false;
        }

        return $this->userMatchesDiscipline($user, $record);
    }

    public function delete(User $user, TherapyRecord $record): bool
    {
        if (! $user->can('therapy_records.delete')) {
            return false;
        }

        // Only drafts may be deleted. Finalized sessions are clinical
        // records — to correct one, add an addendum (future module) or
        // void via a specialized admin action.
        return $record->isDraft();
    }

    public function restore(User $user, TherapyRecord $record): bool
    {
        return $user->can('therapy_records.delete') && $record->isDraft();
    }

    public function forceDelete(User $user, TherapyRecord $record): bool
    {
        return false;
    }

    /**
     * The user must have a linked Staff record whose category is
     * clinical, therapy, or education — the same set that can create.
     */
    private function userMatchesDiscipline(User $user, TherapyRecord $record): bool
    {
        $staffIds = $user->staff()->pluck('staff.id')->all();

        if (empty($staffIds)) {
            return false;
        }

        // The user is the therapist who recorded the session.
        if (in_array($record->therapist_id, $staffIds, true)) {
            return true;
        }

        // Or the user has a staff record in the same discipline category.
        $requiredCategory = $this->categoryForDiscipline($record->discipline);

        if (! $requiredCategory) {
            return false;
        }

        return $user->staff()
            ->where('category', $requiredCategory)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Maps a session discipline to the staff category expected to
     * finalize sessions in that discipline.
     */
    private function categoryForDiscipline(string $discipline): ?string
    {
        return match ($discipline) {
            'physiotherapy', 'occupational_therapy', 'speech' => 'therapy',
            'psychology'                                       => 'clinical',
            'social_work'                                      => 'admin',
            'education'                                        => 'education',
            'multi_disciplinary'                               => 'clinical',
            default                                            => null,
        };
    }
}