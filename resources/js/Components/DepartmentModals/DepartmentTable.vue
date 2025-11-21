<script setup>
import { ref, computed } from 'vue';
import { faEye, faPenToSquare, faTrash, faSearch } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';


const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    search: faSearch,
};

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
    <div class="p-6 bg-gray-200">
        <div class="bg-white shadow-2xl rounded-xl p-6 mb-10">
             <h6 class="font-bold text-l text-[#7A0C23] mb-4 border-b pb-2">TABLE LIST 📃</h6>

        <div class="mb-4 relative">
                <input 
                    type="text" 
                    placeholder="Search Name College.." 
                    v-model="cficSearchTerm"
                    class=" max-w-[400px] p-3 pl-10 border-0 rounded-lg focus:ring-0 shadow-sm bg-gray-200"
                >
                <FontAwesomeIcon :icon="icons.search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
            </div>
          

            <div class="overflow-x-auto">
                <div class=" " >
                    <table class="min-w-full text-sm border-collapse">
                        <thead class="bg-[#7A0C23] text-white sticky top-0 shadow-md">
                            <tr>
                                <th class="p-4 text-left">Name</th>
                                <th class="p-4 text-left">College</th>
                              
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
                                <td class="p-4 text-left text-gray-600">{{ user.College }}</td>
                              
                                <td class="p-4 text-left space-x-3 whitespace-nowrap w-0 min-w-min">
                                       <button @click="handleViewDetails(user, 'CFIC')" title="View Details"
                                    class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                </button>
                                <button @click="handleEditDetails(user, 'CFIC')" title="Edit User"
                                    class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                </button>
                                <button @click="handleDeleteDetails(user, 'CFIC')" title="Delete User"
                                    class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
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


     
    </div>
</template>