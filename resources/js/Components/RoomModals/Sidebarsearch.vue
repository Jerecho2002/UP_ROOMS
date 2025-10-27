<template>
    <div class="h-full p-4 border-l border-gray-200 flex flex-col">

        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Room Search & Details</h3>
            <button @click="$emit('close-search')" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="relative mb-6">
            <input
                type="text"
                placeholder="Search for a room..."
                v-model="localSearchQuery"
                class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:border-green-500 transition duration-150"
            >
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <div v-if="roomData" class="flex flex-col flex-1 overflow-y-auto">
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500 mb-1">Room name</p>
                <p class="text-2xl font-bold text-green-600">{{ roomData.room }}</p>
            </div>

            <div class="mb-6 space-y-2">
                <h4 class="text-sm font-medium text-gray-500 border-b pb-1 mb-2">Room Details:</h4>
                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <span class="font-semibold text-gray-600">ID:</span> <span class="text-gray-900 font-mono">{{ roomData.id }}</span>
                    <span class="font-semibold text-gray-600">Capacity:</span> <span class="text-gray-900 font-bold">{{ roomData.capacity }}</span>
                    <span class="font-semibold text-gray-600">College:</span> <span class="text-gray-900">{{ roomData.college }}</span>
                    <span class="font-semibold text-gray-600">Type:</span> <span class="text-gray-900">{{ roomData.roomType }}</span>
                    <span class="font-semibold text-gray-600">Building:</span> <span class="text-gray-900">{{ roomData.building }}</span>
                    <span class="font-semibold text-gray-600">Floor:</span> <span class="text-gray-900">{{ roomData.floorNumber || 'N/A' }}</span>
                    <span class="font-semibold text-gray-600 col-span-2">Location:</span> <span class="text-gray-900 col-span-2">{{ roomData.location }}</span>
                    <span class="font-semibold text-gray-600 col-span-2">Description:</span> <span class="text-gray-900 col-span-2">{{ roomData.description }}</span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <h4 class="text-sm font-medium text-gray-500 mb-2 border-b pb-1">Schedules ({{ roomData.schedules.length || 0 }})</h4>
                <div class="space-y-2 text-sm">
                    <div v-if="!roomData.schedules || roomData.schedules.length === 0" class="p-2 text-center text-gray-500 italic">No schedules found. Room is likely Available.</div>
                    <div v-for="(schedule, index) in roomData.schedules" :key="index" :class="{'bg-green-50': schedule.isAvailable, 'bg-red-50': !schedule.isAvailable, 'text-green-700': schedule.isAvailable, 'text-red-700': !schedule.isAvailable}" class="p-2 border rounded-lg">
                        <p class="font-semibold">{{ schedule.name }}</p>
                        <p class="text-xs text-gray-600">{{ schedule.time }} <span class="font-medium text-gray-400">|</span> {{ schedule.college }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="localSearchQuery.trim()" class="flex-1 overflow-y-auto">
            <h4 class="text-sm font-medium text-gray-500 mb-2 border-b pb-1">Search Results ({{ filteredRoomsForSidebar.length }})</h4>
            <div class="space-y-2 text-sm">
                <div v-if="filteredRoomsForSidebar.length === 0" class="p-2 text-center text-gray-500 italic">No rooms match your search in the list.</div>
                <div
                    v-for="room in filteredRoomsForSidebar"
                    :key="room.id"
                    @click="$emit('select-room', room)"
                    class="p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition duration-150 cursor-pointer"
                >
                    <p class="font-bold text-gray-800">{{ room.room }} - {{ room.building }}</p>
                    <p class="text-xs text-gray-600">{{ room.college }} | Capacity: {{ room.capacity }}</p>
                </div>
            </div>
        </div>

        <div v-else class="flex-1 flex items-center justify-center text-center text-gray-500 p-6">
            <p>Click on a room in the main table to view its details and schedule here, or use the search bar above.</p>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

/**
 * SidebarSearch.vue
 * This component displays the details and schedule for a selected room
 * and includes a search function to select a different room.
 */
const props = defineProps({
    roomData: {
        type: Object,
        default: null // The currently selected room to display details for
    },
    allRooms: {
        type: Array,
        required: true // The full list of rooms for the internal search
    }
});

const emit = defineEmits(['close-search', 'select-room']);

// Local state for the search bar inside the sidebar
const localSearchQuery = ref('');

/**
 * Computed property to filter the room list for the sidebar search results.
 */
const filteredRoomsForSidebar = computed(() => {
    const query = localSearchQuery.value.toLowerCase().trim();
    if (!query) {
        // If there's no query, don't show all rooms in the search section,
        // focus on the selected room or prompt
        return [];
    }

    return props.allRooms.filter(room =>
        // Search by ID, Room, Building, College, or Room Type
        String(room.id).includes(query) ||
        room.room.toLowerCase().includes(query) ||
        room.building.toLowerCase().includes(query) ||
        room.college.toLowerCase().includes(query) ||
        room.roomType.toLowerCase().includes(query)
    );
});
</script>