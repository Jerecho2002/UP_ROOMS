<script setup>
import { ref, watch, computed } from 'vue';
import { InformationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'; // Assuming you have Heroicons installed or similar

const props = defineProps({
    isVisible: {
        type: Boolean,
        required: true,
    },
    // The list of all rooms from the parent component
    rooms: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'room-added']);

// State for the currently selected room in the viewer
const selectedRoom = ref(null);

// When the modal opens, select the first room by default
watch(() => props.isVisible, (newVal) => {
    if (newVal && props.rooms.length > 0) {
        // Automatically select the first room if none is selected
        if (!selectedRoom.value || !props.rooms.some(r => r.id === selectedRoom.value.id)) {
            selectedRoom.value = props.rooms[0];
        }
    } else if (!newVal) {
        // Optionally clear selection when modal closes
        // selectedRoom.value = null;
    }
});

/**
 * Helper to get status based on schedules
 */
const getRoomStatus = (room) => {
    if (!room.schedules || room.schedules.length === 0) {
        return { text: 'Available', class: 'bg-green-100 text-green-800' };
    }
    // Simple check: if there is any active schedule, it is occupied.
    const isOccupied = room.schedules.some(s => !s.isAvailable); 
    return isOccupied 
        ? { text: 'Occupied', class: 'bg-red-100 text-red-800' }
        : { text: 'Available Slot', class: 'bg-yellow-100 text-yellow-800' };
};

/* ------------------------------------------------------------------- */
/* --- Mock "Add Room" Logic for Demonstration --- */
/* ------------------------------------------------------------------- */

const showMockAddForm = ref(false);
const mockNewRoomData = ref({
    room: '',
    building: '',
    college: '',
    capacity: 0,
    location: '',
    roomType: '',
    description: '',
    department: '',
    floorNumber: 1,
});

const saveNewRoom = () => {
    if (!mockNewRoomData.value.room || !mockNewRoomData.value.building) return;

    emit('room-added', { ...mockNewRoomData.value });
    
    // Reset form and close it
    mockNewRoomData.value = { room: '', building: '', college: '', capacity: 0, location: '', roomType: '', description: '', department: '', floorNumber: 1 };
    showMockAddForm.value = false;
};

</script>

<template>
    <Transition name="modal">
        <div v-if="isVisible" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900 bg-opacity-70 backdrop-blur-sm" @click.self="$emit('close')">
            
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-7xl h-[90vh] flex flex-col transform transition-all duration-300 scale-100" @click.stop>
                
                <div class="flex justify-between items-center p-5 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                        <InformationCircleIcon class="w-6 h-6 mr-2 text-blue-600" />
                        Room Inventory Viewer
                    </h3>
                    <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                        <XMarkIcon class="w-6 h-6" />
                    </button>
                </div>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 overflow-hidden">
                    
                    <div class="md:col-span-1 border-r border-gray-200 bg-gray-50 overflow-y-auto">
                        <div class="p-4">
                            <h4 class="text-lg font-semibold text-gray-700 mb-3">All Rooms ({{ rooms.length }})</h4>
                            
                            <ul class="space-y-2">
                                <li v-for="room in rooms" :key="room.id" 
                                    @click="selectedRoom = room" 
                                    :class="{ 'bg-blue-100 border-blue-500': selectedRoom && selectedRoom.id === room.id, 'hover:bg-gray-100 border-gray-200': selectedRoom && selectedRoom.id !== room.id }"
                                    class="p-3 border-l-4 cursor-pointer rounded-lg transition duration-150 shadow-sm"
                                >
                                    <p class="font-bold text-gray-900">{{ room.room }} (ID: {{ room.id }})</p>
                                    <p class="text-sm text-gray-600">{{ room.building }} - {{ room.college }}</p>
                                </li>
                            </ul>
                            
                            <button @click="showMockAddForm = !showMockAddForm" 
                                class="w-full mt-4 py-2 text-sm rounded-lg transition duration-150"
                                :class="{'bg-green-600 hover:bg-green-700 text-white': !showMockAddForm, 'bg-gray-400 hover:bg-gray-500 text-white': showMockAddForm}">
                                {{ showMockAddForm ? 'Cancel Add' : 'Add New Room' }}
                            </button>
                        </div>
                    </div>
                    
                    <div class="md:col-span-3 p-6 overflow-y-auto">
                        
                        <div v-if="showMockAddForm" class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
                            <h4 class="text-xl font-bold text-yellow-800 mb-4">Add New Room</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <input v-model="mockNewRoomData.room" placeholder="Room Name (e.g., A101)" class="p-2 border rounded" required />
                                <input v-model="mockNewRoomData.building" placeholder="Building" class="p-2 border rounded" required />
                                <input v-model.number="mockNewRoomData.capacity" type="number" placeholder="Capacity" class="p-2 border rounded" required />
                                <input v-model="mockNewRoomData.roomType" placeholder="Room Type" class="p-2 border rounded" />
                                <input v-model="mockNewRoomData.college" placeholder="College" class="p-2 border rounded" />
                                <input v-model.number="mockNewRoomData.floorNumber" type="number" placeholder="Floor Number" class="p-2 border rounded" />
                            </div>
                            <textarea v-model="mockNewRoomData.description" placeholder="Description" rows="2" class="w-full mt-4 p-2 border rounded"></textarea>
                            <button @click="saveNewRoom" :disabled="!mockNewRoomData.room || !mockNewRoomData.building"
                                class="mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-150 disabled:opacity-50">
                                Save New Room
                            </button>
                        </div>


                        <div v-else-if="selectedRoom">
                            <h4 class="text-3xl font-extrabold text-gray-900 mb-4">{{ selectedRoom.room }}</h4>
                            
                            <span :class="getRoomStatus(selectedRoom).class" class="inline-block px-3 py-1 text-sm font-semibold rounded-full mb-4">
                                {{ getRoomStatus(selectedRoom).text }}
                            </span>

                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                                <div class="col-span-1">
                                    <dt class="font-medium text-gray-500">Building / Floor</dt>
                                    <dd class="mt-1 text-gray-900">{{ selectedRoom.building }} (Fl. {{ selectedRoom.floorNumber }})</dd>
                                </div>
                                <div class="col-span-1">
                                    <dt class="font-medium text-gray-500">College</dt>
                                    <dd class="mt-1 text-gray-900">{{ selectedRoom.college }}</dd>
                                </div>
                                <div class="col-span-1">
                                    <dt class="font-medium text-gray-500">Capacity</dt>
                                    <dd class="mt-1 text-gray-900 font-bold">{{ selectedRoom.capacity }}</dd>
                                </div>
                                <div class="col-span-1">
                                    <dt class="font-medium text-gray-500">Room Type</dt>
                                    <dd class="mt-1 text-gray-900">{{ selectedRoom.roomType }}</dd>
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <dt class="font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-gray-900">{{ selectedRoom.description || 'No description available.' }}</dd>
                                </div>
                                <div class="col-span-1 sm:col-span-2 mt-4 pt-4 border-t border-gray-100">
                                    <dt class="font-bold text-gray-700 mb-2">Current Schedules ({{ selectedRoom.schedules?.length || 0 }})</dt>
                                    <dd class="space-y-2">
                                        <div v-if="!selectedRoom.schedules || selectedRoom.schedules.length === 0" class="text-gray-500 italic">
                                            No classes currently scheduled.
                                        </div>
                                        <div v-for="(schedule, index) in selectedRoom.schedules" :key="index" 
                                            class="p-2 rounded-lg"
                                            :class="schedule.isAvailable ? 'bg-green-50' : 'bg-red-50'">
                                            <p class="font-semibold">{{ schedule.name }}</p>
                                            <p class="text-xs text-gray-600">{{ schedule.time }} ({{ schedule.college }})</p>
                                        </div>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div v-else-if="!selectedRoom && rooms.length > 0" class="text-center p-10 text-gray-500">
                            Select a room from the list on the left to view details.
                        </div>
                        <div v-else class="text-center p-10 text-gray-500">
                            No rooms are available in the system. Use the "Add New Room" button to create one.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Modal Transition Styles */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Ensure the modal content scales up on enter */
.modal-enter-from .scale-100,
.modal-leave-to .scale-100 {
    transform: scale(0.95);
    opacity: 0;
}
</style>