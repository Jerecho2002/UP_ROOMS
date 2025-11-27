<template>
    <div class="h-full p-4 border-l border-gray-200 flex flex-col bg-white shadow-xl">

        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Room Search & Details </h3>
            <button @click="$emit('close-search')" class="text-gray-500 hover:text-red-600 transition duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="relative mb-6">
            <input
                type="text"
                placeholder="Search for a room, building, or equipment..."
                v-model="localSearchQuery"
                class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 shadow-sm transition duration-150"
            >
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <div v-if="roomData" class="flex flex-col flex-1 overflow-y-auto">
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500 mb-1">Room name</p>
                <p class="text-2xl font-bold text-green-700">{{ roomData.room }}</p>
            </div>

            <div class="mb-6 space-y-2">
                <h4 class="text-md font-bold text-gray-700 border-b pb-1 mb-3">Room Details </h4>

                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <span class="font-semibold text-gray-600">College:</span> <span class="text-gray-900">{{ roomData.college }}</span>
                    <span class="font-semibold text-gray-600">Building:</span> <span class="text-gray-900">{{ roomData.building }}</span>
                    <span class="font-semibold text-gray-600">Floor Number:</span> <span class="text-gray-900">{{ roomData.floorNumber || 'N/A' }}</span>
                    <span class="font-semibold text-gray-600">Type:</span> <span class="text-gray-900">{{ roomData.roomType }}</span>
                    <span class="font-semibold text-gray-600">Capacity:</span> <span class="text-gray-900 font-bold">{{ roomData.capacity }}</span>
                    <span class="font-semibold text-gray-600">Department:</span> <span class="text-gray-900">{{ roomData.department || 'N/A' }}</span>
                </div>

                <div class="grid grid-cols-1 gap-y-2 text-sm pt-2">
                    <span class="font-semibold text-gray-600">Location:</span> <span class="text-gray-900">{{ roomData.location }}</span>
                    <span class="font-semibold text-gray-600">ID:</span> <span class="text-gray-900 font-mono">{{ roomData.id }}</span>
                </div>
                
                <div class="grid grid-cols-1 gap-y-2 text-sm pt-2">
                    <span class="font-semibold text-gray-600">Description:</span> <span class="text-gray-900 italic">{{ roomData.description || 'N/A' }}</span>
                </div>
            </div>

            <div class="mb-6 space-y-2">
                <h4 class="text-md font-bold text-gray-700 border-b pb-1 mb-3">Equipment ⚙️</h4>
                
                <div v-if="roomData.equipments && roomData.equipments.length > 0" class="flex flex-wrap gap-2 text-sm">
                    <span v-for="(item, itemIndex) in roomData.equipments" :key="itemIndex"
                          class="inline-block bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1 rounded-full border border-purple-200">
                        {{ item.name }} ({{ item.quantity }})
                    </span>
                </div>
                <span v-else class="text-gray-500 italic text-sm">No specific equipment listed.</span>
            </div>

            <div class="flex-1 overflow-y-auto">
                <h4 class="text-md font-bold text-gray-700 mb-3 border-b pb-1">Schedules ({{ roomData.schedules ? roomData.schedules.length : 0 }}) 🗓️</h4>
                <div class="space-y-2 text-sm">
                    <div v-if="!roomData.schedules || roomData.schedules.length === 0" class="p-2 text-center text-gray-500 italic bg-gray-50 rounded-lg">No schedules found. Room is likely **Available**.</div>
                    <div v-for="(schedule, index) in roomData.schedules" :key="index" 
                          :class="[schedule.isAvailable ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200']" 
                          class="p-3 border rounded-lg shadow-sm">
                        <p class="font-semibold">{{ schedule.name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ schedule.time }} <span class="font-medium text-gray-400">|</span> {{ schedule.college }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="localSearchQuery.trim()" class="flex-1 overflow-y-auto">
            <h4 class="text-sm font-medium text-gray-500 mb-2 border-b pb-1">Search Results ({{ filteredRoomsForSidebar.length }})</h4>
            <div class="space-y-2 text-sm">
                <div v-if="filteredRoomsForSidebar.length === 0" class="p-4 text-center text-gray-500 italic bg-gray-50 rounded-lg">No rooms match your search query.</div>
                <div
                    v-for="room in filteredRoomsForSidebar"
                    :key="room.id"
                    @click="$emit('select-room', room)"
                    class="p-3 border rounded-lg bg-gray-50 hover:bg-green-100 border-gray-200 hover:border-green-300 transition duration-150 cursor-pointer shadow-sm"
                >
                    <p class="font-bold text-gray-800">{{ room.room }} - {{ room.building }}</p>
                    <p class="text-xs text-gray-600">{{ room.college }} | Capacity: {{ room.capacity }}</p>
                </div>
            </div>
        </div>

        <div v-else class="flex-1 flex items-center justify-center text-center text-gray-500 p-6">
            <p>Click on a room in the main table to view its details and schedule here, or use the search bar above to filter the list.</p>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Define Props
const props = defineProps({
    /**
     * The currently selected room object to display detailed information for.
     * Is null if no room is selected.
     */
    roomData: {
        type: Object,
        default: null
    },
    /**
     * The full list of all available rooms. Used for the internal sidebar search functionality.
     */
    allRooms: {
        type: Array,
        required: true
    }
});

// Define Emits
const emit = defineEmits(['close-search', 'select-room']);

// --- Local State ---
/**
 * Local reactive state to hold the value of the search bar input.
 */
const localSearchQuery = ref('');

// --- Computed Property ---
/**
 * Filters the `allRooms` prop based on the `localSearchQuery`.
 * Search criteria include Room ID, Room Name, Building, College, Room Type, and Equipment Name.
 */
const filteredRoomsForSidebar = computed(() => {
    const query = localSearchQuery.value.toLowerCase().trim();
    if (!query) {
        return []; // Return empty array if the query is empty
    }

    return props.allRooms.filter(room => {
        // 1. Basic field matching
        const basicMatch = (
            String(room.id || '').includes(query) || // Ensure id is safely converted to string
            (room.room && room.room.toLowerCase().includes(query)) ||
            (room.building && room.building.toLowerCase().includes(query)) ||
            (room.college && room.college.toLowerCase().includes(query)) ||
            (room.roomType && room.roomType.toLowerCase().includes(query))
        );

        // 2. Equipment matching (checks if any equipment name includes the query)
        const equipmentMatch = (
            Array.isArray(room.equipments) && room.equipments.some(equipment => 
                equipment.name.toLowerCase().includes(query)
            )
        );

        return basicMatch || equipmentMatch;
    });
});
</script>