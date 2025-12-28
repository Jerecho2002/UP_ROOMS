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

// --- CONSTANTS ---

// Available roles
const roles = ['Admin', 'Staff', 'Faculty', 'DPTAPR', 'AO', 'ADPD', 'OCS', 'SYSADMIN', 'USER'];

// Available permissions (checkbox options)
const permissionsOptions = ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'];

// --- NEW STATIC LISTS FOR DROPDOWNS ---
const collegeOptions = [
    'College of Engineering (CoE)',
    'College of Arts and Sciences (CAS)',
    'College of Business and Accountancy (CBA)',
    'College of Education (CoEd)',
    'College of Information Technology (CIT)',
    'Graduate School (GS)',
];

const departmentOptions = [
    'Computer Science',
    'Electrical Engineering',
    'Mechanical Engineering',
    'Physics',
    'Mathematics',
    'English/Literature',
    'Accounting',
    'Management',
    'N/A - Administration', // Option for non-academic staff
];

/**
 * Defines the default permissions for each role.
 */
const defaultPermissionsMap = {
    Admin: ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'],
    Staff: ['Can Book', 'Staff Work'],
    Faculty: ['Can Book', 'User Type Only'],
    DPTAPR: ['Can Approve', 'Can Book', 'User Type Only'],
    AO: ['Can Approve', 'Can Edit', 'Staff Work'],
    ADPD: ['Can Approve', 'Can Edit'],
    OCS: ['Can Approve', 'Can Edit', 'Can Book'],
    SYSADMIN: ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'],
    USER: ['Can Book', 'User Type Only'],
};

// --- REACTIVE STATE ---

// Local state for the form - NOW INCLUDING DEPARTMENT AND COLLEGE
const formData = ref({
    username: '',
    email: '',
    first_name: '',
    last_name: '',
    role: 'Staff',
    // --- NEW FIELDS ---
    department: '', // Initialize new field
    college: '',    // Initialize new field
    // --- END NEW FIELDS ---
    permissions: [],
});


// --- WATCHERS & LOGIC ---

// 1. Watch for changes in the 'user' prop and 'type' and initialize formData
watch(() => [props.user, props.type], ([newUser, newType]) => {
    if (newType === 'add') {
        // Reset for a fresh 'add' form
        formData.value = {
            username: '',
            email: '',
            first_name: '',
            last_name: '',
            role: 'Staff', // Default role
            department: '', // Reset department
            college: '',    // Reset college
            permissions: defaultPermissionsMap['Staff'], // Default Staff permissions
        };
    } else if (newUser) {
        // Deep copy the user object to formData for modification/view
        formData.value = {
            username: newUser.username || '',
            email: newUser.email || '',
            first_name: newUser.first_name || '',
            last_name: newUser.last_name || '',
            role: newUser.role || 'Staff',
            // --- INITIALIZE NEW FIELDS FROM USER PROP ---
            department: newUser.department || '',
            college: newUser.college || '',
            // --- END INITIALIZE NEW FIELDS ---
            permissions: Array.isArray(newUser.permissions) ? newUser.permissions : (newUser.permissions ? [newUser.permissions] : []),
        };
    } else {
        // Fallback reset
        formData.value = {};
    }
}, { immediate: true, deep: true });

// 2. Watch for changes in 'role' and automatically update 'permissions'
watch(() => formData.value.role, (newRole) => {
    // Only apply default permissions if we are in 'add' or 'edit' mode.
    if (props.type === 'add' || props.type === 'edit') {
        // Only update if the user hasn't started manually modifying permissions
        // A simpler approach is just to apply the default map:
        formData.value.permissions = defaultPermissionsMap[newRole] || [];
    }
});


// --- COMPUTED PROPERTIES ---

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


// --- ACTION HANDLERS ---

