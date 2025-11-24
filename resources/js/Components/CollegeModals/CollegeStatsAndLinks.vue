<script setup>
import { ref, computed } from 'vue';
import { faEye, faPenToSquare, faTrash, faSearch, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// --- Reactive Data Structure & State 💾 ---

// 1. Core Data: Reactive list of college records
const colleges = ref([
    { id: 1, college: 'College of Information Technology', department: 'BS Information Technology', building: 'IT Building' },
    { id: 2, college: 'College of Engineering', department: 'BS Civil Engineering', building: 'Engineering Building' },
    { id: 3, college: 'College of Arts and Sciences', department: 'BS Psychology', building: 'Arts Building' },
    { id: 4, college: 'College of Business', department: 'BS Business Administration', building: 'Business Building' },
]);

// 2. UI State for Search and Modals
const cficSearchTerm = ref('');
const showAddEditModal = ref(false);
// currentCollege holds the data for the item being viewed/edited, or the new item being added.
const currentCollege = ref(null); 
const modalTitle = ref('Add New College');

// FontAwesome Icons
const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    search: faSearch,
    add: faPlus,
};

// --- Computed Property for Filtering 🔍 ---

const filteredColleges = computed(() => {
    const query = cficSearchTerm.value.toLowerCase();
    if (!query) return colleges.value;

    return colleges.value.filter(college =>
        college.college.toLowerCase().includes(query) ||
        college.department.toLowerCase().includes(query) ||
        college.building.toLowerCase().includes(query)
    );
});

// --- Action Handlers (CRUD Functions) ⚙️ ---

/**
 * Opens the modal configured for 'Add' operation.
 * Initializes currentCollege with empty fields.
 */
const openAddModal = () => {
    // Initialize with an empty object (no ID yet)
    currentCollege.value = { college: '', department: '', building: '' };
    modalTitle.value = 'Add New College';
    showAddEditModal.value = true;
};

/**
 * Opens the modal configured for 'Edit' operation.
 * @param {object} collegeDetails - The data object of the college to edit.
 */
const openEditModal = (collegeDetails) => {
    // IMPORTANT: Deep copy the object to prevent real-time array mutation during editing.
    currentCollege.value = { ...collegeDetails };
    modalTitle.value = 'Edit College Details';
    showAddEditModal.value = true;
};

/**
 * Handles the form submission for both adding a new college and editing an existing one.
 */
const handleSaveCollege = () => {
    if (!currentCollege.value.college || !currentCollege.value.department || !currentCollege.value.building) {
        alert('All fields are required!');
        return;
    }

    if (currentCollege.value.id) {
        // --- ✏️ EDIT Logic (If ID exists) ---
        const index = colleges.value.findIndex(c => c.id === currentCollege.value.id);
        if (index !== -1) {
            // Replace the old object with the updated currentCollege data
            colleges.value[index] = { ...currentCollege.value }; 
        }
        alert(`College "${currentCollege.value.college}" updated successfully!`);
    } else {
        // --- ✨ ADD Logic (If no ID exists) ---
        // 1. Calculate a new unique ID
        const maxId = colleges.value.length ? Math.max(...colleges.value.map(c => c.id)) : 0;
        const newId = maxId + 1;

        // 2. Add the new object to the reactive array
        colleges.value.push({
            id: newId,
            college: currentCollege.value.college,
            department: currentCollege.value.department,
            building: currentCollege.value.building,
        });
        alert(`College "${currentCollege.value.college}" added successfully!`);
    }

    // Close and reset state
    showAddEditModal.value = false;
    currentCollege.value = null;
};

/**
 * Handles View Details (using a simple native alert for placeholder).
 * @param {object} details - The college data to view.
 */
const handleViewDetails = (details) => {
    alert(`
        Viewing Details:
        College: ${details.college}
        Department: ${details.department}
        Building: ${details.building}
    `);
};

/**
 * Handles Delete operation.
 * @param {object} details - The college data to delete.
 */
const handleDeleteDetails = (details) => {
    if (confirm(`Are you sure you want to delete the college: ${details.college} (${details.department})?`)) {
        // Filter out the item with the matching ID
        colleges.value = colleges.value.filter(c => c.id !== details.id);
        alert(`${details.college} has been deleted.`);
    }
};
</script>

<template>
    <div class="space-y-6">

        <div class="bg-white shadow rounded-lg p-4">
            <h6 class="font-bold text-xl text-[#7A0C23] mb-4 border-b pb-2">College List 📃</h6>

            <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                
                <div class="relative w-full sm:w-auto">
                    <input 
                        type="text" 
                        placeholder="Search College, Department, or Building" 
                        v-model="cficSearchTerm"
                        class="max-w-[400px] p-3 pl-10 border-0 rounded-lg focus:ring-0 shadow-sm bg-gray-200 text-sm w-full"
                    >
                    <FontAwesomeIcon :icon="icons.search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                </div>
                
                <button @click="openAddModal"
                    class="flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 shadow-md">
                   
                    ADD College
                </button>
            </div>
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">College</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Building</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="college in filteredColleges" :key="college.id" class="hover:bg-gray-50"> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ college.college }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ college.department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ college.building }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-lg space-x-3">
                                <button @click="handleViewDetails(college)" title="View Details"
                                    class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                </button>
                                <button @click="openEditModal(college)" title="Edit College"
                                    class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                </button>
                                <button @click="handleDeleteDetails(college)" title="Delete College"
                                    class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                    <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredColleges.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No colleges found matching "{{ cficSearchTerm }}".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        
        <Teleport to="body">
            <div v-if="showAddEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
                    <h3 class="text-xl font-bold mb-4 text-[#7A0C23]">{{ modalTitle }}</h3>
                    
                    <form @submit.prevent="handleSaveCollege">
                        
                        <div class="mb-4">
                            <label for="collegeName" class="block text-sm font-medium text-gray-700">College Name</label>
                            <input type="text" id="collegeName" v-model="currentCollege.college" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2" />
                        </div>

                        <div class="mb-4">
                            <label for="departmentName" class="block text-sm font-medium text-gray-700">Department</label>
                            <input type="text" id="departmentName" v-model="currentCollege.department" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2" />
                        </div>
                        
                        <div class="mb-6">
                            <label for="buildingName" class="block text-sm font-medium text-gray-700">Building</label>
                            <input type="text" id="buildingName" v-model="currentCollege.building" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2" />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showAddEditModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
        </div>
</template>