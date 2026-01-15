<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')?->id;

        return [
            'department_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'department_name')->ignore($departmentId)
            ],
            'department_code' => [
                'required',
                'string',
                'max:50',
                'unique:departments,department_code,' . $departmentId
            ],
            'college_id' => [
                'nullable',
                'exists:colleges,id'
            ],
            'department_head_id' => [
                'nullable',
                'exists:users,id'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'office_location' => [
                'nullable',
                'string',
                'max:255'
            ],
            'contact_email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'max:20'
            ]
        ];
    }
}
