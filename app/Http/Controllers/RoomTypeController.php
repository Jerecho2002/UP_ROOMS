<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $roomTypes = RoomType::withCount('rooms')
            ->orderBy('room_type_name')
            ->paginate(20);

        return Inertia::render('roomtypes', [
            'room_types' => $roomTypes,
            'stats' => [
                'total' => RoomType::count(),
                'total_rooms' => RoomType::withCount('rooms')->get()->sum('rooms_count'),
                'most_common' => RoomType::withCount('rooms')
                    ->orderBy('rooms_count', 'desc')
                    ->first(),
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        $query = RoomType::withCount('rooms');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('room_type_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sort_field', 'room_type_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_name' => 'required|string|max:255|unique:room_types,room_type_name',
            'slug' => 'nullable|string|max:255|unique:room_types,slug',
            'description' => 'nullable|string',
            'default_capacity' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['room_type_name']);
        }

        $roomType = RoomType::create($validated);

        return response()->json([
            'message' => 'Room type created successfully',
            'room_type' => $roomType,
        ], 201);
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'room_type_name' => 'sometimes|required|string|max:255|unique:room_types,room_type_name,' . $roomType->id,
            'slug' => 'nullable|string|max:255|unique:room_types,slug,' . $roomType->id,
            'description' => 'nullable|string',
            'default_capacity' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
        ]);

        $roomType->update($validated);

        return response()->json([
            'message' => 'Room type updated successfully',
            'room_type' => $roomType,
        ]);
    }

    public function destroy(RoomType $roomType)
    {
        // Check if room type is being used
        if ($roomType->rooms()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete room type that is assigned to rooms'
            ], 422);
        }

        $roomType->delete();

        return response()->json([
            'message' => 'Room type deleted successfully'
        ]);
    }
}
