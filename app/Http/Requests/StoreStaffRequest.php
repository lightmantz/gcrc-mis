<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('staff.create');
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'preferred_name' => ['nullable', 'string', 'max:80'],

            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:staff,national_id'],

            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'address' => ['nullable', 'string', 'max:500'],

            'emergency_contact_name' => ['nullable', 'string', 'max:120'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:40'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:60'],

            'category' => ['required', Rule::in(['management', 'clinical', 'therapy', 'education', 'admin', 'support'])],
            'department' => ['nullable', 'string', 'max:120'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'professional_qualifications' => ['nullable', 'string', 'max:2000'],
            'specialization' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['required', Rule::in(['full_time', 'part_time', 'contract', 'volunteer', 'intern'])],
            'employment_start_date' => ['required', 'date'],
            'employment_end_date' => ['nullable', 'date', 'after_or_equal:employment_start_date'],
            'status' => ['required', Rule::in(['active', 'on_leave', 'suspended', 'terminated'])],

            'notes' => ['nullable', 'string', 'max:2000'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],

            // Linking user accounts
            'user_ids' => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}