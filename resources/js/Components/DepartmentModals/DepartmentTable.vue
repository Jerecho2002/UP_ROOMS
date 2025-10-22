<script setup>
import { ref, computed } from 'vue';

// Define props to receive the function to open the modal from the parent
const props = defineProps({
    openModal: {
        type: Function,
        required: true
    },
    users: { // Receive users data as a prop
        type: Array,
        required: true
    },
    lastMonthUsers: { // Receive lastMonthUsers data as a prop
        type: Array,
        required: true
    }
});


// --- DATA & LOGIC FOR USER DETAILS TABLE (FIRST TABLE) ---

// 1. State for the search query (First Table)
const searchQuery = ref('');

// 2. Computed property to filter the users array
const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return props.users;
    }

    const query = searchQuery.value.toLowerCase();

    return props.users.filter(user =>
        // Search by User Name
        user.name.toLowerCase().includes(query) ||
        // Search by Email-ID
        user.email.toLowerCase().includes(query)
    );
});


// --- DATA & LOGIC FOR LAST MONTH CREATED USER IDS (SECOND TABLE) ---

// 3. State for the search query (Second Table)
const lastMonthSearchQuery = ref('');

// 4. Computed property to filter the last month users array
const filteredLastMonthUsers = computed(() => {
    if (!lastMonthSearchQuery.value) {
        return props.lastMonthUsers;
    }

    const query = lastMonthSearchQuery.value.toLowerCase();

    return props.lastMonthUsers.filter(item =>
        // Search by Email
        item.email.toLowerCase().includes(query) ||
        // Search by User-ID
        item.userId.toLowerCase().includes(query)
    );
});


// --- ACTION HANDLERS (Delegating to the prop function) ---
// We delegate the opening of the modal to the 'openModal' function passed from the parent.

const handleView = (user) => props.openModal('view', user, 'users');
const handleEdit = (user) => props.openModal('edit', user, 'users');
const handleDelete = (user) => props.openModal('delete', user, 'users');

const handleView2 = (item) => props.openModal('view', item, 'lastMonthUsers');
const handleEdit2 = (item) => props.openModal('edit', item, 'lastMonthUsers');
const handleDelete2 = (item) => props.openModal('delete', item, 'lastMonthUsers');

</script>

<template>
    <div class="p-6 bg-gray-100">
        <div class="bg-white shadow-2xl rounded-xl p-6 mb-10">
            <h2 class="font-bold text-xl text-gray-800 mb-4 border-b pb-2">User Details</h2>

            <div class="mb-4">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search by User or Email..."
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-[#7A0C23] transition duration-150 shadow-sm"
                />
            </div>

            <div class="overflow-x-auto">
                <div class="overflow-y-auto max-h-96 border rounded-lg">
                    <table class="min-w-full text-sm border-collapse">
                        <thead class="bg-[#7A0C23] text-white sticky top-0 shadow-md">
                            <tr>
                                <th class="p-4 text-left">User</th>
                                <th class="p-4 text-left">Email-ID</th>
                                <th class="p-4 text-left hidden sm:table-cell">Phone</th>
                                <th class="p-4 text-left hidden md:table-cell">Profession</th>
                                <th class="p-4 text-left w-0 min-w-min">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="(user, index) in filteredUsers"
                                :key="user.id"
                                :class="{'bg-gray-50': index % 2 !== 0, 'hover:bg-gray-100 transition duration-150': true}"
                            >
                                <td class="p-4 text-left font-medium text-gray-700">{{ user.name }}</td>
                                <td class="p-4 text-left text-gray-600">{{ user.email }}</td>
                                <td class="p-4 text-left text-gray-600 hidden sm:table-cell">{{ user.phone }}</td>
                                <td class="p-4 text-left hidden md:table-cell">
                                    <span
                                        :class="user.profession === 'Instructor' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800'"
                                        class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                    >
                                        {{ user.profession }}
                                    </span>
                                </td>
                                <td class="p-4 text-left space-x-3 whitespace-nowrap w-0 min-w-min">
                                    <button @click="handleView(user)" title="View Details" class="text-blue-500 hover:text-blue-700 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <button @click="handleEdit(user)" title="Edit User" class="text-yellow-600 hover:text-yellow-800 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button @click="handleDelete(user)" title="Delete User" class="text-red-600 hover:text-red-800 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="p-4 text-center text-gray-500 bg-white">
                                    <p class="py-4">No users found matching your search query. 😔</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="bg-white shadow-2xl rounded-xl p-6">
            <h2 class="font-bold text-xl text-gray-800 mb-4 border-b pb-2">Last Month Created User IDs</h2>

            <div class="mb-4">
                <input
                    type="text"
                    v-model="lastMonthSearchQuery"
                    placeholder="Search by Email or User ID..."
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-[#7A0C23] transition duration-150 shadow-sm"
                />
            </div>

            <div class="overflow-x-auto">
                <div class="overflow-y-auto max-h-96 border rounded-lg">
                    <table class="min-w-full text-sm border-collapse">
                        <thead class="bg-[#7A0C23] text-white sticky top-0 shadow-md">
                            <tr>
                                <th class="p-4 text-left">Email</th>
                                <th class="p-4 text-left">User-ID</th>
                                <th class="p-4 text-left hidden sm:table-cell">Month</th>
                                <th class="p-4 text-left hidden md:table-cell">Year Start</th>
                                <th class="p-4 text-left hidden lg:table-cell">Year End</th>
                                <th class="p-4 text-left w-0 min-w-min">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="(item, index) in filteredLastMonthUsers"
                                :key="item.userId"
                                :class="{'bg-gray-50': index % 2 !== 0, 'hover:bg-gray-100 transition duration-150': true}"
                            >
                                <td class="p-4 text-left text-gray-600">{{ item.email }}</td>
                                <td class="p-4 text-left font-mono text-gray-800 font-semibold">{{ item.userId }}</td>
                                <td class="p-4 text-left text-gray-600 hidden sm:table-cell">{{ item.month }}</td>
                                <td class="p-4 text-left text-gray-600 hidden md:table-cell">{{ item.yearStart }}</td>
                                <td class="p-4 text-left text-gray-600 hidden lg:table-cell">{{ item.yearEnd }}</td>
                                <td class="p-4 text-left space-x-3 whitespace-nowrap w-0 min-w-min">
                                    <button @click="handleView2(item)" title="View Details" class="text-blue-500 hover:text-blue-700 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <button @click="handleEdit2(item)" title="Edit User" class="text-yellow-600 hover:text-yellow-800 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button @click="handleDelete2(item)" title="Delete User" class="text-red-600 hover:text-red-800 transition transform hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredLastMonthUsers.length === 0">
                                <td colspan="6" class="p-4 text-center text-gray-500 bg-white">
                                    <p class="py-4">No new users found matching your search query. 😥</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>