<script setup>
import { ref, computed } from 'vue';
import { faEye, faPenToSquare, faTrash, faSearch, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// --- Data & State ---

// 1. Initial, More Realistic Data 📚
const collegeData = ref([
    { id: 1, college: 'College of Engineering', department: 'Computer Engineering', dean: 'Dr. Emily Carter' },
    { id: 2, college: 'College of Arts and Sciences', department: 'Psychology', dean: 'Prof. David Lee' },
    { id: 3, college: 'College of Business', department: 'Accountancy', dean: 'Dr. Samantha Wells' },
    { id: 4, college: 'College of Engineering', department: 'Civil Engineering', dean: 'Dr. Emily Carter' },
    { id: 5, college: 'College of Arts and Sciences', department: 'Biology', dean: 'Prof. David Lee' },
]);

const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    search: faSearch,
    add: faPlus,
};

// 2. Search State
const searchTerm = ref('');

// 3. Modal State
const isViewModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isAddModalOpen = ref(false);
const currentItem = ref(null); // Used for View/Edit operations

// 4. New Department Form State
const newDepartment = ref({
    college: '',
    department: '',
    dean: '',
});

// --- Computed Properties for Filtering ---

const filteredCollegeData = computed(() => {
    const query = searchTerm.value.toLowerCase();
    if (!query) return collegeData.value;

    return collegeData.value.filter(item =>
        item.college.toLowerCase().includes(query) ||
        item.department.toLowerCase().includes(query) ||
        item.dean.toLowerCase().includes(query)
    );
});

// --- Action Handlers (CRUD Functions) ---

// --- 1. View Function ---
const handleViewDetails = (item) => {
    currentItem.value = { ...item }; // Copy the data
    isViewModalOpen.value = true;
};

// --- 2. Edit Functions ---
const handleEditDetails = (item) => {
    currentItem.value = { ...item }; // Copy data to currentItem for editing
    isEditModalOpen.value = true;
};

const saveEdit = () => {
    if (currentItem.value) {
        // Find the index of the item being edited
        const index = collegeData.value.findIndex(d => d.id === currentItem.value.id);
        if (index !== -1) {
            // Update the data in the main array
            collegeData.value[index] = { ...currentItem.value };
            alert(`Successfully updated department: ${currentItem.value.department}`);
            isEditModalOpen.value = false;
            currentItem.value = null;
        }
    }
};

// --- 3. Delete Function ---
const handleDeleteDetails = (item) => {
    if (confirm(`Are you sure you want to delete the department: ${item.department} from ${item.college}?`)) {
        collegeData.value = collegeData.value.filter(d => d.id !== item.id);
        alert(`${item.department} has been deleted.`);
    }
};

// --- 4. Add Function ---
const openAddModal = () => {
    // Reset the form state and open the modal
    newDepartment.value = { college: '', department: '', dean: '' };
    isAddModalOpen.value = true;
};

const saveNewDepartment = () => {
    if (!newDepartment.value.college || !newDepartment.value.department || !newDepartment.value.dean) {
        alert('All fields are required!');
        return;
    }

    const newItem = {
        // Simple way to generate a unique ID
        id: Math.max(...collegeData.value.map(d => d.id), 0) + 1,
        ...newDepartment.value,
    };

    collegeData.value.push(newItem);
    alert(`Successfully added new department: ${newItem.department}`);

    // Close modal and reset form
    isAddModalOpen.value = false;
    newDepartment.value = { college: '', department: '', dean: '' };
};
</script>

<template>
    <div class="space-y-6">

        <div class="bg-white shadow rounded-lg p-4">
            <h6 class="font-bold text-xl text-[#7A0C23] mb-4 pb-2 border-b">Department List 📃</h6>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
                <div class="relative w-full sm:w-auto max-w-[400px]">
                    <input 
                        type="text" 
                        placeholder="Search College, Department, or Dean" 
                        v-model="searchTerm"
                        class="w-full p-3 pl-10 border-0 rounded-lg focus:ring-2 focus:ring-[#7A0C23] shadow-sm bg-gray-100"
                    >
                    <FontAwesomeIcon :icon="icons.search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                </div>

                <button 
                    @click="openAddModal"
                    class="flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md"
                >
                  
                    Add Department
                </button>
            </div>
            
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">College</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Dean</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="item in filteredCollegeData" :key="item.id" class="hover:bg-gray-50"> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.college }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.dean }}</td>
                           
                            <td class="px-6 py-4 whitespace-nowrap text-center text-lg space-x-3">
                                <button @click="handleViewDetails(item)" title="View Details"
                                    class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                </button>
                                <button @click="handleEditDetails(item)" title="Edit Department"
                                    class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                </button>
                                <button @click="handleDeleteDetails(item)" title="Delete Department"
                                    class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredCollegeData.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No college/department records found matching "{{ searchTerm }}".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="isViewModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 space-y-4">
                <h3 class="text-2xl font-bold text-[#7A0C23] border-b pb-2">View Details</h3>
                <div v-if="currentItem">
                    <p><strong>College:</strong> {{ currentItem.college }}</p>
                    <p><strong>Department:</strong> {{ currentItem.department }}</p>
                    <p><strong>Dean/Head:</strong> {{ currentItem.dean }}</p>
                </div>
                <button @click="isViewModalOpen = false" 
                    class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded transition duration-150">
                    Close
                </button>
            </div>
        </div>

        <div v-if="isEditModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 space-y-4">
                <h3 class="text-2xl font-bold text-green-600 border-b pb-2">Edit Department</h3>
                <div v-if="currentItem" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">College Name</label>
                        <input type="text" v-model="currentItem.college" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department Name</label>
                        <input type="text" v-model="currentItem.department" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dean/Head Name</label>
                        <input type="text" v-model="currentItem.dean" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button @click="isEditModalOpen = false" 
                        class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded transition duration-150">
                        Cancel
                    </button>
                    <button @click="saveEdit" 
                        class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-150">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        <div v-if="isAddModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 space-y-4">
                <h3 class="text-2xl font-bold text-[#7A0C23] border-b pb-2">Add New Department</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">College Name</label>
                        <input type="text" v-model="newDepartment.college" 
                               placeholder="e.g., College of Engineering"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department Name</label>
                        <input type="text" v-model="newDepartment.department" 
                               placeholder="e.g., Computer Engineering"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dean/Head Name</label>
                        <input type="text" v-model="newDepartment.dean" 
                               placeholder="e.g., Dr. Jane Doe"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button @click="isAddModalOpen = false" 
                        class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded transition duration-150">
                        Cancel
                    </button>
                    <button @click="saveNewDepartment" 
                        class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-150">
                        Add Department
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>