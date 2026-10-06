<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTreatmentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('treatment_plan'));
    }

    public function rules(): array
    {
        return [
            // child_id and discipline are immutable on edit
            'lead_staff_id' => ['required', 'integer', 'exists:staff,id'],

            'start_date' => ['required', 'date'],
            'target_review_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'review_cycle' => ['required', Rule::in([
                'weekly', 'biweekly', 'monthly', 'quarterly', 'ad_hoc',
            ])],
            'next_review_date' => ['nullable', 'date', 'after_or_equal:start_date'],

            'overall_objectives' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],

            'diagnosis_ids' => ['array'],
            'diagnosis_ids.*' => ['integer', 'exists:diagnoses,id'],

            'team_members' => ['array'],
            'team_members.*.staff_id' => ['required', 'integer', 'exists:staff,id'],
            'team_members.*.role' => ['nullable', 'string', 'max:80'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $plan = $this->route('treatment_plan');

            if ($this->has('diagnosis_ids')) {
                $valid = \App\Models\Diagnosis::whereIn('id', $this->input('diagnosis_ids'))
                    ->where('child_id', $plan->child_id)
                    ->count();

                if ($valid !== count($this->input('diagnosis_ids'))) {
                    $validator->errors()->add(
                        'diagnosis_ids',
                        'One or more selected diagnoses do not belong to this child.'
                    );
                }
            }
        });
    }
}