<?php

namespace App\Http\Requests;

use App\Support\GoalCategories;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTreatmentGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The plan is bound by route; we delegate to the plan's policy.
        return $this->user()->can('update', $this->route('treatment_plan'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:4000'],
            'category' => ['required', Rule::in(GoalCategories::keys())],

            'baseline' => ['nullable', 'string', 'max:2000'],
            'target' => ['nullable', 'string', 'max:2000'],
            'measure' => ['nullable', 'string', 'max:2000'],

            'priority' => ['required', Rule::in(['high', 'medium', 'low'])],
            'target_date' => ['nullable', 'date'],

            'status' => ['required', Rule::in([
                'not_started', 'in_progress', 'achieved',
                'partially_achieved', 'discontinued',
            ])],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}