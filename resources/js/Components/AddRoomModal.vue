<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'save']);

// Initial state for the new room form
const newRoom = ref({
    room: '',
    building: '',
    college: '',
    capacity: null, // Use null for number inputs initially
    location: '',
    roomType: ''
});

// Function to reset the form to its initial empty state
const resetForm = () => {
    newRoom.value = {
        room: '',
        building: '',
        college: '',
        capacity: null,
        location: '',
        roomType: ''
    };
};

// Watch for when the modal opens to ensure the form is reset
watch(() => props.isVisible, (newVal) => {
    if (newVal) {
        resetForm();
    }
});

const handleSubmit = () => {
    // Basic validation (you can expand this)
    if (!newRoom.value.room || !newRoom.value.capacity) {
        alert('Please fill out at least the Room name and Capacity.');
        return;
    }

    // Emit the new room data for the parent component to handle saving and ID assignment
    emit('save', { ...newRoom.value });

    // Reset and close the modal after successful submission
    resetForm();
    emit('close');
};

const handleClose = () => {
    // Optionally reset form on close, or keep for user convenience
    resetForm(); 
    emit('close');
}
</script>

<template>
    <transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50" @click.self="handleClose">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6 transform transition-all duration-300 scale-100 opacity-100">
                
                <div class="flex justify-between items-center border-b pb-3 mb-4">
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

                    <div class="flex justify-end pt-4 space-x-3">
                        <button type="button" @click="handleClose"
                                class="py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                                class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                            Save Room
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </transition>
</template>

<style scoped>
/* Optional: Basic fade transition for a smoother user experience */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>