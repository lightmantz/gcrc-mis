<?php

namespace App\Http\Requests;

use App\Diagnoses\DiagnosisVocabulary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Diagnosis::class);
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'diagnosed_by' => ['required', 'integer', 'exists:staff,id'],
            'assessment_id' => ['nullable', 'integer', 'exists:assessments,id'],

            'condition_key' => ['required', 'string', Rule::in(DiagnosisVocabulary::keys())],
            'condition_other' => [
                'nullable', 'string', 'max:200',
                'required_if:condition_key,other',
            ],

            'diagnosis_type' => ['required', Rule::in(['primary', 'secondary'])],
            'severity' => ['required', Rule::in(['mild', 'moderate', 'severe', 'profound', 'unspecified'])],
            'status' => ['required', Rule::in(['active', 'resolved', 'ruled_out', 'transferred'])],

            'diagnosed_at' => ['required', 'date', 'before_or_equal:today'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:diagnosed_at'],
            'ended_reason' => ['nullable', 'string', 'max:1000'],

            'confirmed' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'confirmed' => $this->boolean('confirmed'),
        ]);
    }

    public function messages(): array
    {
        return [
            'condition_other.required_if' => 'Please specify the condition when "Other" is selected.',
            'ended_at.after_or_equal' => 'The end date cannot be before the diagnosis date.',
        ];
    }

    /**
     * Fill in the condition_label from the vocabulary, or from condition_other.
     */
    protected function passedValidation(): void
    {
        $key = $this->input('condition_key');

        $label = $key === 'other'
            ? $this->input('condition_other')
            : DiagnosisVocabulary::label($key);

        $this->merge(['condition_label' => $label]);
    }
}