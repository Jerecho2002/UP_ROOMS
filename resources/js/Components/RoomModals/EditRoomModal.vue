<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
    // Controls whether the modal is visible
    isVisible: {
        type: Boolean,
        required: true,
    },
    // The data for the room being edited (passed from the main component)
    roomData: {
        type: Object,
        default: () => ({ 
            schedules: [], // Ensure schedules property exists for deep watch
        }),
    }
});

// Events this component can emit
const emit = defineEmits(['close', 'save', 'reset', 'upload']);

// LOCAL STATE: A reactive copy of the roomData to be bound to the form inputs
const editableRoom = ref({});

// State for the temporary schedule being added (copied from AddRoomModal)
const tempSchedule = ref({
    name: '',
    time: '',
    college: '',
    isAvailable: false
});

const resetTempSchedule = () => {
    tempSchedule.value = {
        name: '',
        time: '',
        college: '',
        isAvailable: false
    };
}

// Function to add a schedule item to the editable room's schedules list (copied from AddRoomModal)
const addSchedule = () => {
    if (tempSchedule.value.name && tempSchedule.value.time) {
        // Automatically set college for the schedule based on the room's college, if not provided
        const college = tempSchedule.value.college || editableRoom.value.college || 'N/A';
        // Ensure editableRoom.value.schedules is an array
        if (!Array.isArray(editableRoom.value.schedules)) {
             editableRoom.value.schedules = [];
        }
        editableRoom.value.schedules.push({ ...tempSchedule.value, college: college });
        resetTempSchedule();
    } else {
        alert('Please fill out the Schedule Name and Time.');
    }
}

// Function to remove a schedule item (copied from AddRoomModal)
const removeSchedule = (index) => {
    editableRoom.value.schedules.splice(index, 1);
}

// LOGICAL FIX: Use 'watch' to update the local editableRoom state whenever the parent passes a new room (via props.roomData)
watch(() => props.roomData, (newRoomData) => {
    // Deep copy ensures we don't mutate the data in the parent component's roomList directly
    // Also ensures 'schedules' array is present for the template
    editableRoom.value = { ...newRoomData, schedules: newRoomData.schedules ? [...newRoomData.schedules] : [] }; 
    resetTempSchedule();
}, { deep: true, immediate: true });


// --- Action Handlers ---

const handleSave = () => {
    // Emit the updated data back to the parent component (handleRoomUpdate in the main file)
    // Basic validation before saving
    if (!editableRoom.value.room || !editableRoom.value.capacity || editableRoom.value.capacity <= 0) {
        alert('Please fill out the Room name and ensure Capacity is a positive number.');
        return;
    }
    emit('save', editableRoom.value);
    emit('close'); // Close modal after successful save/update
};

const handleReset = () => {
    // Revert changes by copying the original roomData again
    editableRoom.value = { ...props.roomData, schedules: props.roomData.schedules ? [...props.roomData.schedules] : [] };
    resetTempSchedule();
    console.log("Form reset.");
};
const handleClose = () => {
    emit('close');
}

const handleUpload = () => {
    // Placeholder for actual file selection/upload logic
    console.log("Upload/Photo clicked. (Trigger file dialog)");
    emit('upload');
};

</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden max-w-4xl w-full mx-auto my-auto max-h-[95vh]">
            
            <header class="bg-maroon-dark text-white p-3 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.467 9.5 3.5 7.5 3.5m4.5 2.753C13.168 5.467 14.5 3.5 16.5 3.5m-4.5 2.753v13m-4.5-5.5h9"></path></svg>
                    <h2 class="text-xl font-semibold">EDIT ROOM: {{ editableRoom.room }}</h2>
                </div>
                <button @click="handleClose" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </header>

            <div class="p-8 bg-gray-100 overflow-y-auto max-h-[85vh]">
                <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-8">
                    
                    <div class="flex-1 space-y-4 text-lg font-medium text-gray-700">
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">ROOM:</span>
                            <input v-model="editableRoom.room" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" required />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Building:</span>
                            <input v-model="editableRoom.building" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">College:</span>
                            <input v-model="editableRoom.college" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Capacity:</span>
                            <input v-model.number="editableRoom.capacity" type="number" min="1" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" required />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Location:</span>
                            <input v-model="editableRoom.location" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Room Type:</span>
                            <input v-model="editableRoom.roomType" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" />
                        </div>
                        <div class="flex space-x-2 items-center">
                            <span class="w-24 font-bold text-gray-800">Description:</span>
                            <input v-model="editableRoom.description" type="text" class="border-b border-gray-300 bg-transparent focus:outline-none w-full p-1 text-base" />
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
                        <button @click="handleUpload" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg transition duration-150 w-full text-sm">
                            UPLOAD / PHOTO
                        </button>
                    </div>
                </div>

                <div class="border-t pt-4 mt-8">
                    <h4 class="text-xl font-semibold text-gray-800 mb-3">Manage Room Schedules 🗓️</h4>

                    <div class="space-y-3 p-3 border border-dashed rounded-lg bg-white shadow-inner">
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

                    <div v-if="editableRoom.schedules && editableRoom.schedules.length > 0" class="mt-4 space-y-2 max-h-40 overflow-y-auto">
                        <div v-for="(schedule, index) in editableRoom.schedules" :key="index"
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

                <div class="flex justify-center space-x-4 mt-8 pt-4 border-t border-gray-300">
                    <button @click="handleSave" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                        SAVE CHANGES
                    </button>
                    <button @click="handleReset" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                        RESET FORM
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