<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false
    }
});

// Emits 'close' and 'save' (sends new room data to the parent/controller)
const emit = defineEmits(['close', 'save']);

// Initial state for the new room form
const newRoom = ref({
    room: '',
    building: '',
    college: '',
    capacity: null,
    location: '',
    roomType: '',
    description: '', // Added for completeness in layout
    schedules: []
});

// State for the temporary schedule being added
const tempSchedule = ref({
    name: '',
    time: '',
    college: '',
    isAvailable: false
});

// Function to reset the form to its initial empty state
const resetForm = () => {
    newRoom.value = {
        room: '',
        building: '',
        college: '',
        capacity: null,
        location: '',
        roomType: '',
        description: '', // Added for completeness in layout
        schedules: [] // Reset schedules as well
    };
    resetTempSchedule();
};

const resetTempSchedule = () => {
    tempSchedule.value = {
        name: '',
        time: '',
        college: '',
        isAvailable: false
    };
}

// Function to add a schedule item to the new room's schedules list
const addSchedule = () => {
    if (tempSchedule.value.name && tempSchedule.value.time) {
        // Automatically set college for the schedule based on the room's college, if not provided
        const college = tempSchedule.value.college || newRoom.value.college || 'N/A';
        newRoom.value.schedules.push({ ...tempSchedule.value, college: college });
        resetTempSchedule();
    } else {
        alert('Please fill out the Schedule Name and Time.');
    }
}

// Function to remove a schedule item
const removeSchedule = (index) => {
    newRoom.value.schedules.splice(index, 1);
}

// Watch for when the modal opens to ensure the form is reset
watch(() => props.isVisible, (newVal) => {
    if (newVal) {
        resetForm();
    }
});

const handleSubmit = () => {
    // Basic validation
    if (!newRoom.value.room || !newRoom.value.capacity || newRoom.value.capacity <= 0) {
        alert('Please fill out the Room name and ensure Capacity is a positive number.');
        return;
    }

    // Emit the new room data up to the parent component
    emit('save', { ...newRoom.value });

    // Reset and close the modal after submission
    resetForm();
    emit('close');
};

const handleClose = () => {
    // Reset form on close
    resetForm();
    emit('close');
}
</script>

<template>
    <transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50" @click.self="handleClose">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6 transform transition-all duration-300 scale-100 opacity-100 max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center border-b pb-3 mb-4 sticky top-0 bg-white z-[70]">
                    <h3 class="text-2xl font-semibold text-gray-800">Add New Room ➕</h3>
                    <button @click="handleClose" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label for="room" class="block text-sm font-medium text-gray-700">Room Name <span class="text-red-500">*</span></label>
                        <input type="text" id="room" v-model="newRoom.room" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                    </div>

                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="building" class="block text-sm font-medium text-gray-700">Building</label>
                            <input type="text" id="building" v-model="newRoom.building"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                        </div>
                        <div class="flex-1">
                            <label for="college" class="block text-sm font-medium text-gray-700">College</label>
                            <input type="text" id="college" v-model="newRoom.college"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity <span class="text-red-500">*</span></label>
                            <input type="number" id="capacity" v-model.number="newRoom.capacity" required min="1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                        </div>
                        <div class="flex-1">
                            <label for="roomType" class="block text-sm font-medium text-gray-700">Room Type</label>
                            <select id="roomType" v-model="newRoom.roomType"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                                <option value="">Select Type</option>
                                <option value="Lecture Hall">Lecture Hall</option>
                                <option value="Classroom">Classroom</option>
                                <option value="Computer Lab">Computer Lab</option>
                                <option value="Science Lab">Science Lab</option>
                                <option value="Conference Room">Conference Room</option>
                                <option value="Study Area">Study Area</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" id="location" v-model="newRoom.location"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input type="text" id="description" v-model="newRoom.description"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2">
                    </div>

                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Room Schedules 🗓️</h4>

                        <div class="space-y-3 p-3 border border-dashed rounded-lg bg-gray-50">
                            <div class="flex space-x-2">
                                <input type="text" placeholder="Schedule Name (e.g., Math 101-A)" v-model="tempSchedule.name"
                                    class="flex-1 border-gray-300 rounded-md shadow-sm sm:text-sm p-2">
                                <input type="text" placeholder="Time (e.g., 9:00AM - 12:00PM MWF)" v-model="tempSchedule.time"
                                    class="flex-1 border-gray-300 rounded-md shadow-sm sm:text-sm p-2">
                            </div>
                            <div class="flex space-x-2 items-center">
                                <input type="text" placeholder="College (Optional)" v-model="tempSchedule.college"
                                    class="w-1/3 border-gray-300 rounded-md shadow-sm sm:text-sm p-2">
                                <label class="flex items-center space-x-2 w-1/3">
                                    <input type="checkbox" v-model="tempSchedule.isAvailable"
                                        class="text-green-600 focus:ring-green-500 rounded border-gray-300">
                                    <span class="text-sm text-gray-700">Mark as Available Slot</span>
                                </label>
                                <button type="button" @click="addSchedule"
                                        class="w-1/3 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-md transition duration-150 text-sm">
                                    Add Schedule
                                </button>
                            </div>
                        </div>

                        <div v-if="newRoom.schedules.length > 0" class="mt-4 space-y-2 max-h-40 overflow-y-auto">
                            <div v-for="(schedule, index) in newRoom.schedules" :key="index"
                                 class="flex items-center justify-between p-2 text-sm rounded-md"
                                 :class="schedule.isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                <span>
                                    <strong>{{ schedule.name }}</strong> ({{ schedule.time }})
                                    <span v-if="schedule.college">| {{ schedule.college }}</span>
                                    <span v-if="schedule.isAvailable" class="ml-2 px-2 py-0.5 text-xs font-semibold bg-green-500 text-white rounded-full">AVAILABLE</span>
                                </span>
                                <button type="button" @click="removeSchedule(index)"
                                        class="text-red-500 hover:text-red-700 ml-3 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div v-else class="mt-4 text-center text-gray-500 text-sm italic">No schedules added yet.</div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="handleClose"
                                class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150">
                            Create Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
    opacity: 0;
}
</style>