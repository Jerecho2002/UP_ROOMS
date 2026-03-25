<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipmentId = $this->route('equipment');

        return [
            'equipment_name' => 'required|string|max:100',
            'inventory_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipment', 'inventory_id')->ignore($equipmentId),
            ],
            'property_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('equipment', 'property_id')->ignore($equipmentId),
            ],
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'cfic_id' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
        ];
    }
}
