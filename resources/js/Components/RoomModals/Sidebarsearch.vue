<template>
    <div class="h-full p-4 border-l border-gray-200 flex flex-col bg-white shadow-xl">
        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Room Details</h3>
            <button @click="$emit('close-search')" class="text-gray-500 hover:text-red-600 transition duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="relative mb-6">
            <input
                type="text"
                placeholder="Search rooms..."
                v-model="localSearchQuery"
                class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 shadow-sm transition duration-150"
            >
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <!-- Room Details View -->
        <div v-if="roomData && !localSearchQuery.trim()" class="flex flex-col flex-1 overflow-y-auto">
            <!-- Room Header -->
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-500 mb-1">Room</p>
                <p class="text-2xl font-bold text-green-700">{{ roomData.room_name }}</p>
                <p class="text-sm text-gray-600">{{ roomData.room_code }}</p>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span :class="{
                    'bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium': roomData.status === 'available',
                    'bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium': roomData.status === 'occupied',
                    'bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium': roomData.status === 'maintenance',
                    'bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium': roomData.status === 'closed'
                }">
                    {{ roomData.status?.toUpperCase() || 'UNKNOWN' }}
                </span>
            </div>

            <!-- Room Details -->
            <div class="mb-6 space-y-4">
                <h4 class="text-md font-bold text-gray-700 border-b pb-2">Room Information</h4>

                <div class="grid grid-cols-2 gap-y-3 text-sm">
                    <div>
                        <span class="font-semibold text-gray-600 block">Building:</span>
                        <span class="text-gray-900">{{ roomData.building?.building_name || 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600 block">College:</span>
                        <span class="text-gray-900">{{ roomData.college?.college_name || 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600 block">Department:</span>
                        <span class="text-gray-900">{{ roomData.department?.department_name || 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600 block">Type:</span>
                        <span class="text-gray-900">{{ roomData.room_type?.type_name || 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600 block">Capacity:</span>
                        <span class="text-gray-900 font-bold">{{ roomData.capacity || 0 }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600 block">Floor:</span>
                        <span class="text-gray-900">{{ roomData.floor_number || 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="font-semibold text-gray-600 block">Location:</span>
                        <span class="text-gray-900">{{ roomData.location || 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="font-semibold text-gray-600 block">Area:</span>
                        <span class="text-gray-900">{{ roomData.area_sqm ? roomData.area_sqm + ' sqm' : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Facilities -->
            <div class="mb-6 space-y-2">
                <h4 class="text-md font-bold text-gray-700 border-b pb-2">Facilities</h4>
                <div v-if="roomData.facilities && roomData.facilities.length > 0" class="flex flex-wrap gap-2">
                    <span v-for="(facility, index) in roomData.facilities" :key="index"
                          class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                        {{ facility }}
                    </span>
                </div>
                <span v-else class="text-gray-500 italic text-sm">No facilities listed.</span>
            </div>

            <!-- Equipment -->
            <div class="mb-6 space-y-2">
                <h4 class="text-md font-bold text-gray-700 border-b pb-2">Equipment</h4>
                <div v-if="roomData.equipment && roomData.equipment.length > 0" class="space-y-2">
                    <div v-for="(item, index) in roomData.equipment" :key="index"
                         class="flex justify-between items-center p-2 bg-purple-50 rounded-lg">
                        <span class="font-medium text-purple-800">{{ item.name }}</span>
                        <span class="bg-purple-200 text-purple-900 px-2 py-1 rounded text-xs font-bold">
                            {{ item.quantity }} pcs
                        </span>
                    </div>
                </div>
                <span v-else class="text-gray-500 italic text-sm">No equipment listed.</span>
            </div>

            <!-- Notes -->
            <div class="mb-6 space-y-2">
                <h4 class="text-md font-bold text-gray-700 border-b pb-2">Notes</h4>
                <p class="text-sm text-gray-700 italic">{{ roomData.notes || 'No notes available.' }}</p>
            </div>

            <!-- Schedules -->
            <div class="flex-1 overflow-y-auto">
                <h4 class="text-md font-bold text-gray-700 mb-3 border-b pb-2">Schedules</h4>
                <div v-if="roomData.schedules && roomData.schedules.length > 0" class="space-y-2">
                    <div v-for="schedule in roomData.schedules" :key="schedule.id"
                         class="p-3 border rounded-lg bg-gray-50">
                        <p class="font-semibold text-gray-800">{{ schedule.subject_name || schedule.title }}</p>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ formatDate(schedule.start_time) }} - {{ formatDate(schedule.end_time) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">{{ schedule.faculty?.name || 'No faculty assigned' }}</p>
                    </div>
                </div>
                <div v-else class="p-4 text-center text-gray-500 italic bg-gray-50 rounded-lg">
                    No scheduled activities
                </div>
            </div>
        </div>

        <!-- Search Results View -->
        <div v-else-if="localSearchQuery.trim()" class="flex-1 overflow-y-auto">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Search Results ({{ filteredRoomsForSidebar.length }})</h4>
            <div class="space-y-2">
                <div v-if="filteredRoomsForSidebar.length === 0"
                     class="p-4 text-center text-gray-500 italic bg-gray-50 rounded-lg">
                    No rooms match your search
                </div>

                <div v-for="room in filteredRoomsForSidebar" :key="room.id"
                     @click="selectRoom(room)"
                     class="p-3 border rounded-lg bg-white hover:bg-green-50 border-gray-200 hover:border-green-300 transition duration-150 cursor-pointer shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-gray-800">{{ room.room_name }}</p>
                            <p class="text-sm text-gray-600">{{ room.room_code }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ room.building?.building_name || 'No building' }} • Capacity: {{ room.capacity }}
                            </p>
                        </div>
                        <span :class="{
                            'bg-green-100 text-green-800 text-xs px-2 py-1 rounded': room.status === 'available',
                            'bg-red-100 text-red-800 text-xs px-2 py-1 rounded': room.status === 'occupied',
                            'bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded': room.status === 'maintenance',
                            'bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded': room.status === 'closed'
                        }">
                            {{ room.status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Room Selected View -->
        <div v-else class="flex-1 flex items-center justify-center text-center text-gray-500 p-6">
            <div>
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="text-sm">Click on a room in the main table to view details, or use the search bar above to find rooms.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    roomData: {
        type: Object,
        default: null
    },
    allRooms: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close-search', 'select-room']);

const localSearchQuery = ref('');

// Filter rooms for sidebar search
const filteredRoomsForSidebar = computed(() => {
    const query = localSearchQuery.value.toLowerCase().trim();
    if (!query) return [];

    return props.allRooms.filter(room => {
        return (
            String(room.id).includes(query) ||
            (room.room_code && room.room_code.toLowerCase().includes(query)) ||
            (room.room_name && room.room_name.toLowerCase().includes(query)) ||
            (room.building?.building_name && room.building.building_name.toLowerCase().includes(query)) ||
            (room.location && room.location.toLowerCase().includes(query))
        );
    });
});

const selectRoom = (room) => {
    emit('select-room', room);
    localSearchQuery.value = '';
};

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>
