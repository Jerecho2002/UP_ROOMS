<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'save']);

const equipmentOptions = [
    'Table', 'Chair', 'Computer', 'Keyboard', 'Mouse',
    'Head Set', 'Laptop', 'Projector', 'Monitor', 'Whiteboard'
];

const showSuccessMessage = ref(false);

const newRoom = ref({
    room: '',
    building: '',
    college: '',
    capacity: null,
    location: '',
    roomType: '',
    description: '',
    comment: '',
    equipments: [],
});

const tempEquipment = ref({
    name: '',
    quantity: 1,
});

const resetTempEquipment = () => {
    tempEquipment.value = { name: '', quantity: 1 };
};

const resetForm = () => {
    newRoom.value = {
        room: '',
        building: '',
        college: '',
        capacity: null,
        location: '',
        roomType: '',
        description: '',
        comment: '',
        equipments: [],
    };
    resetTempEquipment();
    showSuccessMessage.value = false;
};

const addEquipment = () => {
    if (!tempEquipment.value.name) {
        alert('Please select an Equipment Item.');
        return;
    }
    if (tempEquipment.value.quantity <= 0 || !Number.isInteger(tempEquipment.value.quantity)) {
        alert('Quantity must be a positive whole number.');
        return;
    }

    const existingIndex = newRoom.value.equipments.findIndex(
        item => item.name === tempEquipment.value.name
    );

    if (existingIndex !== -1) {
        alert(`Equipment "${tempEquipment.value.name}" is already listed.`);
    } else {
        newRoom.value.equipments.push({
            name: tempEquipment.value.name,
            quantity: tempEquipment.value.quantity
        });
        resetTempEquipment();
    }
};

const removeEquipment = (index) => {
    newRoom.value.equipments.splice(index, 1);
};

watch(() => props.isVisible, (newVal) => {
    if (newVal) {
        resetForm();
    }
});

const handleSubmit = () => {
    if (!newRoom.value.room || !newRoom.value.capacity || newRoom.value.capacity <= 0) {
        alert('Please fill out the Room name and ensure Capacity is a positive number.');
        return;
    }

    const roomData = {
        ...newRoom.value,
        capacity: Number(newRoom.value.capacity)
    };

    emit('save', roomData);

    showSuccessMessage.value = true;

    setTimeout(() => {
        resetForm();
        emit('close');
    }, 1500);
};

const handleClose = () => {
    resetForm();
    emit('close');
};
</script>

<template>
    <transition name="modal-fade">
        <div
            v-if="isVisible"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50"
            @click.self="handleClose"
        >
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto">

                <!-- HEADER -->
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">Add New Room ➕</h3>
                    <button @click="handleClose" class="text-gray-400 hover:text-gray-600 transition"
                            title="Close Modal (Resets form)">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- SUCCESS MESSAGE -->
                <transition name="success-fade">
                    <div v-if="showSuccessMessage" class="fixed inset-0 z-[80] flex items-center justify-center">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg text-center">
                            <strong class="font-bold block text-xl mb-1">Success! 🎉</strong>
                            <span>Room '{{ newRoom.room }}' created successfully! Closing in 1.5 seconds...</span>
                        </div>
                    </div>
                </transition>

                <!-- FORM CONTENT -->
                <div :class="{ 'hidden': showSuccessMessage }">
                    <form @submit.prevent="handleSubmit" class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Room Name *</label>
                            <input type="text" v-model="newRoom.room" required
                                   class="mt-1 block w-full border-gray-300 rounded-md p-2 shadow-sm">
                        </div>

                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Buildings</label>
                                <select v-model="newRoom.building"
                                        class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                    <option value="">Select Type</option>
                                    <option value="Building arts">Building arts</option>
                                    <option value="Building UP Mains">Building UP Mains</option>
                                    <option value="Building Law">Building Law</option>
                                    <option value="Building Rooms">Building Rooms</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">College</label>
                                <select v-model="newRoom.college"
                                        class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                                    <option value="">Select Type</option>
                                    <option value="College IT-Department">College IT-Departments</option>
                                    <option value="College of Law">College of Law</option>
                                    <option value="College of HTM">College of HTM</option>
                                    <option value="College of Criminilogy">College of Criminology</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Capacity *</label>
                                <input type="number" v-model.number="newRoom.capacity" required min="1"
                                       class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                            </div>

                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Room Type</label>
                                <select v-model="newRoom.roomType"
                                        class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
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
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" v-model="newRoom.location"
                                   class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea v-model="newRoom.description" rows="2"
                                      class="mt-1 w-full border-gray-300 rounded-md p-2 shadow-sm"></textarea>
                        </div>

                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Room Equipments 🛠️</h4>

                            <div class="space-y-3 p-3 border border-dashed rounded-lg bg-gray-50">
                                <div class="flex space-x-2 items-end">
                                    <div class="w-2/3">
                                        <label class="block text-xs font-medium text-gray-600">Select Item</label>
                                        <select v-model="tempEquipment.name"
                                                class="w-full border-gray-300 rounded-md p-2">
                                            <option value="">Select Equipment</option>
                                            <option v-for="item in equipmentOptions" :key="item" :value="item">
                                                {{ item }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="w-1/6">
                                        <label class="block text-xs font-medium text-gray-600">Qty</label>
                                        <input type="number" v-model.number="tempEquipment.quantity" min="1"
                                               class="w-full border-gray-300 rounded-md p-2 text-center">
                                    </div>

                                    <button type="button" @click="addEquipment"
                                            class="w-1/6 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-md">
                                        + Add
                                    </button>
                                </div>
                            </div>

                            <div v-if="newRoom.equipments.length > 0" class="mt-4 space-y-2 max-h-40 overflow-y-auto">
                                <div v-for="(item, index) in newRoom.equipments" :key="index"
                                     class="flex items-center justify-between p-2 bg-purple-100 text-purple-800 rounded-md text-sm">
                                    <span>
                                        <strong>{{ item.name }}</strong>
                                        <span
                                            class="ml-2 px-2 py-0.5 bg-purple-500 text-white rounded-full text-xs font-bold">
                                            {{ item.quantity }} pc(s)
                                        </span>
                                    </span>

                                    <button type="button" @click="removeEquipment(index)"
                                            class="text-red-500 hover:text-red-700">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <div v-else class="text-center text-gray-500 text-sm italic mt-3">
                                No equipment added yet.
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" @click="handleClose"
                                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Create Room
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </transition>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.success-fade-enter-active,
.success-fade-leave-active {
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.success-fade-enter-from,
.success-fade-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
