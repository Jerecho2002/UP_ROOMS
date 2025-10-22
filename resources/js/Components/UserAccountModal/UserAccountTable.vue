<script setup>
import { computed, ref, defineProps, defineEmits } from 'vue';

// Define the users prop received from the parent
const props = defineProps({
    users: {
        type: Array,
        required: true
    }
});

// Define the events this component can emit
const emit = defineEmits(['openModal']);

const searchQuery = ref('');

// Filter logic now operates on the prop array
const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return props.users;
    }
    const query = searchQuery.value.toLowerCase();
    return props.users.filter(user =>
        user.name.toLowerCase().includes(query) ||
        user.school.toLowerCase().includes(query) ||
        user.address.toLowerCase().includes(query)
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
        <h2 class="text-xl font-bold mb-4 text-gray-700">User Account</h2>

        <div class="mb-4 flex justify-between items-center">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="SEARCH"
                class="border border-gray-300 rounded-full px-4 py-2 w-72 focus:outline-none focus:ring-2 focus:ring-sky-400"
            />
            <button @click="handleAddAccount" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow transition duration-150">
                Add Account
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-xl">
            <div class="overflow-y-auto max-h-[80vh]">
                <table class="min-w-full border border-gray-200 text-sm text-center">
                    <thead class="bg-[#7A0C23] text-white sticky top-0 shadow">
                        <tr>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">NAME</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">SCHOOL</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">AGE</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">ADDRESS</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">ROOM</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">START</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">END</th>
                            <th class="px-4 py-3 border border-gray-500 font-semibold">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(user) in filteredUsers"
                            :key="user.id"
                            class="odd:bg-white even:bg-gray-100 hover:bg-gray-200 transition duration-100"
                        >
                            <td class="px-4 py-2 text-left font-medium text-gray-800">{{ user.name }}</td>
                            <td class="px-4 py-2">{{ user.school }}</td>
                            <td class="px-4 py-2">{{ user.age }}</td>
                            <td class="px-4 py-2 text-left">{{ user.address }}</td>
                            <td class="px-4 py-2">{{ user.room }}</td>
                            <td class="px-4 py-2">{{ user.start }}</td>
                            <td class="px-4 py-2">{{ user.end }}</td>
                            <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                                <button @click="handleView(user)" title="View" class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                                <button @click="handleEdit(user)" title="Edit" class="text-yellow-600 hover:text-yellow-800 transform hover:scale-110 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button @click="handleDelete(user)" title="Delete" class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredUsers.length === 0">
                            <td colspan="8" class="p-8 text-center text-gray-500">No users found matching your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
