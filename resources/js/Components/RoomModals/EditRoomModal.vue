<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden max-w-4xl w-full mx-auto my-auto max-h-[95vh]">

            <header class="bg-[#7A0C23] text-white p-3 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.467 9.5 3.5 7.5 3.5m4.5 2.753C13.168 5.467 14.5 3.5 16.5 3.5m-4.5 2.753v13m-4.5-5.5h9"></path></svg>
                    <h2 class="text-xl font-semibold">EDIT ROOM: {{ editableRoom.room_name || editableRoom.room_code }}</h2>
                </div>
                <button @click="handleClose" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </header>

            <div class="p-8 bg-gray-100 overflow-y-auto max-h-[85vh]">
                <form @submit.prevent="handleSave" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Room Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room Code *</label>
                                <input v-model="editableRoom.room_code" type="text" required
                                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                            </div>

                            <!-- Room Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room Name *</label>
                                <input v-model="editableRoom.room_name" type="text" required
                                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                            </div>

                            <!-- Building -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Building</label>
                                <select v-model="editableRoom.building_id"
                                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                                    <option value="">Select Building</option>
                                    <option v-for="building in buildings" :key="building.id" :value="building.id">
                                        {{ building.building_name }}
                                    </option>
                                </select>
                            </div>

                            <!-- College -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">College</label>
                                <select v-model="editableRoom.college_id"
                                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                                    <option value="">Select College</option>
                                    <option v-for="college in colleges" :key="college.id" :value="college.id">
                                        {{ college.college_name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Department -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select v-model="editableRoom.department_id"
                                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                                    <option value="">Select Department</option>
                                    <option v-for="department in departments" :key="department.id" :value="department.id">
                                        {{ department.department_name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Room Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                                <select v-model="editableRoom.room_type_id"
                                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                                    <option value="">Select Type</option>
                                    <option v-for="type in roomTypes" :key="type.id" :value="type.id">
                                        {{ type.type_name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Capacity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
                                <input v-model.number="editableRoom.capacity" type="number" required min="1"
                                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                            </div>

                            <!-- Floor Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Floor Number</label>
                                <input v-model.number="editableRoom.floor_number" type="number"
                                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input v-model="editableRoom.location" type="text"
                                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                            </div>

                            <!-- Area -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Area (sqm)</label>
                                <input v-model.number="editableRoom.area_sqm" type="number"
                                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="editableRoom.status"
                                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <!-- Assigned User -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned User</label>
                                <select v-model="editableRoom.assigned_user_id"
                                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                                    <option value="">Select User</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.first_name }} {{ user.last_name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Full Width Fields -->
                    <div class="space-y-4">
                        <!-- Facilities -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Facilities (comma-separated)</label>
                            <input v-model="facilitiesInput" type="text" placeholder="Projector, Whiteboard, AC, etc."
                                   class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea v-model="editableRoom.notes" rows="3"
                                      class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"></textarea>
                        </div>
                    </div>

                    <!-- Equipment Section -->
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Equipment ⚙️</h4>

                        <div class="space-y-3" v-for="(equipment, index) in editableRoom.equipments" :key="index">
                            <div class="flex gap-2 items-center">
                                <input type="text" v-model="equipment.name" placeholder="Equipment name"
                                       class="flex-1 border rounded p-2">
                                <input type="number" v-model.number="equipment.quantity" placeholder="Qty" min="1"
                                       class="w-20 border rounded p-2">
                                <button type="button" @click="removeEquipment(index)"
                                        class="bg-red-500 text-white px-3 py-2 rounded">
                                    Remove
                                </button>
                            </div>
                        </div>

                        <button type="button" @click="addEquipment"
                                class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            + Add Equipment
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4 mt-8 pt-4 border-t border-gray-300">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                            SAVE CHANGES
                        </button>
                        <button type="button" @click="handleReset" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-8 rounded-lg transition duration-150">
                            RESET FORM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    isVisible: Boolean,
    roomData: Object,
    buildings: Array,
    colleges: Array,
    departments: Array,
    roomTypes: Array,
    users: Array
});

const emit = defineEmits(['close', 'save']);

// Local editable copy
const editableRoom = ref({});
const originalRoomData = ref({});
const facilitiesInput = ref('');

// Watch for roomData changes
watch(() => props.roomData, (newRoomData) => {
    if (newRoomData) {
        editableRoom.value = { ...newRoomData };
        originalRoomData.value = { ...newRoomData };

        // Convert facilities array to string for input
        if (newRoomData.facilities && Array.isArray(newRoomData.facilities)) {
            facilitiesInput.value = newRoomData.facilities.join(', ');
        } else {
            facilitiesInput.value = '';
        }

        // Ensure equipments array exists
        if (!editableRoom.value.equipments) {
            editableRoom.value.equipments = [];
        }
    }
}, { immediate: true });

// Watch facilities input
watch(facilitiesInput, (newValue) => {
    if (newValue.trim()) {
        editableRoom.value.facilities = newValue.split(',').map(item => item.trim()).filter(item => item);
    } else {
        editableRoom.value.facilities = [];
    }
});

const addEquipment = () => {
    editableRoom.value.equipments.push({
        name: '',
        quantity: 1
    });
};

const removeEquipment = (index) => {
    editableRoom.value.equipments.splice(index, 1);
};

const handleSave = () => {
    if (!editableRoom.value.room_code || !editableRoom.value.room_name || !editableRoom.value.capacity) {
        alert('Please fill in all required fields (Room Code, Room Name, and Capacity).');
        return;
    }

    emit('save', editableRoom.value);
};

const handleReset = () => {
    editableRoom.value = { ...originalRoomData.value };

    // Reset facilities input
    if (originalRoomData.value.facilities && Array.isArray(originalRoomData.value.facilities)) {
        facilitiesInput.value = originalRoomData.value.facilities.join(', ');
    } else {
        facilitiesInput.value = '';
    }
};

const handleClose = () => {
    emit('close');
};
</script>
