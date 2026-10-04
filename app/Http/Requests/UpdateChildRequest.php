<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('children.edit');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'preferred_name' => ['nullable', 'string', 'max:80'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],

            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],

            'blood_type' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'unknown'])],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'chronic_conditions' => ['nullable', 'string', 'max:2000'],
            'current_medications' => ['nullable', 'string', 'max:2000'],
            'disability_summary' => ['nullable', 'string', 'max:2000'],
            'primary_condition' => ['nullable', Rule::in([
                'cerebral_palsy', 'down_syndrome', 'autism_spectrum',
                'intellectual_disability', 'physical_disability',
                'hearing_impairment', 'visual_impairment',
                'speech_language_disorder', 'learning_disability',
                'multiple_disabilities', 'other',
            ])],
            'primary_condition_other' => ['nullable', 'string', 'max:120'],

            'special_care_requirements' => ['nullable', 'string', 'max:2000'],
            'feeding_requirements' => ['nullable', 'string', 'max:2000'],
            'mobility_notes' => ['nullable', 'string', 'max:2000'],
            'communication_notes' => ['nullable', 'string', 'max:2000'],
            'requires_constant_supervision' => ['boolean'],

            'registration_date' => ['required', 'date'],
            'referred_by' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(['active', 'on_hold', 'discharged', 'deceased'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'requires_constant_supervision' => $this->boolean('requires_constant_supervision'),
        ]);
    }
}