<script setup>
import { ref, computed, defineEmits } from 'vue'
import IconButton from '@/Components/IconButton.vue'

// Emit events for toast notifications
const emit = defineEmits(['status-updated', 'record-deleted', 'record-created', 'record-edited', 'error'])

// --- Mock Data: Simulates the records fetched from an API ---
const mockRecordsData = [
    { id: 1, name: 'MID TERM', status: 'NotActive', startDate: '2024-09-15', endDate: '2026-09-15' },
    { id: 2, name: 'FINALS', status: 'active', startDate: '2025-01-15', endDate: '2027-01-15' },
    { id: 3, name: 'SUMMER SEMESTER', status: 'NotActive', startDate: '2025-06-01', endDate: '2025-07-31' },
    { id: 4, name: 'ACADEMIC YEAR 2026', status: 'active', startDate: '2026-09-01', endDate: '2027-05-30' },
    { id: 5, name: 'FIRST SEMESTER 2025', status: 'active', startDate: '2025-08-01', endDate: '2025-12-15' },
    { id: 6, name: 'SECOND SEMESTER 2025', status: 'NotActive', startDate: '2026-01-15', endDate: '2026-05-30' },
    { id: 7, name: 'MID YEAR TERM', status: 'active', startDate: '2025-05-01', endDate: '2025-06-30' },
    { id: 8, name: 'ANNUAL TERM', status: 'NotActive', startDate: '2026-01-01', endDate: '2026-12-31' },
    { id: 9, name: 'WINTER TERM', status: 'active', startDate: '2025-11-01', endDate: '2026-02-28' },
    { id: 10, name: 'SPRING TERM', status: 'active', startDate: '2026-03-01', endDate: '2026-05-31' },
    { id: 11, name: 'FALL TERM', status: 'NotActive', startDate: '2026-08-01', endDate: '2026-12-15' },
    { id: 12, name: 'QUARTERLY TERM 1', status: 'active', startDate: '2025-01-01', endDate: '2025-03-31' },
    { id: 13, name: 'QUARTERLY TERM 2', status: 'NotActive', startDate: '2025-04-01', endDate: '2025-06-30' },
    { id: 14, name: 'QUARTERLY TERM 3', status: 'active', startDate: '2025-07-01', endDate: '2025-09-30' },
    { id: 15, name: 'QUARTERLY TERM 4', status: 'NotActive', startDate: '2025-10-01', endDate: '2025-12-31' },
];
// --------------------------------------------------------

const records = ref(mockRecordsData);

// --- Pagination State ---
const currentPage = ref(1);
const itemsPerPage = ref(5);

// --- Search State ---
const searchTerm = ref('');

// --- Modal State ---
const isStatusModalOpen = ref(false);
const recordToUpdate = ref(null); // The record whose status we are changing

// --- Add/Edit Modal State ---
const isAddEditModalOpen = ref(false);
const isAddMode = ref(true);
const editingRecord = ref(null);

// --- Delete Modal State (Custom Confirmation) ---
const isDeleteModalOpen = ref(false);
const recordToDelete = ref(null);

// --- New Record Form State ---
const newRecord = ref({
    name: '',
    startDate: '',
    endDate: '',
    status: 'NotActive'
});

// --- Status Options Mapping (for the modal) ---
const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'NotActive', label: 'Not Active' },
];

// --- Filtered Records ---
const filteredRecords = computed(() => {
    const query = searchTerm.value.toLowerCase();
    if (!query) return records.value;

    return records.value.filter(record =>
        record.name.toLowerCase().includes(query) ||
        record.status.toLowerCase().includes(query) ||
        record.startDate.includes(query) ||
        record.endDate.includes(query)
    );
});

// --- Paginated Records ---
const paginatedRecords = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredRecords.value.slice(start, end);
});

// --- Pagination Computed ---
const totalPages = computed(() => {
    return Math.ceil(filteredRecords.value.length / itemsPerPage.value);
});

const showingRange = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(currentPage.value * itemsPerPage.value, filteredRecords.value.length);
    const total = filteredRecords.value.length;
    return { start, end, total };
});

// --- Pagination Methods ---
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

// --- Status Display Functions ---
const getStatusDisplay = (status) => {
    switch (status) {
        case 'active':
            return {
                text: 'Active',
                class: 'bg-green-100 text-green-800 border border-green-400 hover:bg-green-200'
            };
        case 'NotActive':
            return {
                text: 'Not Active',
                class: 'bg-red-100 text-red-800 border border-red-400 hover:bg-red-200'
            };
        default:
            return {
                text: 'Unknown',
                class: 'bg-gray-100 text-gray-800 border border-gray-400 hover:bg-gray-200'
            };
    }
};

// --- CRUD Operations ---

