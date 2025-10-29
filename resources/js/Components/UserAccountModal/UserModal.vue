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
    user: {
        type: Object,
        default: () => null,
    },
});

// Emits 'close' and 'dataUpdated(data, type)'
const emit = defineEmits(['close', 'dataUpdated']);

// Local state for the form (used for edit/add)
const formData = ref({});

// Watch for changes in the 'user' prop and update local formData for editing
watch(() => [props.user, props.type], ([newUser, newType]) => {
    if (newType === 'add') {
        // Reset for a fresh 'add' form
        formData.value = { 
            name: '', 
            school: '', 
            age: 0, 
            address: '', 
            room: '', 
            start: new Date().toISOString().substring(0, 10), // Current date default
            end: new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().substring(0, 10), // Next year default
        };
    } else if (newUser) {
        // Deep copy the user object to formData for modification in edit/view
        formData.value = { ...newUser };
    } else {
        // Fallback reset
        formData.value = {};
    }
}, { immediate: true });


// Computed properties for dynamic content
const modalTitle = computed(() => {
    switch (props.type) {
        case 'add': return 'Add New User Account';
        case 'view': return `View Details: ${props.user?.name || 'User'}`;
        case 'edit': return `Edit User Account: ${props.user?.name || 'User'}`;
        case 'delete': return `Confirm Delete: ${props.user?.name || 'User'}`;
        default: return 'User Account Action';
    }
});

const isView = computed(() => props.type === 'view');
const isEdit = computed(() => props.type === 'edit');
const isAdd = computed(() => props.type === 'add');
const isDelete = computed(() => props.type === 'delete');


// --- Action Handlers ---

const handleSubmit = () => {
    if (isAdd.value || isEdit.value) {
        // Notify parent with the modified data and the action type
        emit('dataUpdated', formData.value, props.type);
    }
};

const handleDeleteConfirm = () => {
    // Notify parent with the user data and the delete action type
    emit('dataUpdated', props.user, 'delete');
};

</script>

<template>
    <Transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="emit('close')">
            
            <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 opacity-100">
                
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">{{ modalTitle }}</h3>
                    <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- VIEW MODE -->
                <div v-if="isView && user" class="space-y-3 text-gray-700">
                    <p><strong>ID:</strong> {{ user.id }}</p>
                    <p><strong>Name:</strong> {{ user.name }}</p>
                    <p><strong>School:</strong> {{ user.school }}</p>
                    <p><strong>Age:</strong> {{ user.age }}</p>
                    <p><strong>Address:</strong> {{ user.address }}</p>
                    <p><strong>Room:</strong> {{ user.room }}</p>
                    <p><strong>Start Date:</strong> {{ user.start }}</p>
                    <p><strong>End Date:</strong> {{ user.end }}</p>
                    <div class="mt-6 pt-4 border-t flex justify-end">
                        <button @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Close</button>
                    </div>
                </div>

                <!-- ADD/EDIT FORM -->
                <form v-else-if="isAdd || isEdit" @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" v-model="formData.name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div>
                        <label for="school" class="block text-sm font-medium text-gray-700">School</label>
                        <input type="text" id="school" v-model="formData.school" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div>
                        <label for="room" class="block text-sm font-medium text-gray-700">Room</label>
                        <input type="text" id="room" v-model="formData.room" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                            <input type="number" id="age" v-model.number="formData.age" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" id="address" v-model="formData.address" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="start" v-model="formData.start" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="end" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="end" v-model="formData.end" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>


                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <button type="button" @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" :class="isAdd ? 'bg-green-600 hover:bg-green-700' : 'bg-yellow-600 hover:bg-yellow-700'" class="text-white px-4 py-2 rounded transition">
                            {{ isAdd ? 'Add Account' : 'Save Changes' }}
                        </button>
                    </div>
                </form>

                <!-- DELETE CONFIRMATION -->
                <div v-else-if="isDelete && user" class="space-y-4">
                    <p class="text-lg text-red-600">Are you sure you want to delete the account for **{{ user.name }}**?</p>
                    <p class="text-gray-600">This action cannot be undone.</p>
                    <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
                        <button @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button @click="handleDeleteConfirm" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">Delete Permanently</button>
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
