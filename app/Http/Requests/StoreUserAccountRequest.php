<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change to your authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'employee_number' => 'required|string|max:20|unique:user_accounts,employee_number',
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'college_id' => 'required|integer|exists:colleges,id',
            'department_id' => 'required|integer|exists:departments,id',
            'status' => 'required|in:active,inactive',
        ];
    }
}
