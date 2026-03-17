<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTermRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change if you have authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'term_name'         => 'required|string|max:255',
            'term_code'         => 'required|string|max:50|unique:terms,term_code',
            'term_type'         => 'required|string|max:50', // e.g., Regular, Summer
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'enrollment_start'  => 'nullable|date',
            'enrollment_end'    => 'nullable|date|after_or_equal:enrollment_start',
            'classes_start'     => 'nullable|date',
            'classes_end'       => 'nullable|date|after_or_equal:classes_start',
            'examination_start' => 'nullable|date',
            'examination_end'   => 'nullable|date|after_or_equal:examination_start',
            'is_current' => 'required|in:0,1',
            'status'            => 'required|in:upcoming,active,completed,cancelled',
            'academic_year'     => 'required|int',
            'notes'             => 'nullable|string',
        ];
    }
}
