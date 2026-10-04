<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('children.edit');
    }

    public function rules(): array
    {
        // Same rules as StoreEmergencyContactRequest
        return [
            'name' => ['required', 'string', 'max:120'],
            'relationship' => ['nullable', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:40'],
            'alternate_phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],
            'priority' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}