const handleSubmit = () => {
    if (isAdd.value || props.type === 'edit') {
        // Validate basic fields + new required fields
        if (!formData.value.username || !formData.value.email || !formData.value.department || !formData.value.college) {
            console.error('Username, Email, Department, and College are required.');
            alert('Username, Email, Department, and College are required.'); // User-facing alert
            return;
        }
        // Notify parent with the modified data and the action type
        emit('dataUpdated', formData.value, props.type);
        emit('close');
    }
};

const handleDeleteConfirm = () => {
    // Notify parent with the user data and the delete action type
    emit('dataUpdated', props.user, 'delete');
    emit('close');
};

</script>
<template>
    <Transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="emit('close')">

            <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 opacity-100">

                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">{{ modalTitle }}</h3>
                    <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>



                <div v-if="isView && user" class="space-y-3 text-gray-700">
                    <div class="grid grid-cols-2 gap-4">
                        <p v-if="user.id"><strong>ID:</strong> {{ user.id }}</p>
                        <p><strong>Username:</strong> {{ user.username }}</p>
                        <p><strong>Email:</strong> {{ user.email }}</p>
                        <p><strong>Role:</strong> {{ user.role }}</p>
                        <p><strong>First Name:</strong> {{ user.first_name || 'N/A' }}</p>
                        <p><strong>Last Name:</strong> {{ user.last_name || 'N/A' }}</p>
                        <p><strong>Department:</strong> {{ user.department || 'N/A' }}</p>
                        <p><strong>College:</strong> {{ user.college || 'N/A' }}</p>
                    </div>

                    <h4 class="font-semibold mt-4 pt-4 border-t">Permissions</h4>
                    <ul class="list-disc list-inside ml-4">
                        <li v-if="user.permissions && user.permissions.length > 0" v-for="p in user.permissions" :key="p">{{ p }}</li>
                        <li v-else class="text-gray-500 italic">No specific permissions granted.</li>
                    </ul>

                    <div class="mt-6 pt-4 border-t flex justify-end">
                        <button @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Close</button>
                    </div>
                </div>



                <form v-else-if="isEditOrAdd" @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 text-left">First Name</label>
                            <input type="text" id="first_name" v-model="formData.first_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 text-left">Last Name</label>
                            <input type="text" id="last_name" v-model="formData.last_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 text-left">Username <span class="text-red-500">*</span></label>
                            <input type="text" id="username" v-model="formData.username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 text-left">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" v-model="formData.email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 text-left">Department <span class="text-red-500">*</span></label>
                            <select id="department" v-model="formData.department" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                <option value="" disabled>Select Department</option>
                                <option v-for="dept in departmentOptions" :key="dept" :value="dept">{{ dept }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="college" class="block text-sm font-medium text-gray-700 text-left">College <span class="text-red-500">*</span></label>
                            <select id="college" v-model="formData.college" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                <option value="" disabled>Select College</option>
                                <option v-for="col in collegeOptions" :key="col" :value="col">{{ col }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 text-left">Role</label>
                        <select id="role" v-model="formData.role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                        </select>
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">Permissions</label>
                        <div class="grid grid-cols-2 gap-2 p-3 border border-gray-300 rounded-md">
                            <div v-for="permission in permissionsOptions" :key="permission" class="flex items-center">
                                <input
                                    :id="permission"
                                    type="checkbox"
                                    :value="permission"
                                    v-model="formData.permissions"
                                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                >
                                <label :for="permission" class="ml-2 block text-sm text-gray-900">{{ permission }}</label>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 text-left">Default permissions set for <strong>{{ formData.role }}</strong> role.</p>
                    </div>

                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <button type="button" @click="emit('close')" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancel</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                            {{ isAdd ? 'Add Account' : 'Save Changes' }}
                        </button>
                    </div>
                </form>

                <div v-else-if="isDelete && user" class="space-y-4">
                    <p class="text-lg text-red-600">Are you sure you want to delete the account for <strong>{{ user.username }}</strong>?</p>
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
