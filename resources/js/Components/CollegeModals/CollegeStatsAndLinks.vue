<script setup>
import { ref, computed } from 'vue';
import { faEye, faPenToSquare, faTrash, faSearch, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// EMIT EVENTS TO PARENT
const emit = defineEmits(["created", "edited", "deleted"]);

// --- Reactive Data Structure ---
const colleges = ref([
    { id: 1, college: 'College of Information Technology', department: 'BS Information Technology', building: 'IT Building' },
    { id: 2, college: 'College of Engineering', department: 'BS Civil Engineering', building: 'Engineering Building' },
    { id: 3, college: 'College of Arts and Sciences', department: 'BS Psychology', building: 'Arts Building' },
    { id: 4, college: 'College of Business', department: 'BS Business Administration', building: 'Business Building' },
]);

const cficSearchTerm = ref('');
const showAddEditModal = ref(false);
const currentCollege = ref(null);
const modalTitle = ref('Add New College');

const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    search: faSearch,
    add: faPlus,
};

// --- FILTERED SEARCH ---
const filteredColleges = computed(() => {
    const q = cficSearchTerm.value.toLowerCase();
    if (!q) return colleges.value;

    return colleges.value.filter(college =>
        college.college.toLowerCase().includes(q) ||
        college.department.toLowerCase().includes(q) ||
        college.building.toLowerCase().includes(q)
    );
});

// --- MODAL FUNCTIONS ---

const openAddModal = () => {
    currentCollege.value = { college: '', department: '', building: '' };
    modalTitle.value = 'Add New College';
    showAddEditModal.value = true;
};

const openEditModal = (collegeDetails) => {
    currentCollege.value = { ...collegeDetails };
    modalTitle.value = 'Edit College Details';
    showAddEditModal.value = true;
};

// --- SAVE: ADD or EDIT ---
const handleSaveCollege = () => {
    if (!currentCollege.value.college || !currentCollege.value.department || !currentCollege.value.building) {
        alert('Fill out all fields.');
        return;
    }

    if (currentCollege.value.id) {
        // EDIT
        const index = colleges.value.findIndex(c => c.id === currentCollege.value.id);
        if (index !== -1) {
            colleges.value[index] = { ...currentCollege.value };
        }

        emit("edited"); // FIRE TOAST EVENT
    } else {
        // ADD
        const newId = Math.max(...colleges.value.map(c => c.id)) + 1;

        colleges.value.push({
            id: newId,
            college: currentCollege.value.college,
            department: currentCollege.value.department,
            building: currentCollege.value.building,
        });

        emit("created"); // FIRE TOAST EVENT
    }

    showAddEditModal.value = false;
    currentCollege.value = null;
};

// --- DELETE FUNCTION ---
const handleDeleteDetails = (details) => {
    if (confirm(`Delete "${details.college}"?`)) {
        colleges.value = colleges.value.filter(c => c.id !== details.id);
        emit("deleted", details.college); // FIRE TOAST EVENT WITH NAME
    }
};

// --- VIEW DETAILS (TEMP) ---
const handleViewDetails = (details) => {
    alert(`
College: ${details.college}
Department: ${details.department}
Building: ${details.building}
    `);
};
</script>

<template>
    <div class="space-y-6">

        <!-- COLLEGE LIST -->
        <div class="bg-white shadow rounded-lg p-4">
            <h6 class="font-bold text-xl text-[#7A0C23] mb-4 border-b pb-2">College List 📃</h6>

            <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">

                <!-- SEARCH BAR -->
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search College, Department, or Building"
                        v-model="cficSearchTerm"
                        class="max-w-[400px] p-3 pl-10 rounded-lg bg-gray-200 shadow-sm text-sm"
                    >
                    <FontAwesomeIcon :icon="icons.search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                </div>

                <!-- ADD BUTTON -->
                <button @click="openAddModal"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                    ADD College
                </button>
            </div>

            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23] text-white text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">College</th>
                            <th class="px-6 py-3 text-left">Department</th>
                            <th class="px-6 py-3 text-left">Building</th>
                            <th class="px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="college in filteredColleges" :key="college.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ college.college }}</td>
                            <td class="px-6 py-4">{{ college.department }}</td>
                            <td class="px-6 py-4">{{ college.building }}</td>
                            <td class="px-6 py-4 text-center space-x-3">

                                <button @click="handleViewDetails(college)" class="text-blue-500 hover:text-blue-700">
                                    <FontAwesomeIcon :icon="icons.eye" class="h-5" />
                                </button>

                                <button @click="openEditModal(college)" class="text-green-600 hover:text-green-800">
                                    <FontAwesomeIcon :icon="icons.edit" class="h-5" />
                                </button>

                                <button @click="handleDeleteDetails(college)" class="text-red-600 hover:text-red-800">
                                    <FontAwesomeIcon :icon="icons.delete" class="h-5" />
                                </button>

                            </td>
                        </tr>

                        <tr v-if="filteredColleges.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No colleges found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL -->
        <Teleport to="body">
            <div v-if="showAddEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">

                    <h3 class="text-xl font-bold mb-4 text-[#7A0C23]">{{ modalTitle }}</h3>

                    <form @submit.prevent="handleSaveCollege">

                        <div class="mb-4">
                            <label class="text-sm font-medium">College Name</label>
                            <input v-model="currentCollege.college" class="w-full p-2 border rounded-md" required />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium">Department</label>
                            <input v-model="currentCollege.department" class="w-full p-2 border rounded-md" required />
                        </div>

                        <div class="mb-6">
                            <label class="text-sm font-medium">Building</label>
                            <input v-model="currentCollege.building" class="w-full p-2 border rounded-md" required />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showAddEditModal = false" class="px-4 py-2 bg-gray-300 rounded-md">
                                Cancel
                            </button>

                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">
                                Save
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </Teleport>
    </div>
</template>
