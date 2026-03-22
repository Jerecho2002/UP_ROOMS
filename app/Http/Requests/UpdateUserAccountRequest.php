<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('userAccount');

        return [
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($this->userAccount->user_id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'employee_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('user_accounts', 'employee_number')->ignore($userId),
            ],
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'college_id' => 'required|integer|exists:colleges,id',
            'department_id' => 'required|integer|exists:departments,id',
            'status' => 'required|in:active,inactive,suspended,pending',
        ];
    }
}
