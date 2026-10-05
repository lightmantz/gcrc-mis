<?php

namespace App\Http\Requests;

use App\Diagnoses\DiagnosisVocabulary;
use App\Models\Diagnosis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Diagnosis $diagnosis */
        $diagnosis = $this->route('diagnosis');

        return $this->user()->can('update', $diagnosis);
    }

    public function rules(): array
    {
        return [
            // child_id is immutable on edit.
            // diagnosed_by may change if the record is corrected, but usually doesn't.
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

    protected function passedValidation(): void
    {
        $key = $this->input('condition_key');

        $label = $key === 'other'
            ? $this->input('condition_other')
            : DiagnosisVocabulary::label($key);

        $this->merge(['condition_label' => $label]);
    }
}