// Open Add Modal
const openAddModal = () => {
    isAddMode.value = true;
    newRecord.value = {
        name: '',
        startDate: '',
        endDate: '',
        status: 'NotActive'
    };
    isAddEditModalOpen.value = true;
};

// Open Edit Modal
const openEditModal = (record) => {
    isAddMode.value = false;
    editingRecord.value = { ...record };
    isAddEditModalOpen.value = true;
};

// Save Record (Add or Edit)
const saveRecord = () => {
    if (isAddMode.value) {
        // Add new record
        if (!newRecord.value.name || !newRecord.value.startDate || !newRecord.value.endDate) {
            emit('error', 'All fields are required');
            return;
        }

        const newId = records.value.length > 0 ? Math.max(...records.value.map(r => r.id)) + 1 : 1;
        records.value.push({
            id: newId,
            ...newRecord.value
        });
        emit('record-created', newRecord.value.name);
    } else {
        // Edit existing record
        if (!editingRecord.value.name || !editingRecord.value.startDate || !editingRecord.value.endDate) {
            emit('error', 'All fields are required');
            return;
        }

        const index = records.value.findIndex(r => r.id === editingRecord.value.id);
        if (index !== -1) {
            records.value[index] = { ...editingRecord.value };
            emit('record-edited', editingRecord.value.name);
        }
    }

    isAddEditModalOpen.value = false;
    resetPagination();
};

// --- Status Modal Logic ---
const openStatusModal = (record) => {
    recordToUpdate.value = record;
    isStatusModalOpen.value = true;
};

const closeStatusModal = () => {
    isStatusModalOpen.value = false;
    recordToUpdate.value = null;
};

const updateStatus = (newStatusValue) => {
    if (recordToUpdate.value) {
        const index = records.value.findIndex(r => r.id === recordToUpdate.value.id);
        if (index !== -1) {
            const oldStatus = records.value[index].status;
            records.value[index].status = newStatusValue;

            // Emit status updated event
            emit('status-updated', {
                name: recordToUpdate.value.name,
                oldStatus: oldStatus,
                newStatus: newStatusValue
            });
        }
    }
    closeStatusModal();
};

// --- Delete Confirmation Logic ---
const openDeleteModal = (record) => {
    recordToDelete.value = record;
    isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    recordToDelete.value = null;
};

const confirmDelete = () => {
    if (recordToDelete.value) {
        const recordName = recordToDelete.value.name;
        records.value = records.value.filter(record => record.id !== recordToDelete.value.id);
        emit('record-deleted', recordName);

        // Reset pagination if needed
        if (paginatedRecords.value.length === 0 && currentPage.value > 1) {
            prevPage();
        }
    }
    closeDeleteModal();
};

// Reset pagination when searching
const handleSearch = () => {
    resetPagination();
};

// Helper computed for form binding
const formRecord = computed(() => {
    return isAddMode.value ? newRecord.value : editingRecord.value;
});
</script>

