<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoalProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('recordProgress', $this->route('goal'));
    }

    public function rules(): array
    {
        return [
            'recorded_on' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['required', 'string', 'max:3000'],
            'progress_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status_at_record' => ['nullable', Rule::in([
                'not_started', 'in_progress', 'achieved',
                'partially_achieved', 'discontinued',
            ])],
            // Optional: whether to update the goal's own status/progress
            'update_goal' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'update_goal' => $this->boolean('update_goal', true),
        ]);
    }
}