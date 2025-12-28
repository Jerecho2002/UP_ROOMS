<script setup>
import { ref, computed } from 'vue';
import IconButton from '@/Components/IconButton.vue';

// EMIT EVENTS TO PARENT
const emit = defineEmits(["created", "edited", "deleted"]);

// --- Reactive Data Structure ---
const colleges = ref([
    { id: 1, college: 'College of Information Technology', department: 'BS Information Technology', building: 'IT Building', description: 'Focuses on information technology and computer science programs.' },
    { id: 2, college: 'College of Engineering', department: 'BS Civil Engineering', building: 'Engineering Building', description: 'Offers various engineering disciplines with modern laboratories.' },
    { id: 3, college: 'College of Arts and Sciences', department: 'BS Psychology', building: 'Arts Building', description: 'Provides liberal arts, sciences, and social sciences programs.' },
    { id: 4, college: 'College of Business', department: 'BS Business Administration', building: 'Business Building', description: 'Focuses on business management, finance, and entrepreneurship.' },
    { id: 5, college: 'College of Medicine', department: 'Doctor of Medicine', building: 'Medical Building', description: 'Medical school with clinical training facilities.' },
    { id: 6, college: 'College of Law', department: 'Juris Doctor', building: 'Law Building', description: 'Law school with moot court and legal clinics.' },
    { id: 7, college: 'College of Education', department: 'BS Elementary Education', building: 'Education Building', description: 'Teacher education and educational leadership programs.' },
    { id: 8, college: 'College of Nursing', department: 'BS Nursing', building: 'Health Sciences Building', description: 'Nursing programs with simulation laboratories.' },
    { id: 9, college: 'College of Architecture', department: 'BS Architecture', building: 'Design Building', description: 'Architecture and design programs with studios.' },
    { id: 10, college: 'College of Pharmacy', department: 'BS Pharmacy', building: 'Science Building', description: 'Pharmacy programs with research laboratories.' },
]);

// --- Pagination State ---
const currentPage = ref(1);
const itemsPerPage = ref(5);

// --- Search State ---
const cficSearchTerm = ref('');

// --- Modal States ---
const showAddEditModal = ref(false);
const showViewModal = ref(false);
const currentCollege = ref(null);
const modalTitle = ref('Add New College');
const viewCollege = ref(null);

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

// --- PAGINATED DATA ---
const paginatedColleges = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredColleges.value.slice(start, end);
});

// --- PAGINATION COMPUTED ---
const totalPages = computed(() => {
    return Math.ceil(filteredColleges.value.length / itemsPerPage.value);
});

const showingRange = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(currentPage.value * itemsPerPage.value, filteredColleges.value.length);
    const total = filteredColleges.value.length;
    return { start, end, total };
});

// --- PAGINATION METHODS ---
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const resetPagination = () => {
    currentPage.value = 1;
};

// --- MODAL FUNCTIONS ---
const openAddModal = () => {
    currentCollege.value = { college: '', department: '', building: '', description: '' };
    modalTitle.value = 'Add New College';
    showAddEditModal.value = true;
};

const openEditModal = (collegeDetails) => {
    currentCollege.value = { ...collegeDetails };
    modalTitle.value = 'Edit College Details';
    showAddEditModal.value = true;
};

const openViewModal = (collegeDetails) => {
    viewCollege.value = { ...collegeDetails };
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewCollege.value = null;
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
            description: currentCollege.value.description || 'No description available.'
        });

        emit("created"); // FIRE TOAST EVENT

        // Reset pagination to show new entry if needed
        resetPagination();
    }

    showAddEditModal.value = false;
    currentCollege.value = null;
};

