<script setup>
import { computed, ref, defineProps, defineEmits } from 'vue';

// 1. --- Font Awesome Imports & Setup ---
// You must import the Font Awesome component and the specific icons you need.
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash } from '@fortawesome/free-solid-svg-icons';

// Mapping icons for easier use in the template
const icons = {
    eye: faEye,
    edit: faPenToSquare, // Correct icon for 'Edit'
    delete: faTrash,     // Correct icon for 'Delete'
};
// ----------------------------------------

const props = defineProps({
    users: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['openModal']);

const searchQuery = ref('');

// Filtering now uses the new user fields
const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return props.users;
    }
    const query = searchQuery.value.toLowerCase();
    return props.users.filter(user =>
        user.username.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query) ||
        user.first_name.toLowerCase().includes(query) ||
        user.last_name.toLowerCase().includes(query) ||
        user.role.toLowerCase().includes(query)
    );
});

// --- Action Handlers (Emit events to parent) ---

const handleAddAccount = () => {
    emit('openModal', 'add');
};

const handleView = (user) => {
    emit('openModal', 'view', user);
};

const handleEdit = (user) => {
    emit('openModal', 'edit', user);
};

const handleDelete = (user) => {
    emit('openModal', 'delete', user);
};

</script>

<template>
    <div class="flex-1 p-6">
        <h2 class="text-xl font-bold mb-4 text-[#7A0C23]">User Account Management</h2>

        <div class="mb-4 flex justify-between items-center">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="SEARCH "
                class="border border-gray-300 rounded-full px-4 py-2 w-72 focus:outline-none focus:ring-2 focus:ring-sky-400"
            />
            <button @click="handleAddAccount" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow transition duration-150">
                Add New User
            </button>
        </div>

        <div class=" bg-white rounded-lg shadow-xl">
            <div class=" max-h-[80vh]">
                <table class="min-w-full border border-gray-200 text-sm text-center">
                    <thead class="bg-[#7A0C23] text-white sticky top-0 shadow">
                        <tr>
                             <th class="px-4 py-3 border border-gray-500 font-semibold text-left">NAME</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold text-left">USERNAME</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold text-left">EMAIL</th>
                            
                            <th class="px-4 py-3 border border-gray-500 font-semibold">OFFICE</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(user) in filteredUsers"
                            :key="user.id"
                            class="odd:bg-white even:bg-gray-100 hover:bg-gray-200 transition duration-100"
                        >
                            <td class="px-4 py-2 text-left font-medium text-gray-800">{{ user.first_name }}</td>
                           
                             <td class="px-4 py-2 text-left">{{ user.username }}</td>
                            
                            <td class="px-4 py-2 text-left">{{ user.email }}</td>
                            
                            <td class="px-4 py-2">{{ user.role }}</td>
                            <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                                <button @click="handleView(user)" title="View" class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                </button>
                                <button @click="handleEdit(user)" title="Edit" class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                </button>
                                <button @click="handleDelete(user)" title="Delete" class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredUsers.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-500">No users found matching your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>