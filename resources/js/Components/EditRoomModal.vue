<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'; // Ensure 'watch' is imported

const props = defineProps({
    // Controls whether the modal is visible
    isVisible: {
        type: Boolean,
        required: true,
    },
    // The data for the room being edited (passed from the main component)
    roomData: {
        type: Object,
        default: () => ({}),
    }
});

// Events this component can emit
const emit = defineEmits(['close', 'save', 'reset', 'upload']);

// LOCAL STATE: A reactive copy of the roomData to be bound to the form inputs
const editableRoom = ref({});

// LOGICAL FIX: Use 'watch' to update the local editableRoom state whenever the parent passes a new room (via props.roomData)
watch(() => props.roomData, (newRoomData) => {
    // Deep copy ensures we don't mutate the data in the parent component's roomList directly
    editableRoom.value = { ...newRoomData }; 
}, { deep: true, immediate: true });


// --- Action Handlers ---

const handleSave = () => {
    // Emit the updated data back to the parent component (handleRoomUpdate in the main file)
    emit('save', editableRoom.value);
    emit('close'); // Close modal after successful save/update
};

const handleReset = () => {
    // Revert changes by copying the original roomData again
    editableRoom.value = { ...props.roomData };
    console.log("Form reset.");
    // Optionally emit a reset event if needed
    // emit('reset');
};

const handleUpload = () => {
    // Placeholder for actual file selection/upload logic
    console.log("Upload/Photo clicked. (Trigger file dialog)");
    emit('upload');
};

</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden max-w-4xl w-full mx-auto my-auto h-auto">
            
            <header class="bg-maroon-dark text-white p-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.467 9.5 3.5 7.5 3.5m4.5 2.753C13.168 5.467 14.5 3.5 16.5 3.5m-4.5 2.753v13m-4.5-5.5h9"></path></svg>
                    <h2 class="text-xl font-semibold">EDIT ROOM</h2>
                </div>
                <button @click="$emit('close')" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </header>

            <div class="p-8 bg-gray-100">
                <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-8">
                    
                    <div class="flex-1 space-y-4 text-lg font-medium text-gray-700">
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">ROOM:</span>
                            <input v-model="editableRoom.room" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Building:</span>
                            <input v-model="editableRoom.building" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">College:</span>
                            <input v-model="editableRoom.college" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Capacity:</span>
                            <input v-model.number="editableRoom.capacity" type="number" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Location:</span>
                            <input v-model="editableRoom.location" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Room Type:</span>
                            <input v-model="editableRoom.roomType" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1" />
                        </div>
                    </div>

                    <div class="flex-shrink-0 w-full lg:w-64 flex flex-col items-center space-y-3">
                        <div class="w-full h-48 bg-gray-300 rounded-lg overflow-hidden flex justify-center items-center">
                            <img 
                                src="https://via.placeholder.com/256x192.png?text=Room+Photo" 
                                alt="Room Photo" 
                                class="object-cover w-full h-full"
                            >
                        </div>
                        <span class="text-gray-600 font-semibold">Current Photo</span>
                    </div>
                </div>

                <div class="flex justify-center space-x-4 mt-8">
                    <button @click="handleSave" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                        SAVE
                    </button>
                    <button @click="handleReset" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                        RESET
                    </button>
                    <button @click="handleUpload" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                        UPLOAD / PHOTO
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-maroon-dark { 
    background-color: #7B0025; 
}
input[type="text"], input[type="number"] {
    /* Ensure border bottom is styled */
    border-bottom-width: 2px;
}
</style>