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

// --- REACTIVE STATE ---
const isLoading = ref(false);
const formErrors = ref({});
const passwordField = ref('');
const confirmPassword = ref('');

// --- CONSTANTS ---
const roles = ['Admin', 'Staff', 'Faculty', 'DPTAPR', 'AO', 'ADPD', 'OCS', 'SYSADMIN', 'USER'];
const permissionsOptions = ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'];
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
    'N/A - Administration',
];

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

// Form data
const formData = ref({
    username: '',
    email: '',
    first_name: '',
    last_name: '',
    role: 'Staff',
    department: '',
    college: '',
    permissions: [],
});

// --- WATCHERS & LOGIC ---

// Watch for changes in the 'user' prop and 'type'
watch(() => [props.user, props.type], ([newUser, newType]) => {
    if (newType === 'add') {
        // Reset for a fresh 'add' form
        formData.value = {
            username: '',
            email: '',
            first_name: '',
            last_name: '',
            role: 'Staff',
            department: '',
            college: '',
            permissions: defaultPermissionsMap['Staff'],
        };
        passwordField.value = '';
        confirmPassword.value = '';
        formErrors.value = {};
    } else if (newUser) {
        // Deep copy the user object to formData for modification/view
        formData.value = {
            username: newUser.username || '',
            email: newUser.email || '',
            first_name: newUser.first_name || '',
            last_name: newUser.last_name || '',
            role: newUser.role || 'Staff',
            department: newUser.department || '',
            college: newUser.college || '',
            permissions: Array.isArray(newUser.permissions) ? newUser.permissions : (newUser.permissions ? [newUser.permissions] : []),
        };
        passwordField.value = '';
        confirmPassword.value = '';
        formErrors.value = {};
    } else {
        formData.value = {};
        formErrors.value = {};
    }
}, { immediate: true, deep: true });

