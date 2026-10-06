<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTherapyRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('therapy_record'));
    }

    public function rules(): array
    {
        return [
            // child_id is immutable on edit
            'therapist_id' => ['required', 'integer', 'exists:staff,id'],
            'treatment_plan_id' => ['nullable', 'integer', 'exists:treatment_plans,id'],

            'discipline' => ['required', Rule::in([
                'physiotherapy', 'occupational_therapy', 'speech',
                'psychology', 'social_work', 'education', 'multi_disciplinary',
            ])],

            'session_date' => ['required', 'date', 'before_or_equal:today'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],

            'session_type' => ['required', Rule::in([
                'individual', 'group', 'consultation', 'home_visit', 'telehealth',
            ])],
            'location' => ['nullable', 'string', 'max:120'],

            'status' => ['required', Rule::in([
                'scheduled', 'attended', 'missed', 'cancelled', 'no_show',
            ])],

            'activities' => ['nullable', 'string', 'max:5000'],
            'child_response' => ['nullable', 'string', 'max:5000'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'recommendations' => ['nullable', 'string', 'max:5000'],

            'follow_up_required' => ['boolean'],
            'follow_up_notes' => ['nullable', 'string', 'max:2000'],

            'goal_ids' => ['array'],
            'goal_ids.*' => ['integer', 'exists:treatment_goals,id'],
            'goal_notes' => ['array'],
            'goal_notes.*' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'follow_up_required' => $this->boolean('follow_up_required'),
        ]);
    }
}