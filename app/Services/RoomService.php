<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoomService
{
    public function getRooms(int $perPage = 10, ?string $search = null)
    {
        return Room::with([
            'building:id,building_name',
            'college:id,college_name',
            'department:id,department_name',
            'roomType:id,room_type_name',
            'assignedUser:id,first_name,last_name,middle_name',
            'equipment:id,equipment_name'
        ])
            ->orderByDesc('created_at')
            ->when(
                $search,
                fn($query) =>
                $query->where('room_name', 'like', "%{$search}%")
            )
            ->paginate($perPage)
            ->through(fn($room) => [
                ...$room->toArray(),

                'building_name'   => $room->building?->building_name ?? 'N/A',
                'college_name'    => $room->college?->college_name ?? 'N/A',
                'department_name' => $room->department?->department_name ?? 'N/A',
                'room_type_name'  => $room->roomType?->room_type_name ?? 'N/A',

                'assigned_user' => $room->assignedUser
                    ? trim(
                        $room->assignedUser->first_name . ' ' .
                            ($room->assignedUser->middle_name ?? '') . ' ' .
                            $room->assignedUser->last_name
                    )
                    : 'N/A',

                'equipment_ids' => $room->equipment->pluck('id')->toArray(),

                'equipment' => $room->equipment->map(fn($eq) => [
                    'id'  => $eq->id,
                    'name' => $eq->equipment_name,
                    'qty' => $eq->pivot->quantity ?? 0,
                ])->values()->toArray(),
            ])
            ->withQueryString();
    }

    public function create(array $data): Room
    {
        return DB::transaction(function () use ($data) {

            $room = Room::create($data);

            $this->syncEquipment($room, collect($data['equipment'] ?? []));

            return $room;
        });
    }

    public function update(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {

            $room->update($data);

            $this->updateEquipment($room, collect($data['equipment'] ?? []));

            return $room;
        });
    }

    private function syncEquipment(Room $room, Collection $equipmentData): void
    {
        if ($equipmentData->isEmpty()) return;

        $room->equipment()->sync(
            $equipmentData->mapWithKeys(fn($e) => [
                $e['id'] => ['quantity' => $e['qty']]
            ])
        );

        foreach ($equipmentData as $e) {
            Equipment::find($e['id'])?->decrement('quantity', $e['qty']);
        }
    }

    private function updateEquipment(Room $room, Collection $newEquipment): void
    {
        $oldEquipment = $room->equipment()->get()->keyBy('id');

        $room->equipment()->sync(
            $newEquipment->mapWithKeys(fn($e) => [
                $e['id'] => ['quantity' => $e['qty']]
            ])
        );

        foreach ($newEquipment as $e) {
            $equipment = Equipment::find($e['id']);
            if (!$equipment) continue;

            $oldQty = $oldEquipment[$e['id']]->pivot->quantity ?? 0;
            $newQty = $e['qty'];

            $diff = $newQty - $oldQty;

            if ($diff > 0) {
                $equipment->decrement('quantity', $diff);
            } elseif ($diff < 0) {
                $equipment->increment('quantity', abs($diff));
            }
        }

        $removed = $oldEquipment->keys()->diff($newEquipment->pluck('id'));

        foreach ($removed as $id) {
            $oldQty = $oldEquipment[$id]->pivot->quantity;
            Equipment::find($id)?->increment('quantity', $oldQty);
        }
    }
}