<template>
    <div class="mb-6 p-6 bg-white rounded-xl shadow-lg">
        <!-- Search and Add Controls -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 space-y-3 md:space-y-0">
            <!-- Search Box -->
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <IconButton
                        icon="search"
                        title="Search records"
                        size="sm"
                        disabled
                    />
                </div>
                <input
                    type="text"
                    v-model="searchTerm"
                    @input="handleSearch"
                    placeholder="Search by name, status, or date..."
                    class="pl-10 pr-4 py-2 w-full border border-yellow-300 rounded-lg focus:ring-2 focus:ring-[#850038] focus:border-transparent outline-none bg-white shadow-sm"
                />
            </div>

            <!-- Add Button -->
            <IconButton
                @click="openAddModal"
                icon="plus"
                title="Add New Term"
                size="sm"
                color="green"
                outlined
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150"
            >
                Add New Term
            </IconButton>
        </div>

        <!-- Records Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-yellow-300">
                <thead class="bg-[#850038] text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            RECORD LIST
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            START DATE
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            END DATE
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            STATUS
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            ACTIONS
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-yellow-400">
                    <tr v-for="record in paginatedRecords" :key="record.id" class="hover:bg-gray-300 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ record.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ record.startDate }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ record.endDate }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <!-- Clickable Status Badge -->
                            <span
                                @click="openStatusModal(record)"
                                :class="[
                                    'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer transition duration-150',
                                    getStatusDisplay(record.status).class
                                ]"
                                title="Click to change status"
                            >
                                {{ getStatusDisplay(record.status).text }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <!-- Edit Button -->
                                <IconButton
                                    @click="openEditModal(record)"
                                    icon="edit"
                                    title="Edit Term"
                                    size="sm"
                                    color="green"
                                    class="hover:scale-110 transition-transform"
                                />

                                <!-- Delete Button -->
                                <IconButton
                                    @click="openDeleteModal(record)"
                                    icon="delete"
                                    title="Delete Term"
                                    size="sm"
                                    color="red"
                                    class="hover:scale-110 transition-transform"
                                />
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr v-if="filteredRecords.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <IconButton
                                    icon="search"
                                    title="No Results"
                                    size="lg"
                                    disabled
                                    class="mx-auto mb-3 opacity-50"
                                />
                                <p class="text-base font-medium mb-1">No terms found</p>
                                <p class="text-sm mb-4">Try adjusting your search or add a new term</p>
                                <IconButton
                                    @click="openAddModal"
                                    icon="plus"
                                    title="Add New Term"
                                    size="sm"
                                    color="green"
                                    outlined
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md"
                                >
                                    Add New Term
                                </IconButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="filteredRecords.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200 mt-4">
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
                        class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#850038] focus:border-transparent"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                    <span class="text-sm text-gray-600">per page</span>
                </div>

                <!-- Page navigation -->
                <div class="flex items-center space-x-2">
                    <!-- Previous button -->
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
                                    ? 'bg-[#850038] text-white border-[#850038]'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <!-- Next button -->
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
                    Filtered Results: <span class="font-semibold text-[#850038]">{{ filteredRecords.length }}</span>
                    | Total Terms: <span class="font-semibold text-[#850038]">{{ records.length }}</span>
                </p>
            </div>
        </div>

        <!-- MODALS -->

        <!-- Status Change Modal -->
        <div v-if="isStatusModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click.self="closeStatusModal">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all duration-300 scale-100" @click.stop>
                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4">Change Status for: {{ recordToUpdate?.name }}</h3>

                <div class="space-y-3">
                    <button
                        v-for="option in statusOptions"
                        :key="option.value"
                        @click="updateStatus(option.value)"
                        :class="[
                            'w-full text-center py-2 px-4 rounded-lg font-medium transition-colors',
                            getStatusDisplay(option.value).class.replace('cursor-pointer', '').replace('hover:bg-green-200', 'hover:bg-green-300 hover:text-green-900').replace('hover:bg-red-200', 'hover:bg-red-300 hover:text-red-900'),
                            recordToUpdate?.status === option.value ? 'ring-2 ring-offset-2 ring-[#850038]' : 'hover:shadow-md'
                        ]"
                    >
                        Set to {{ option.label }}
                    </button>
                </div>

                <div class="mt-6 pt-4 border-t flex justify-end">
                    <IconButton
                        @click="closeStatusModal"
                        icon="times"
                        title="Cancel"
                        size="sm"
                        color="gray"
                        outlined
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition"
                    >
                        Cancel
                    </IconButton>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="isAddEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click.self="isAddEditModalOpen = false">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md transform transition-all duration-300 scale-100" @click.stop>
                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4">
                    {{ isAddMode ? 'Add New Term' : 'Edit Term' }}
                </h3>

                <form @submit.prevent="saveRecord" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Term Name</label>
                        <input
                            type="text"
                            v-model="formRecord.name"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#850038] focus:border-transparent outline-none"
                            placeholder="e.g., MID TERM 2025"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input
                                type="date"
                                v-model="formRecord.startDate"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#850038] focus:border-transparent outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input
                                type="date"
                                v-model="formRecord.endDate"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#850038] focus:border-transparent outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select
                            v-model="formRecord.status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#850038] focus:border-transparent outline-none"
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                        <IconButton
                            type="button"
                            @click="isAddEditModalOpen = false"
                            icon="times"
                            title="Cancel"
                            size="sm"
                            color="gray"
                            outlined
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition"
                        >
                            Cancel
                        </IconButton>

                        <IconButton
                            type="submit"
                            icon="check"
                            :title="isAddMode ? 'Add Term' : 'Save Changes'"
                            size="sm"
                            color="green"
                            outlined
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition"
                        >
                            {{ isAddMode ? 'Add Term' : 'Save Changes' }}
                        </IconButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click.self="closeDeleteModal">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all duration-300 scale-100" @click.stop>
                <h3 class="text-xl font-semibold text-red-700 border-b pb-2 mb-4">Confirm Deletion</h3>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-5">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <IconButton
                                icon="warning"
                                title="Warning"
                                size="sm"
                                disabled
                            />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Delete <span class="font-semibold">"{{ recordToDelete?.name }}"</span>?
                                <span class="block text-yellow-600 text-xs mt-1">This action cannot be undone.</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <IconButton
                        @click="closeDeleteModal"
                        icon="times"
                        title="Cancel"
                        size="sm"
                        color="gray"
                        outlined
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition"
                    >
                        Cancel
                    </IconButton>

                    <IconButton
                        @click="confirmDelete"
                        icon="delete"
                        title="Delete"
                        color="white"
                        size="sm"
                        outlined
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition shadow-md"
                    >
                        Delete
                    </IconButton>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Modal Transition Styles */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

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
