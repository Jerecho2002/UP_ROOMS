<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RoomTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('RoomTypes');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = RoomType::orderBy('room_type_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_type_name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_name' => 'required|string|max:100|unique:room_types',
            'description' => 'nullable|string',
            'default_capacity' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['room_type_name']);

        $roomType = RoomType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room type created successfully',
            'data' => $roomType
        ], 201);
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'room_type_name' => 'required|string|max:100|unique:room_types,room_type_name,' . $roomType->id,
            'description' => 'nullable|string',
            'default_capacity' => 'nullable|integer|min:1',
            'features' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['room_type_name']);

        $roomType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room type updated successfully',
            'data' => $roomType
        ]);
    }

    public function destroy(RoomType $roomType)
    {
        $roomType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room type deleted successfully'
        ]);
    }
}
