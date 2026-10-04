<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('children.edit');
    }

    public function rules(): array
    {
        $attachingExisting = $this->filled('guardian_id');

        return [
            // Attach-existing path
            'guardian_id' => ['nullable', 'integer', 'exists:guardians,id'],

            // Create-and-attach path (required only when guardian_id is absent)
            'first_name' => [$attachingExisting ? 'nullable' : 'required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => [$attachingExisting ? 'nullable' : 'required', 'string', 'max:80'],
            'phone' => [$attachingExisting ? 'nullable' : 'required', 'string', 'max:40'],
            'alternate_phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:120'],

            // Relationship metadata — always required
            'relationship' => ['required', Rule::in([
                'mother', 'father', 'grandmother', 'grandfather',
                'aunt', 'uncle', 'sibling', 'legal_guardian',
                'foster_parent', 'other',
            ])],
            'relationship_other' => ['nullable', 'string', 'max:80', 'required_if:relationship,other'],
            'is_primary' => ['boolean'],
            'is_legal' => ['boolean'],
            'consent_medical' => ['boolean'],
            'consent_education' => ['boolean'],
            'consent_photography' => ['boolean'],
            'lives_with_child' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_primary' => $this->boolean('is_primary'),
            'is_legal' => $this->boolean('is_legal'),
            'consent_medical' => $this->boolean('consent_medical'),
            'consent_education' => $this->boolean('consent_education'),
            'consent_photography' => $this->boolean('consent_photography'),
            'lives_with_child' => $this->boolean('lives_with_child'),
        ]);
    }
}