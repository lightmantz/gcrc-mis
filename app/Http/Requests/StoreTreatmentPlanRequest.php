<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTreatmentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\TreatmentPlan::class);
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'lead_staff_id' => ['required', 'integer', 'exists:staff,id'],

            'discipline' => ['required', Rule::in([
                'physiotherapy', 'occupational_therapy', 'speech',
                'psychology', 'social_work', 'education', 'multi_disciplinary',
            ])],

            'start_date' => ['required', 'date'],
            'target_review_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'review_cycle' => ['required', Rule::in([
                'weekly', 'biweekly', 'monthly', 'quarterly', 'ad_hoc',
            ])],
            'next_review_date' => ['nullable', 'date', 'after_or_equal:start_date'],

            'overall_objectives' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],

            // Diagnoses the plan addresses (IDs from the child's diagnoses)
            'diagnosis_ids' => ['array'],
            'diagnosis_ids.*' => ['integer', 'exists:diagnoses,id'],

            // Contributing team members
            'team_members' => ['array'],
            'team_members.*.staff_id' => ['required', 'integer', 'exists:staff,id'],
            'team_members.*.role' => ['nullable', 'string', 'max:80'],
        ];
    }

    /**
     * Ensure the selected diagnoses and team members belong to this child.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $childId = $this->input('child_id');

            if ($childId && $this->has('diagnosis_ids')) {
                $valid = \App\Models\Diagnosis::whereIn('id', $this->input('diagnosis_ids'))
                    ->where('child_id', $childId)
                    ->count();

                if ($valid !== count($this->input('diagnosis_ids'))) {
                    $validator->errors()->add(
                        'diagnosis_ids',
                        'One or more selected diagnoses do not belong to this child.'
                    );
                }
            }

            if ($this->filled('lead_staff_id') && $this->has('team_members')) {
                $lead = (int) $this->input('lead_staff_id');
                $teamIds = collect($this->input('team_members'))
                    ->pluck('staff_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                if (in_array($lead, $teamIds, true)) {
                    $validator->errors()->add(
                        'team_members',
                        'The lead clinician should not also be listed as a team member.'
                    );
                }
            }
        });
    }
}