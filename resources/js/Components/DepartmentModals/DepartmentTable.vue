<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue';
import IconButton from '@/Components/IconButton.vue';

// Define props and emits
const props = defineProps({
  departments: {
    type: Array,
    required: true,
    default: () => []
  }
});

const emit = defineEmits(['created', 'edited', 'deleted']);

// --- Pagination State ---
const currentPage = ref(1);
const itemsPerPage = ref(5);

// --- Search State ---
const searchTerm = ref('');

// --- Modal State ---
const isViewModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isAddModalOpen = ref(false);
const currentItem = ref(null); // Used for View/Edit operations

// --- New Department Form State ---
const newDepartment = ref({
    college: '',
    department: '',
    dean: '',
});

// --- FILTERED DATA ---
const filteredCollegeData = computed(() => {
    const query = searchTerm.value.toLowerCase();
    if (!query) return props.departments;

    return props.departments.filter(item =>
        item.college.toLowerCase().includes(query) ||
        item.department.toLowerCase().includes(query) ||
        item.dean.toLowerCase().includes(query)
    );
});

// --- PAGINATED DATA ---
const paginatedDepartments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredCollegeData.value.slice(start, end);
});

// --- PAGINATION COMPUTED ---
const totalPages = computed(() => {
    return Math.ceil(filteredCollegeData.value.length / itemsPerPage.value);
});

const showingRange = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(currentPage.value * itemsPerPage.value, filteredCollegeData.value.length);
    const total = filteredCollegeData.value.length;
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

// --- Action Handlers (CRUD Functions) ---

// --- 1. View Function ---
const handleViewDetails = (item) => {
    currentItem.value = { ...item };
    isViewModalOpen.value = true;
};

// --- 2. Edit Functions ---
const handleEditDetails = (item) => {
    currentItem.value = { ...item };
    isEditModalOpen.value = true;
};

const saveEdit = () => {
    if (currentItem.value) {
        emit("edited", currentItem.value);
        isEditModalOpen.value = false;
        currentItem.value = null;
    }
};

// --- 3. Delete Function ---
const handleDeleteDetails = (item) => {
    if (confirm(`Are you sure you want to delete the department: ${item.department} from ${item.college}?`)) {
        emit("deleted", item.department);
    }
};

// --- 4. Add Function ---
const openAddModal = () => {
    newDepartment.value = { college: '', department: '', dean: '' };
    isAddModalOpen.value = true;
};

const saveNewDepartment = () => {
    if (!newDepartment.value.college || !newDepartment.value.department || !newDepartment.value.dean) {
        alert('All fields are required!');
        return;
    }

    emit("created", newDepartment.value);
    isAddModalOpen.value = false;
    newDepartment.value = { college: '', department: '', dean: '' };
};
</script>

<template>
    <div class="space-y-6">

        <div class="bg-white shadow rounded-lg p-4">
            <h6 class="font-bold text-xl text-[#7A0C23] mb-4 pb-2 border-b">Department List 📃</h6>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
                <div class=" relative w-full sm:w-auto max-w-[400px]">
                    <input
                        type="text"
                        placeholder="Search College, Department, or Dean"
                        v-model="searchTerm"
                        @input="resetPagination"
                        class="border-yellow-400 w-full p-3 pl-10 rounded-lg focus:ring-2 focus:ring-[#7A0C23] shadow-sm bg-gray-100"
                    >
                    <!-- Search Icon using IconButton -->
                    <IconButton
                        icon="search"
                        size="sm"
                        color="gray"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
                    />
                </div>

                <!-- Add Button using IconButton -->
                <IconButton
                    @click="openAddModal"
                    icon="plus"
                    title="Add Department"
                    size="sm"
                    color="green"
                    outlined
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150"
                >
                    Add Department
                </IconButton>
            </div>

            <div class="overflow-x-auto border rounded-lg border-yellow-400">
                <table class="min-w-full divide-y divide-yellow-300">
                    <thead class="bg-[#7A0C23] text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">College</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Dean</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-yellow-400 bg-white">
                        <tr v-for="item in paginatedDepartments" :key="item.id" class="hover:bg-gray-300">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.college }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.department }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.dean }}</td>

                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                                <!-- View Button using IconButton -->
                                <IconButton
                                    @click="handleViewDetails(item)"
                                    icon="eye"
                                    title="View Details"
                                    size="sm"
                                    color="blue"
                                    class="hover:scale-110 transition-transform"
                                />

                                <!-- Edit Button using IconButton -->
                                <IconButton
                                    @click="handleEditDetails(item)"
                                    icon="edit"
                                    title="Edit Department"
                                    size="sm"
                                    color="green"
                                    class="hover:scale-110 transition-transform"
                                />

                                <!-- Delete Button using IconButton -->
                                <IconButton
                                    @click="handleDeleteDetails(item)"
                                    icon="delete"
                                    title="Delete Department"
                                    size="sm"
                                    color="red"
                                    class="hover:scale-110 transition-transform"
                                />
                            </td>
                        </tr>
                        <tr v-if="filteredCollegeData.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No college/department records found matching "{{ searchTerm }}".
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- PAGINATION CONTROLS -->
                <div v-if="filteredCollegeData.length > 0" class="bg-gray-50 px-6 py-4 border-t border-yellow-400">
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
                            Filtered Results: <span class="font-semibold text-[#7A0C23]">{{ filteredCollegeData.length }}</span>
                            | Total Departments: <span class="font-semibold text-[#7A0C23]">{{ departments.length }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <Teleport to="body">
            <div v-if="isViewModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 space-y-4">
                    <h3 class="text-2xl font-bold text-[#7A0C23] border-b pb-2">View Department Details</h3>
                    <div v-if="currentItem">
                        <div class="space-y-3">
                            <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                <p class="text-sm text-gray-500 mb-1">College</p>
                                <p class="text-lg font-semibold text-gray-800">{{ currentItem.college }}</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded border border-green-100">
                                <p class="text-sm text-gray-500 mb-1">Department</p>
                                <p class="text-lg font-semibold text-gray-800">{{ currentItem.department }}</p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded border border-yellow-100">
                                <p class="text-sm text-gray-500 mb-1">Dean/Head</p>
                                <p class="text-lg font-semibold text-gray-800">{{ currentItem.dean }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <IconButton
                            @click="isViewModalOpen = false"
                            icon="times"
                            title="Close"
                            size="sm"
                            color="gray"
                            outlined
                            class="mt-4 bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded transition duration-150"
                        >
                            Close
                        </IconButton>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Edit Modal -->
        <Teleport to="body">
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
                        <IconButton
                            @click="isEditModalOpen = false"
                            icon="times"
                            title="Cancel"
                            size="sm"
                            color="gray"
                            outlined
                            class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded transition duration-150"
                        >
                            Cancel
                        </IconButton>

                        <IconButton
                            @click="saveEdit"
                            icon="check"
                            title="Save Changes"
                            size="sm"
                            color="green"
                            outlined
                            class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-150"
                        >
                            Save Changes
                        </IconButton>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Add Modal -->
        <Teleport to="body">
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
                        <IconButton
                            @click="isAddModalOpen = false"
                            icon="times"
                            title="Cancel"
                            size="sm"
                            color="gray"
                            outlined
                            class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded transition duration-150"
                        >
                            Cancel
                        </IconButton>

                        <IconButton
                            @click="saveNewDepartment"
                            icon="check"
                            title="Add Department"
                            size="sm"
                            color="green"
                            outlined
                            class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition duration-150"
                        >
                            Add Department
                        </IconButton>
                    </div>
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
</style>
