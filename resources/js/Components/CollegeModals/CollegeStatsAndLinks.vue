<script setup>
import { ref, computed } from 'vue';
import { faEye, faPenToSquare, faTrash, faSearch } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// --- Reactive State & Data ---

// 1. CFIC Data State
const cficSearchTerm = ref(''); 
const cficUsers = ref([ 
    // Data copied directly from screenshot content
    { id: 1, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
    { id: 2, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
    { id: 3, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
    { id: 4, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
]);

// 2. Student Data State 
const studentSearchTerm = ref('');
const studentUsers = ref([
    // Data copied directly from screenshot content
    { id: 5, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
    { id: 6, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
    { id: 7, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
    { id: 8, user: 'User-1', email: 'evan@gmail.com', phone: '09982077429', profession: 'CFIC' },
]);


const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    search: faSearch,
};

// --- Computed Properties for Filtering ---

// CFIC Filtering
const filteredCficUsers = computed(() => {
    const query = cficSearchTerm.value.toLowerCase();
    if (!query) return cficUsers.value;

    return cficUsers.value.filter(user =>
        user.user.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query)
    );
});

// Student Filtering
const filteredStudentUsers = computed(() => {
    const query = studentSearchTerm.value.toLowerCase();
    if (!query) return studentUsers.value;

    return studentUsers.value.filter(user =>
        user.user.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query)
    );
});

// --- Action Handlers (Functionality) ---

const handleViewDetails = (details, type) => {
    // Placeholder function
    alert(`Viewing ${type} details for: ${details.user} (Email: ${details.email})`);
};

const handleEditDetails = (details, type) => {
    // Placeholder function
    alert(`Opening edit form for ${type}: ${details.user}`);
};

const handleDeleteDetails = (details, type) => {
    // Functional placeholder to demonstrate reactivity
    if (confirm(`Are you sure you want to delete ${details.user} from ${type} Data?`)) {
        if (type === 'CFIC') {
            cficUsers.value = cficUsers.value.filter(u => u.id !== details.id);
        } else if (type === 'STUDENT') {
            studentUsers.value = studentUsers.value.filter(u => u.id !== details.id);
        }
        alert(`${details.user} has been deleted.`);
    }
};
</script>

<template>
    <div class="space-y-6">

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">CFIC DATA</h2>

            <div class="mb-4 relative">
                <input 
                    type="text" 
                    placeholder="Search" 
                    v-model="cficSearchTerm"
                    class="w-full p-3 pl-10 border-0 rounded-lg focus:ring-0 shadow-sm bg-gray-200"
                >
                <FontAwesomeIcon :icon="icons.search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email-ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Profession</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Subject</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="user in filteredCficUsers" :key="user.id" class="hover:bg-gray-50"> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.user }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.profession }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.profession }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-lg space-x-2">
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
                        <tr v-if="filteredCficUsers.length === 0">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No CFIC users found matching "{{ cficSearchTerm }}".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">STUDENT DATA</h2>

            <div class="mb-4 relative">
                <input 
                    type="text" 
                    placeholder="Search" 
                    v-model="studentSearchTerm"
                    class="w-full p-3 pl-10 border-0 rounded-lg focus:ring-0 shadow-sm bg-gray-200"
                >
                <FontAwesomeIcon :icon="icons.search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23]">
                         <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email-ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Profession</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Subject</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="user in filteredStudentUsers" :key="user.id" class="hover:bg-gray-50"> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.user }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.profession }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.profession }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-lg space-x-2">
                                <button @click="handleViewDetails(user, 'STUDENT')" title="View Details"
                                    class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                </button>
                                <button @click="handleEditDetails(user, 'STUDENT')" title="Edit User"
                                    class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                </button>
                                <button @click="handleDeleteDetails(user, 'STUDENT')" title="Delete User"
                                    class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredStudentUsers.length === 0">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No student users found matching "{{ studentSearchTerm }}".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</template>