<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('guardians.create');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'preferred_name' => ['nullable', 'string', 'max:80'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:guardians,national_id'],

            'phone' => ['required', 'string', 'max:40'],
            'alternate_phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],

            'occupation' => ['nullable', 'string', 'max:120'],
            'employer' => ['nullable', 'string', 'max:120'],
            'education_level' => ['nullable', 'string', 'max:80'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}