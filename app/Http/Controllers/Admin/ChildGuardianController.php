<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachGuardianRequest;
use App\Models\Child;
use App\Models\Guardian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChildGuardianController extends Controller
{
    /**
     * Attach a guardian to a child — either an existing one
     * (guardian_id present) or a freshly created one.
     */
    public function store(AttachGuardianRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();

        // Find or create the guardian.
        if (! empty($data['guardian_id'])) {
            $guardian = Guardian::findOrFail($data['guardian_id']);
        } else {
            $guardian = Guardian::create([
                'first_name'      => $data['first_name'],
                'middle_name'     => $data['middle_name'] ?? null,
                'last_name'       => $data['last_name'],
                'phone'           => $data['phone'],
                'alternate_phone' => $data['alternate_phone'] ?? null,
                'email'           => $data['email'] ?? null,
                'address'         => $data['address'] ?? null,
                'occupation'      => $data['occupation'] ?? null,
            ]);
        }

        // Already attached? Update the pivot instead of duplicating.
        if ($child->guardians()->where('guardian_id', $guardian->id)->exists()) {
            $child->guardians()->updateExistingPivot($guardian->id, [
                'relationship'        => $data['relationship'],
                'relationship_other'  => $data['relationship_other'] ?? null,
                'is_primary'          => $data['is_primary'] ?? false,
                'is_legal'            => $data['is_legal'] ?? false,
                'consent_medical'     => $data['consent_medical'] ?? false,
                'consent_education'   => $data['consent_education'] ?? false,
                'consent_photography' => $data['consent_photography'] ?? false,
                'lives_with_child'    => $data['lives_with_child'] ?? false,
                'notes'               => $data['notes'] ?? null,
            ]);
        } else {
            // Enforce only one primary guardian per child.
            if (! empty($data['is_primary'])) {
                $child->guardians()->updateExistingPivot(
                    $child->guardians()->wherePivot('is_primary', true)->pluck('guardians.id')->all(),
                    ['is_primary' => false]
                );
            }

            $child->guardians()->attach($guardian->id, [
                'relationship'        => $data['relationship'],
                'relationship_other'  => $data['relationship_other'] ?? null,
                'is_primary'          => $data['is_primary'] ?? false,
                'is_legal'            => $data['is_legal'] ?? false,
                'consent_medical'     => $data['consent_medical'] ?? false,
                'consent_education'   => $data['consent_education'] ?? false,
                'consent_photography' => $data['consent_photography'] ?? false,
                'lives_with_child'    => $data['lives_with_child'] ?? false,
                'notes'               => $data['notes'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', "Guardian {$guardian->full_name} linked to {$child->full_name}.");
    }

    /**
     * Update the pivot (relationship flags) for one guardian on one child.
     */
    public function update(Request $request, Child $child, Guardian $guardian): RedirectResponse
    {
        $data = $request->validate([
            'relationship' => ['required', Rule::in([
                'mother', 'father', 'grandmother', 'grandfather',
                'aunt', 'uncle', 'sibling', 'legal_guardian',
                'foster_parent', 'other',
            ])],
            'relationship_other' => ['nullable', 'string', 'max:80'],
            'is_primary' => ['boolean'],
            'is_legal' => ['boolean'],
            'consent_medical' => ['boolean'],
            'consent_education' => ['boolean'],
            'consent_photography' => ['boolean'],
            'lives_with_child' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Enforce one primary per child.
        if (! empty($data['is_primary'])) {
            $otherIds = $child->guardians()
                ->wherePivot('is_primary', true)
                ->where('guardians.id', '!=', $guardian->id)
                ->pluck('guardians.id')
                ->all();

            if (! empty($otherIds)) {
                $child->guardians()->updateExistingPivot($otherIds, ['is_primary' => false]);
            }
        }

        $child->guardians()->updateExistingPivot($guardian->id, [
            'relationship'        => $data['relationship'],
            'relationship_other'  => $data['relationship_other'] ?? null,
            'is_primary'          => $request->boolean('is_primary'),
            'is_legal'            => $request->boolean('is_legal'),
            'consent_medical'     => $request->boolean('consent_medical'),
            'consent_education'   => $request->boolean('consent_education'),
            'consent_photography' => $request->boolean('consent_photography'),
            'lives_with_child'    => $request->boolean('lives_with_child'),
            'notes'               => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', "Relationship with {$guardian->full_name} updated.");
    }

    /**
     * Detach a guardian from a child.
     * Deletes only the pivot row, not the guardian record itself.
     */
    public function destroy(Child $child, Guardian $guardian): RedirectResponse
    {
        $child->guardians()->detach($guardian->id);

        return redirect()
            ->route('admin.children.show', $child)
            ->with('status', "Guardian {$guardian->full_name} unlinked from {$child->full_name}.");
    }
}