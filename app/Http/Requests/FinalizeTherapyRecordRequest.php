<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizeTherapyRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('finalize', $this->route('therapy_record'));
    }

    public function rules(): array
    {
        // No input — finalization is a state transition only.
        return [];
    }
}