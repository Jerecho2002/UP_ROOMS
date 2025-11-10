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

const emit = defineEmits(['close', 'dataUpdated']);

// Local state for the form (used for edit/add)
const formData = ref({});

// Watch for changes in the 'user' prop and update local formData for editing
watch(() => [props.user, props.type], ([newUser, newType]) => {
    if (newType === 'add') {
        // Reset for a fresh 'add' form with initial values for the new schema
        formData.value = { 
            username: '', 
            email: '', 
            first_name: '', 
            last_name: '', 
            role: 'Staff', // Default role
        };
    } else if (newUser) {
        // Deep copy the user object to formData for modification
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
        case 'view': return `View Details: ${props.user?.username || 'User'}`;
        case 'edit': return `Edit User Account: ${props.user?.username || 'User'}`;
        case 'delete': return `Confirm Delete: ${props.user?.username || 'User'}`;
        default: return 'User Account Action';
    }
});

const isView = computed(() => props.type === 'view');
const isAdd = computed(() => props.type === 'add');
const isEditOrAdd = computed(() => props.type === 'edit' || props.type === 'add');
const isDelete = computed(() => props.type === 'delete');


// --- Action Handlers ---

const handleSubmit = () => {
    if (isAdd.value || props.type === 'edit') {
        // Validate basic fields
        if (!formData.value.username || !formData.value.email) {
            console.error('Username and Email are required.');
            return;
        }
        // Notify parent with the modified data and the action type
        emit('dataUpdated', formData.value, props.type);
    }
};

const handleDeleteConfirm = () => {
    // Notify parent with the user data and the delete action type
    emit('dataUpdated', props.user, 'delete');
};

// Available roles, matching the factory
const roles = ['Admin', 'Staff', 'Faculty'];

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
                    <p><strong>Username:</strong> {{ user.username }}</p>
                    <p><strong>Email:</strong> {{ user.email }}</p>
                    <p><strong>First Name:</strong> {{ user.first_name || 'N/A' }}</p>
                    <p><strong>Last Name:</strong> {{ user.last_name || 'N/A' }}</p>
                    <p><strong>Role:</strong> {{ user.role }}</p>
                    <div class="mt-6 pt-4 border-t flex justify-end">
                        <button @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Close</button>
                    </div>
                </div>

                <!-- ADD/EDIT FORM -->
                <form v-else-if="isEditOrAdd" @submit.prevent="handleSubmit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="first_name" v-model="formData.first_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="last_name" v-model="formData.last_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" v-model="formData.username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" v-model="formData.email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" v-model="formData.role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                        </select>
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
                    <p class="text-lg text-red-600">Are you sure you want to delete the account for **{{ user.username }}**?</p>
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