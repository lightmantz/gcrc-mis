<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('children.edit');
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB
                'mimes:pdf,jpg,jpeg,png,doc,docx',
            ],
            'title' => ['nullable', 'string', 'max:200'],
            'category' => ['required', Rule::in([
                'referral_letter', 'medical_report', 'assessment_report',
                'consent_form', 'discharge_document', 'identification',
                'photo', 'other',
            ])],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Only PDF, images (jpg/png), and Word documents are allowed.',
            'file.max' => 'Files must be 10 MB or smaller.',
        ];
    }
}