// --- DELETE FUNCTION ---
const handleDeleteDetails = (details) => {
    if (confirm(`Delete "${details.college}"?`)) {
        colleges.value = colleges.value.filter(c => c.id !== details.id);
        emit("deleted", details.college); // FIRE TOAST EVENT WITH NAME

        // Reset pagination if needed
        if (paginatedColleges.value.length === 0 && currentPage.value > 1) {
            prevPage();
        }
    }
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
                        @input="resetPagination"
                        class="border-yellow-400 max-w-[400px] p-3 pl-10 rounded-lg bg-gray-200 shadow-sm text-sm"
                    >
                    <!-- Search Icon using IconButton -->
                    <IconButton
                        icon="search"
                        size="sm"
                        color="gray"
                        class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                    />
                </div>

                <!-- ADD BUTTON using IconButton -->
                <IconButton
                    @click="openAddModal"
                    icon="plus"
                    title="Add College"
                    size="sm"
                    color="green"
                    outlined
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md"
                >
                    ADD COLLEGE
                </IconButton>
            </div>

            <div class="border-yellow-400 overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-yellow-400">
                    <thead class="bg-[#7A0C23] text-white text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">College</th>
                            <th class="px-6 py-3 text-left">Department</th>
                            <th class="px-6 py-3 text-left">Building</th>
                            <th class="px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-yellow-400">
                        <tr v-for="college in paginatedColleges" :key="college.id" class="hover:bg-gray-300">
                            <td class="px-6 py-4">{{ college.college }}</td>
                            <td class="px-6 py-4">{{ college.department }}</td>
                            <td class="px-6 py-4">{{ college.building }}</td>
                            <td class="px-6 py-4 text-center space-x-3">

                                <!-- View Button using IconButton -->
                                <IconButton
                                    @click="openViewModal(college)"
                                    icon="eye"
                                    title="View Details"
                                    size="sm"
                                    color="blue"
                                    class="hover:scale-110 transition-transform"
                                />

                                <!-- Edit Button using IconButton -->
                                <IconButton
                                    @click="openEditModal(college)"
                                    icon="edit"
                                    title="Edit College"
                                    size="sm"
                                    color="green"
                                    class="hover:scale-110 transition-transform"
                                />

                                <!-- Delete Button using IconButton -->
                                <IconButton
                                    @click="handleDeleteDetails(college)"
                                    icon="delete"
                                    title="Delete College"
                                    size="sm"
                                    color="red"
                                    class="hover:scale-110 transition-transform"
                                />

                            </td>
                        </tr>

                        <tr v-if="filteredColleges.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No colleges found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- PAGINATION CONTROLS -->
                <div v-if="filteredColleges.length > 0" class="bg-gray-50 px-6 py-4 border-t border-yellow-400">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">

                        <!-- Showing range -->
                        <div class="text-sm text-gray-600">
                            Showing {{ showingRange.start }} to {{ showingRange.end }} of {{ showingRange.total }} entries
                        </div>

                        <!-- Items per page selector -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Show:</span>
                            <select
                                v-model="itemsPerPage"
                                @change="resetPagination"
                                class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                            >
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-sm text-gray-600">per page</span>
                        </div>

                        <!-- Page navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous button using IconButton -->
                            <IconButton
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                icon="chevronLeft"
                                title="Previous Page"
                                size="sm"
                                color="gray"
                                outlined
                                :class="[
                                    'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150',
                                    currentPage === 1
                                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Previous
                            </IconButton>

                            <!-- Page numbers -->
                            <div class="flex items-center space-x-1">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px] transition-colors duration-150',
                                        currentPage === page
                                            ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <!-- Next button using IconButton -->
                            <IconButton
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                icon="chevronRight"
                                title="Next Page"
                                size="sm"
                                color="gray"
                                outlined
                                :class="[
                                    'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150',
                                    currentPage === totalPages
                                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Next
                            </IconButton>
                        </div>

                        <!-- Page indicator -->
                        <div class="text-sm text-gray-600">
                            Page {{ currentPage }} of {{ totalPages }}
                        </div>
                    </div>

                    <!-- Results summary -->
                    <div class="mt-4 pt-3 border-t border-gray-300 text-center">
                        <p class="text-sm text-gray-500">
                            Filtered Results: <span class="font-semibold text-[#7A0C23]">{{ filteredColleges.length }}</span>
                            | Total Colleges: <span class="font-semibold text-[#7A0C23]">{{ colleges.length }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODAL -->
        <Teleport to="body">
            <div v-if="showViewModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg max-h-[90vh] overflow-y-auto">
                    <!-- Close Button -->
                    <div class="flex justify-end mb-2">
                        <IconButton
                            @click="closeViewModal"
                            icon="times"
                            title="Close"
                            size="sm"
                            color="gray"
                            class="hover:bg-gray-100 rounded-full p-1"
                        />
                    </div>

                    <!-- Modal Title -->
                    <h3 class="text-xl font-bold mb-6 text-[#7A0C23] text-center border-b pb-3">
                        College Details 👁️
                    </h3>

                    <!-- College Information -->
                    <div v-if="viewCollege" class="space-y-6">
                        <!-- College Info Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-5 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-12 h-12 bg-[#7A0C23] rounded-full flex items-center justify-center">
                                    <span class="text-white text-lg font-bold">🎓</span>
                                </div>
                            </div>
                            <h4 class="text-xl font-bold text-center text-gray-800 mb-2">
                                {{ viewCollege.college }}
                            </h4>
                            <p class="text-gray-600 text-center text-sm mb-4">
                                College ID: <span class="font-semibold">#{{ viewCollege.id }}</span>
                            </p>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="text-sm font-semibold text-gray-500 mb-2">📚 DEPARTMENT</h5>
                                <p class="text-gray-800 font-medium">{{ viewCollege.department }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h5 class="text-sm font-semibold text-gray-500 mb-2">🏢 BUILDING</h5>
                                <p class="text-gray-800 font-medium">{{ viewCollege.building }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h5 class="text-sm font-semibold text-gray-500 mb-2 flex items-center">
                                <span class="mr-2">📝</span> DESCRIPTION
                            </h5>
                            <p class="text-gray-700 leading-relaxed">
                                {{ viewCollege.description }}
                            </p>
                        </div>

                        <!-- Additional Info -->
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h5 class="text-sm font-semibold text-gray-500 mb-3 flex items-center">
                                <span class="mr-2">ℹ️</span> COLLEGE INFORMATION
                            </h5>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-semibold text-green-600">Active</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Established:</span>
                                    <span class="font-semibold">2020</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Students:</span>
                                    <span class="font-semibold">1,500+</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-center space-x-4 pt-4 border-t border-gray-200">
                            <IconButton
                                @click="openEditModal(viewCollege)"
                                icon="edit"
                                title="Edit College"
                                size="sm"
                                color="green"
                                outlined
                                class="hover:scale-105 transition-transform"
                            >
                                Edit
                            </IconButton>


                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ADD/EDIT MODAL -->
        <Teleport to="body">
            <div v-if="showAddEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-lg">

                    <h3 class="text-xl font-bold mb-4 text-[#7A0C23]">{{ modalTitle }}</h3>

                    <form @submit.prevent="handleSaveCollege">

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">College Name</label>
                            <input v-model="currentCollege.college"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   required />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">Department</label>
                            <input v-model="currentCollege.department"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   required />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">Building</label>
                            <input v-model="currentCollege.building"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   required />
                        </div>

                        <div class="mb-6">
                            <label class="text-sm font-medium block mb-1">Description (Optional)</label>
                            <textarea v-model="currentCollege.description"
                                      rows="3"
                                      class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                      placeholder="Enter a brief description of the college..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <!-- Cancel button using IconButton -->
                            <IconButton
                                @click="showAddEditModal = false"
                                icon="times"
                                title="Cancel"
                                size="sm"
                                color="gray"
                                outlined
                                class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400"
                            >
                                Cancel
                            </IconButton>

                            <!-- Save button using IconButton -->
                            <IconButton
                                type="submit"
                                icon="check"
                                title="Save"
                                size="sm"
                                color="green"
                                outlined
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                            >
                                Save
                            </IconButton>
                        </div>

                    </form>

                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
/* Custom styles for pagination */
button:not(:disabled):hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Ensure pagination controls are properly spaced */
.space-x-1 > * + * {
    margin-left: 0.25rem;
}

.space-x-2 > * + * {
    margin-left: 0.5rem;
}

.space-x-3 > * + * {
    margin-left: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .flex-col.md\:flex-row {
        gap: 1rem;
    }

    .space-x-2 {
        justify-content: center;
    }
}

/* Custom scrollbar for modal */
.modal-scroll {
    max-height: calc(90vh - 100px);
    overflow-y: auto;
}

.modal-scroll::-webkit-scrollbar {
    width: 6px;
}

.modal-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-scroll::-webkit-scrollbar-thumb {
    background: #7A0C23;
    border-radius: 10px;
}

.modal-scroll::-webkit-scrollbar-thumb:hover {
    background: #5a061a;
}
</style>
