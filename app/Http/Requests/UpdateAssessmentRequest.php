<?php

namespace App\Http\Requests;

use App\Assessments\AssessmentTypeRegistry;
use App\Models\Assessment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $assessment = $this->route('assessment');

        return $this->user()->can('update', $assessment);
    }

    public function rules(): array
    {
        /** @var Assessment $assessment */
        $assessment = $this->route('assessment');

        $base = [
            // child_id and type are NOT updatable.
            'assessor_id' => ['required', 'integer', 'exists:staff,id'],
            'assessment_date' => ['required', 'date', 'before_or_equal:today'],
            'summary' => ['nullable', 'string', 'max:4000'],
            'recommendations' => ['nullable', 'string', 'max:4000'],
            'findings' => ['required', 'array'],
        ];

        if (AssessmentTypeRegistry::has($assessment->type)) {
            $base = array_merge(
                $base,
                AssessmentTypeRegistry::get($assessment->type)->rules()
            );
        }

        return $base;
    }
}