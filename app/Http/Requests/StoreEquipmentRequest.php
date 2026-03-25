<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipment_name' => 'required|string|max:100',
            'inventory_id' => 'required|string|max:50|unique:equipment,inventory_id',
            'property_id' => 'nullable|string|max:50|unique:equipment,property_id',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'cfic_id' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
        ];
    }
}
