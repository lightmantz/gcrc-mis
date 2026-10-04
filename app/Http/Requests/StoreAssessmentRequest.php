<?php

namespace App\Http\Requests;

use App\Assessments\AssessmentTypeRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('assessments.create');
    }

    public function rules(): array
    {
        $base = [
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'assessor_id' => ['required', 'integer', 'exists:staff,id'],
            'type' => ['required', 'string', Rule::in(AssessmentTypeRegistry::keys())],
            'assessment_date' => ['required', 'date', 'before_or_equal:today'],
            'summary' => ['nullable', 'string', 'max:4000'],
            'recommendations' => ['nullable', 'string', 'max:4000'],

            // The findings payload is validated dynamically below.
            'findings' => ['required', 'array'],
        ];

        // Merge in the type-specific rules for the chosen type.
        $type = $this->input('type');

        if ($type && AssessmentTypeRegistry::has($type)) {
            $base = array_merge($base, AssessmentTypeRegistry::get($type)->rules());
        }

        return $base;
    }

    /**
     * Validate the assessor_id belongs to someone in the correct category.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $assessorId = $this->input('assessor_id');

            if (! $type || ! $assessorId || ! AssessmentTypeRegistry::has($type)) {
                return;
            }

            $expectedCategory = AssessmentTypeRegistry::get($type)->assessorCategory();
            $staff = \App\Models\Staff::find($assessorId);

            if ($staff && $staff->category !== $expectedCategory) {
                $validator->errors()->add(
                    'assessor_id',
                    "The selected assessor must be in the {$expectedCategory} category for this assessment type."
                );
            }
        });
    }
}