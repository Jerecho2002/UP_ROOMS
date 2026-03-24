<?php

namespace App\Http\Requests;

use App\Models\Equipment;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_name'         => 'required|string|max:255',
            'room_code'         => 'required|string|max:100|unique:rooms,room_code',
            'building_id'       => 'required|exists:buildings,id',
            'college_id'        => 'required|exists:colleges,id',
            'department_id'     => 'nullable|exists:departments,id',
            'room_type_id'      => 'required|exists:room_types,id',
            'assigned_user_id'  => 'nullable|exists:user_accounts,id',
            'floor_number'      => 'required|integer|min:0',
            'location'          => 'nullable|string|max:255',
            'capacity'          => 'required|integer|min:1',
            'description'       => 'nullable|string',
            'equipment'           => 'nullable|array',
            'equipment.*.id'      => 'required|exists:equipment,id',
            'equipment.*.qty'     => 'required|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (!$this->equipment) return;

            foreach ($this->equipment as $item) {
                $equipment = Equipment::find($item['id']);

                if (!$equipment) continue;

                $requestedQty = $item['qty'];

                if (($equipment->quantity - $requestedQty) < 0) {
                    $validator->errors()->add(
                        'equipment',
                        "{$equipment->equipment_name} does not have enough stock."
                    );
                }

                // Optional: prevent reaching 0
                if (($equipment->quantity - $requestedQty) === 0) {
                    $validator->errors()->add(
                        'equipment',
                        "{$equipment->equipment_name} cannot reach zero stock."
                    );
                }
            }
        });
    }
}
