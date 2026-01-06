<template>
    <transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50" @click.self="handleClose">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">
                <!-- HEADER -->
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">Add New Room ➕</h3>
                    <button @click="handleClose" class="text-gray-400 hover:text-gray-600 transition" title="Close Modal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- FORM CONTENT -->
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <!-- Basic Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Room Name *</label>
                        <input type="text" v-model="newRoom.room_name" required class="mt-1 block w-full border-gray-300 rounded-md p-2 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Room Code *</label>
                        <input type="text" v-model="newRoom.room_code" required class="mt-1 block w-full border-gray-300 rounded-md p-2 shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Building</label>
                            <select v-model="newRoom.building_id" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                <option value="">Select Building</option>
                                <option v-for="building in buildings" :key="building.id" :value="building.id">
                                    {{ building.building_name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">College</label>
                            <select v-model="newRoom.college_id" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                <option value="">Select College</option>
                                <option v-for="college in colleges" :key="college.id" :value="college.id">
                                    {{ college.college_name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <select v-model="newRoom.department_id" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                <option value="">Select Department</option>
                                <option v-for="department in departments" :key="department.id" :value="department.id">
                                    {{ department.department_name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Room Type</label>
                            <select v-model="newRoom.room_type_id" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                <option value="">Select Type</option>
                                <option v-for="type in roomTypes" :key="type.id" :value="type.id">
                                    {{ type.type_name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Floor Number</label>
                            <input type="number" v-model.number="newRoom.floor_number" min="0" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacity *</label>
                            <input type="number" v-model.number="newRoom.capacity" required min="1" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" v-model="newRoom.location" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="newRoom.description" rows="2" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select v-model="newRoom.status" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assigned User</label>
                            <select v-model="newRoom.assigned_user_id" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                <option value="">Select User</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }} ({{ user.user_type }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea v-model="newRoom.notes" rows="2" class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm"></textarea>
                    </div>

                    <!-- Equipment Section -->
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Room Equipments 🛠️</h4>

                        <div class="space-y-3 p-3 border border-dashed rounded-lg bg-gray-50">
                            <div class="flex space-x-2 items-end">
                                <div class="w-2/3">
                                    <label class="block text-xs font-medium text-gray-600">Equipment Name</label>
                                    <input type="text" v-model="tempEquipment.name" placeholder="e.g., Projector, Chair, Table" class="w-full border-gray-300 rounded-md p-2">
                                </div>

                                <div class="w-1/6">
                                    <label class="block text-xs font-medium text-gray-600">Qty</label>
                                    <input type="number" v-model.number="tempEquipment.quantity" min="1" class="w-full border-gray-300 rounded-md p-2 text-center">
                                </div>

                                <button type="button" @click="addEquipment" class="w-1/6 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-md">
                                    + Add
                                </button>
                            </div>
                        </div>

                        <div v-if="newRoom.equipments && newRoom.equipments.length > 0" class="mt-4 space-y-2 max-h-40 overflow-y-auto">
                            <div v-for="(item, index) in newRoom.equipments" :key="index" class="flex items-center justify-between p-2 bg-purple-100 text-purple-800 rounded-md text-sm">
                                <span>
                                    <strong>{{ item.name }}</strong>
                                    <span class="ml-2 px-2 py-0.5 bg-purple-500 text-white rounded-full text-xs font-bold">
                                        {{ item.quantity }} pc(s)
                                    </span>
                                </span>
                                <button type="button" @click="removeEquipment(index)" class="text-red-500 hover:text-red-700">
                                    ✕
                                </button>
                            </div>
                        </div>

                        <div v-else class="text-center text-gray-500 text-sm italic mt-3">
                            No equipment added yet.
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" @click="handleClose" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Create Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        required: true,
    },
    buildings: {
        type: Array,
        default: () => []
    },
    colleges: {
        type: Array,
        default: () => []
    },
    departments: {
        type: Array,
        default: () => []
    },
    roomTypes: {
        type: Array,
        default: () => []
    },
    users: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'save']);

const newRoom = ref({
    room_name: '',
    room_code: '',
    building_id: null,
    college_id: null,
    department_id: null,
    room_type_id: null,
    assigned_user_id: null,
    floor_number: null,
    location: '',
    capacity: 30,
    area_sqm: null,
    description: '',
    status: 'available',
    notes: '',
    equipments: [],
});

const tempEquipment = ref({
    name: '',
    quantity: 1,
});

const addEquipment = () => {
    if (!tempEquipment.value.name) {
        alert('Please enter equipment name.');
        return;
    }
    if (tempEquipment.value.quantity <= 0 || !Number.isInteger(tempEquipment.value.quantity)) {
        alert('Quantity must be a positive whole number.');
        return;
    }

    if (!newRoom.value.equipments) {
        newRoom.value.equipments = [];
    }

    newRoom.value.equipments.push({
        name: tempEquipment.value.name,
        quantity: tempEquipment.value.quantity
    });

    tempEquipment.value = { name: '', quantity: 1 };
};

const removeEquipment = (index) => {
    newRoom.value.equipments.splice(index, 1);
};

watch(() => props.isVisible, (newVal) => {
    if (newVal) {
        resetForm();
    }
});

const resetForm = () => {
    newRoom.value = {
        room_name: '',
        room_code: '',
        building_id: null,
        college_id: null,
        department_id: null,
        room_type_id: null,
        assigned_user_id: null,
        floor_number: null,
        location: '',
        capacity: 30,
        area_sqm: null,
        description: '',
        status: 'available',
        notes: '',
        equipments: [],
    };
    tempEquipment.value = { name: '', quantity: 1 };
};

const handleSubmit = () => {
    if (!newRoom.value.room_name || !newRoom.value.room_code || !newRoom.value.capacity) {
        alert('Please fill out all required fields.');
        return;
    }

    // Format data for submission
    const submitData = {
        ...newRoom.value,
        capacity: Number(newRoom.value.capacity),
        floor_number: newRoom.value.floor_number ? Number(newRoom.value.floor_number) : null,
        equipments: newRoom.value.equipments || []
    };

    emit('save', submitData);
};

const handleClose = () => {
    resetForm();
    emit('close');
};
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
