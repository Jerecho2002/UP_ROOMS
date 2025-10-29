<script setup>
import { defineProps, defineEmits, computed, ref, watch } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String, // 'add', 'view', 'edit', 'delete'
        default: null,
    },
    building: {
        type: Object,
        default: () => null,
    },
});

// Emits 'close' and 'dataUpdated(data, type)'
const emit = defineEmits(['close', 'dataUpdated']);

// Local state for the form (used for edit/add)
const formData = ref({});

// Watch for changes in the 'building' prop and update local formData
watch(() => [props.building, props.type], ([newBuilding, newType]) => {
    if (newType === 'add') {
        // Reset for a fresh 'add' form with defaults
        formData.value = { 
            name: '', 
            address: '', 
            total_space: '', 
            lift: 'No', 
            parking: 'false', // Use string for select input
        };
    } else if (newBuilding) {
        // Deep copy and normalize parking boolean to string for the form
        formData.value = { 
            ...newBuilding,
            parking: newBuilding.parking ? 'true' : 'false'
        };
    } else {
        // Fallback reset
        formData.value = {};
    }
}, { immediate: true });


// Computed properties for dynamic content
const modalTitle = computed(() => {
    const buildingName = props.building?.name || 'Building';
    switch (props.type) {
        case 'add': return 'Add New Building';
        case 'view': return `Details: ${buildingName}`;
        case 'edit': return `Edit Building: ${buildingName}`;
        case 'delete': return `Confirm Delete: ${buildingName}`;
        default: return 'Building Action';
    }
});

const isView = computed(() => props.type === 'view');
const isEdit = computed(() => props.type === 'edit');
const isAdd = computed(() => props.type === 'add');
const isDelete = computed(() => props.type === 'delete');


// --- Action Handlers ---

const handleSubmit = () => {
    if (isAdd.value || isEdit.value) {
        // Emit data (parking is still a string here, parent converts it to boolean)
        emit('dataUpdated', formData.value, props.type);
    }
};

const handleDeleteConfirm = () => {
    // Emit user data for identification and the delete action type
    emit('dataUpdated', props.building, 'delete');
};

</script>

<template>
    <Transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="emit('close')">
            
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 opacity-100">
                
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">{{ modalTitle }}</h3>
                    <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- VIEW MODE -->
                <div v-if="isView && building" class="space-y-3 text-gray-700">
                    <p><strong>ID:</strong> {{ building.id }}</p>
                    <p><strong>Name:</strong> {{ building.name }}</p>
                    <p><strong>Address:</strong> {{ building.address }}</p>
                    <p><strong>Total Space:</strong> {{ building.total_space }}</p>
                    <p><strong>Lift:</strong> {{ building.lift }}</p>
                    <p><strong>Parking:</strong> <span :class="building.parking ? 'text-green-600' : 'text-red-600'">{{ building.parking ? 'Available' : 'Unavailable' }}</span></p>
                    
                    <div class="mt-6 pt-4 border-t flex justify-end">
                        <button @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Close</button>
                    </div>
                </div>

                <!-- ADD/EDIT FORM -->
                <form v-else-if="isAdd || isEdit" @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Building Name</label>
                        <input type="text" id="name" v-model="formData.name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input type="text" id="address" v-model="formData.address" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div>
                        <label for="space" class="block text-sm font-medium text-gray-700">Total Space (e.g., 5000 sq ft)</label>
                        <input type="text" id="space" v-model="formData.total_space" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="lift" class="block text-sm font-medium text-gray-700">Lift/Elevators</label>
                            <input type="text" id="lift" v-model="formData.lift" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="e.g., Yes (2) or No">
                        </div>
                        <div>
                            <label for="parking" class="block text-sm font-medium text-gray-700">Parking Available</label>
                            <select id="parking" v-model="formData.parking" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 appearance-none bg-white">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                    </div>


                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <button type="button" @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Cancel</button>
                        <button type="submit" :class="isAdd ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'" class="text-white px-4 py-2 rounded-lg transition">
                            {{ isAdd ? 'Add Building' : 'Save Changes' }}
                        </button>
                    </div>
                </form>

                <!-- DELETE CONFIRMATION -->
                <div v-else-if="isDelete && building" class="space-y-4">
                    <p class="text-lg text-red-600 font-semibold">Are you absolutely sure you want to delete the building **{{ building.name }}**?</p>
                    <p class="text-gray-600">This action will remove all associated data and cannot be undone.</p>
                    <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
                        <button @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Cancel</button>
                        <button @click="handleDeleteConfirm" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">Delete Permanently</button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Basic fade transition for a smoother user experience */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