// Watch for changes in 'role' and update 'permissions'
watch(() => formData.value.role, (newRole) => {
    if (props.type === 'add' || props.type === 'edit') {
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
const isEdit = computed(() => props.type === 'edit');
const isEditOrAdd = computed(() => props.type === 'edit' || props.type === 'add');
const isDelete = computed(() => props.type === 'delete');

// Check if password is required
const isPasswordRequired = computed(() => isAdd.value || (isEdit.value && passwordField.value));

// --- VALIDATION METHODS ---
const validateForm = () => {
    formErrors.value = {};

    if (!formData.value.username.trim()) {
        formErrors.value.username = 'Username is required';
    }

    if (!formData.value.email.trim()) {
        formErrors.value.email = 'Email is required';
    } else if (!isValidEmail(formData.value.email)) {
        formErrors.value.email = 'Please enter a valid email address';
    }

    if (!formData.value.department) {
        formErrors.value.department = 'Department is required';
    }

    if (!formData.value.college) {
        formErrors.value.college = 'College is required';
    }

    if (isPasswordRequired.value) {
        if (!passwordField.value) {
            formErrors.value.password = 'Password is required';
        } else if (passwordField.value.length < 6) {
            formErrors.value.password = 'Password must be at least 6 characters';
        } else if (passwordField.value !== confirmPassword.value) {
            formErrors.value.confirmPassword = 'Passwords do not match';
        }
    }

    return Object.keys(formErrors.value).length === 0;
};

const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

// --- FORM HANDLING METHODS ---
const handleSubmit = async () => {
    if (!validateForm()) {
        return;
    }

    isLoading.value = true;

    try {
        const payload = {
            ...formData.value,
            permissions: formData.value.permissions || []
        };

        // Add password only if provided
        if (isPasswordRequired.value && passwordField.value) {
            payload.password = passwordField.value;
        }

        // Add ID for edit mode
        if (isEdit.value && props.user) {
            payload.id = props.user.id;
        }

        // Emit to parent component
        emit('dataUpdated', payload, props.type);

    } catch (error) {
        console.error('Error in form submission:', error);
        // Let parent handle the error
        emit('dataUpdated', { error: error.message }, props.type);
    } finally {
        isLoading.value = false;
    }
};

const handleDeleteConfirm = async () => {
    isLoading.value = true;

    try {
        // Emit to parent component
        emit('dataUpdated', {
            id: props.user.id,
            username: props.user.username
        }, 'delete');

    } catch (error) {
        console.error('Error in delete confirmation:', error);
        // Let parent handle the error
        emit('dataUpdated', { error: error.message }, 'delete');
    } finally {
        isLoading.value = false;
    }
};

// Clear errors when typing
const clearError = (field) => {
    if (formErrors.value[field]) {
        delete formErrors.value[field];
    }
};
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="isVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="emit('close')">
            <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-100 opacity-100">

                <!-- Loading overlay -->
                <div v-if="isLoading" class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center rounded-lg z-10">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#7A0C23] mx-auto mb-4"></div>
                        <p class="text-gray-600">Processing...</p>
                    </div>
                </div>

                <!-- Header -->
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800">{{ modalTitle }}</h3>
                    <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition" :disabled="isLoading">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- View Mode -->
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
                        <p><strong>Created:</strong> {{ new Date(user.created_at).toLocaleDateString() }}</p>
                        <p><strong>Last Updated:</strong> {{ new Date(user.updated_at).toLocaleDateString() }}</p>
                    </div>

                    <div class="mt-4 pt-4 border-t">
                        <h4 class="font-semibold mb-2">Permissions</h4>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="p in user.permissions" :key="p"
                                  class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ p }}
                            </span>
                            <span v-if="!user.permissions || user.permissions.length === 0"
                                  class="text-gray-500 italic">
                                No specific permissions granted.
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t flex justify-end">
                        <button @click="emit('close')"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition"
                                :disabled="isLoading">
                            Close
                        </button>
                    </div>
                </div>

                <!-- Add/Edit Form -->
                <form v-else-if="isEditOrAdd" @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 text-left">First Name</label>
                            <input type="text" id="first_name" v-model="formData.first_name"
                                   @input="clearError('first_name')"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 text-left">Last Name</label>
                            <input type="text" id="last_name" v-model="formData.last_name"
                                   @input="clearError('last_name')"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Username & Email -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 text-left">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" v-model="formData.username" required
                                   @input="clearError('username')"
                                   :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                            formErrors.username ? 'border-red-500' : 'border-gray-300']">
                            <p v-if="formErrors.username" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.username }}</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 text-left">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" v-model="formData.email" required
                                   @input="clearError('email')"
                                   :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                            formErrors.email ? 'border-red-500' : 'border-gray-300']">
                            <p v-if="formErrors.email" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.email }}</p>
                        </div>
                    </div>

                    <!-- Password Fields (Only for Add or when password is entered) -->
                    <div v-if="isAdd" class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 text-left">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" v-model="passwordField" required
                                   @input="clearError('password')"
                                   :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                            formErrors.password ? 'border-red-500' : 'border-gray-300']">
                            <p v-if="formErrors.password" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.password }}</p>
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 text-left">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="confirmPassword" v-model="confirmPassword" required
                                   @input="clearError('confirmPassword')"
                                   :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                            formErrors.confirmPassword ? 'border-red-500' : 'border-gray-300']">
                            <p v-if="formErrors.confirmPassword" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.confirmPassword }}</p>
                        </div>
                    </div>

                    <!-- Password Fields for Edit (Optional) -->
                    <div v-if="isEdit" class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 text-left">
                                New Password <span class="text-gray-400 text-xs">(Leave blank to keep current)</span>
                            </label>
                            <input type="password" id="password" v-model="passwordField"
                                   @input="clearError('password')"
                                   :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                            formErrors.password ? 'border-red-500' : 'border-gray-300']">
                            <p v-if="formErrors.password" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.password }}</p>
                        </div>

                        <div v-if="passwordField">
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 text-left">
                                Confirm New Password
                            </label>
                            <input type="password" id="confirmPassword" v-model="confirmPassword"
                                   @input="clearError('confirmPassword')"
                                   :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                            formErrors.confirmPassword ? 'border-red-500' : 'border-gray-300']">
                            <p v-if="formErrors.confirmPassword" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.confirmPassword }}</p>
                        </div>
                    </div>

                    <!-- Department & College -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 text-left">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select id="department" v-model="formData.department" required
                                    @change="clearError('department')"
                                    :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                             formErrors.department ? 'border-red-500' : 'border-gray-300']">
                                <option value="" disabled>Select Department</option>
                                <option v-for="dept in departmentOptions" :key="dept" :value="dept">{{ dept }}</option>
                            </select>
                            <p v-if="formErrors.department" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.department }}</p>
                        </div>

                        <div>
                            <label for="college" class="block text-sm font-medium text-gray-700 text-left">
                                College <span class="text-red-500">*</span>
                            </label>
                            <select id="college" v-model="formData.college" required
                                    @change="clearError('college')"
                                    :class="['mt-1 block w-full border rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                                             formErrors.college ? 'border-red-500' : 'border-gray-300']">
                                <option value="" disabled>Select College</option>
                                <option v-for="col in collegeOptions" :key="col" :value="col">{{ col }}</option>
                            </select>
                            <p v-if="formErrors.college" class="mt-1 text-sm text-red-600 text-left">{{ formErrors.college }}</p>
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 text-left">Role</label>
                        <select id="role" v-model="formData.role"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                        </select>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Permissions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 text-left">Permissions</label>
                        <div class="grid grid-cols-2 gap-2 p-3 border border-gray-300 rounded-md bg-gray-50">
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
                        <p class="mt-1 text-xs text-gray-500 text-left">
                            Default permissions set for <strong>{{ formData.role }}</strong> role.
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-4 border-t flex justify-end space-x-3">
                        <button type="button" @click="emit('close')"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition disabled:opacity-50"
                                :disabled="isLoading">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition disabled:opacity-50 flex items-center"
                                :disabled="isLoading">
                            <span v-if="isLoading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                            {{ isAdd ? 'Add Account' : 'Save Changes' }}
                        </button>
                    </div>
                </form>

                <!-- Delete Confirmation -->
                <div v-else-if="isDelete && user" class="space-y-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.692-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Delete User Account</h3>
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete the account for <strong class="text-red-600">{{ user.username }}</strong>?
                        </p>
                        <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        This action cannot be undone. All data associated with this account will be permanently removed.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
                        <button @click="emit('close')"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition disabled:opacity-50"
                                :disabled="isLoading">
                            Cancel
                        </button>
                        <button @click="handleDeleteConfirm"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition disabled:opacity-50 flex items-center"
                                :disabled="isLoading">
                            <span v-if="isLoading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
                            Delete Permanently
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </Transition>
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

/* Custom scrollbar for modal */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
