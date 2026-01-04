<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import IconButton from '@/Components/IconButton.vue';

// EMIT EVENTS TO PARENT
const emit = defineEmits(["created", "edited", "deleted"]);

// Get initial data from Inertia
const page = usePage();
const initialColleges = page.props.initialColleges || [];

// --- Reactive Data Structure ---
const colleges = ref(initialColleges);

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
const isLoading = ref(false);

// --- Load colleges from API ---
const loadColleges = async () => {
    try {
        isLoading.value = true;
        const response = await fetch('/api/colleges');
        const data = await response.json();

        if (data.success) {
            colleges.value = data.data;
        } else {
            console.error('Failed to load colleges:', data.message);
        }
    } catch (error) {
        console.error('Error loading colleges:', error);
        // Fallback to initial data
        colleges.value = initialColleges;
    } finally {
        isLoading.value = false;
    }
};

// Initialize on component mount
onMounted(() => {
    loadColleges();
});

// --- FILTERED SEARCH ---
const filteredColleges = computed(() => {
    const q = cficSearchTerm.value.toLowerCase();
    if (!q) return colleges.value;

    return colleges.value.filter(college =>
        college.college.toLowerCase().includes(q) ||
        (college.department && college.department.toLowerCase().includes(q)) ||
        (college.building && college.building.toLowerCase().includes(q)) ||
        (college.college_code && college.college_code.toLowerCase().includes(q))
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

// Calculate visible page buttons with ellipsis
const visiblePages = computed(() => {
    const total = totalPages.value;
    const current = currentPage.value;
    const delta = 2; // Number of pages to show on each side of current page
    const range = [];

    if (total <= 7) {
        // Show all pages if total pages is 7 or less
        for (let i = 1; i <= total; i++) {
            range.push(i);
        }
    } else {
        // Always show first page
        range.push(1);

        // Calculate start and end of middle range
        let start = Math.max(2, current - delta);
        let end = Math.min(total - 1, current + delta);

        // Adjust if we're near the beginning
        if (current <= delta + 2) {
            end = delta * 2 + 2;
        }

        // Adjust if we're near the end
        if (current >= total - delta - 1) {
            start = total - delta * 2 - 1;
        }

        // Add ellipsis after first page if needed
        if (start > 2) {
            range.push('...');
        }

        // Add middle pages
        for (let i = start; i <= end; i++) {
            if (i > 1 && i < total) {
                range.push(i);
            }
        }

        // Add ellipsis before last page if needed
        if (end < total - 1) {
            range.push('...');
        }

        // Always show last page
        if (total > 1) {
            range.push(total);
        }
    }

    return range;
});

// --- MODAL FUNCTIONS ---
const openAddModal = () => {
    currentCollege.value = {
        college: '',
        college_code: '',
        department: '',
        building: '',
        description: '',
        contact_email: '',
        contact_phone: ''
    };
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
const handleSaveCollege = async () => {
    if (!currentCollege.value.college) {
        alert('College name is required.');
        return;
    }

    try {
        isLoading.value = true;
        const isEdit = !!currentCollege.value.id;
        const url = isEdit ? `/api/colleges/${currentCollege.value.id}` : '/api/colleges';
        const method = isEdit ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(currentCollege.value)
        });

        const data = await response.json();

        if (data.success) {
            if (isEdit) {
                // Update the college in the list
                const index = colleges.value.findIndex(c => c.id === data.data.id);
                if (index !== -1) {
                    colleges.value[index] = data.data;
                }
                emit("edited");
            } else {
                // Add new college to the list
                colleges.value.unshift(data.data);
                emit("created");
                resetPagination();
            }

            showAddEditModal.value = false;
            currentCollege.value = null;
        } else {
            alert(data.message || 'Failed to save college. Please check your input.');
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    } catch (error) {
        console.error('Error saving college:', error);
        alert('An error occurred while saving the college.');
    } finally {
        isLoading.value = false;
    }
};

// --- DELETE FUNCTION ---
const handleDeleteDetails = async (details) => {
    if (!confirm(`Are you sure you want to delete "${details.college}"?`)) {
        return;
    }

    try {
        isLoading.value = true;
        const response = await fetch(`/api/colleges/${details.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            colleges.value = colleges.value.filter(c => c.id !== details.id);
            emit("deleted", details.college);

            // Reset pagination if needed
            if (paginatedColleges.value.length === 0 && currentPage.value > 1) {
                prevPage();
            }
        } else {
            alert(data.message || 'Failed to delete college.');
        }
    } catch (error) {
        console.error('Error deleting college:', error);
        alert('An error occurred while deleting the college.');
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Loading overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#7A0C23] mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading...</p>
            </div>
        </div>

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
                                    'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150 min-w-[80px]',
                                    currentPage === 1
                                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Previous
                            </IconButton>

                            <!-- Page numbers with ellipsis -->
                            <div class="flex items-center space-x-1 overflow-hidden">
                                <button
                                    v-for="pageNum in visiblePages"
                                    :key="pageNum"
                                    @click="pageNum !== '...' ? goToPage(pageNum) : null"
                                    :disabled="pageNum === '...'"
                                    :class="[
                                        'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px] transition-colors duration-150',
                                        pageNum === '...'
                                            ? 'bg-transparent border-transparent text-gray-500 cursor-default'
                                            : currentPage === pageNum
                                                ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ pageNum }}
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
                                    'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150 min-w-[80px]',
                                    currentPage === totalPages
                                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Next
                            </IconButton>
                        </div>

                        <!-- Page indicator -->
                        <div class="text-sm text-gray-600 whitespace-nowrap">
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
                                <span v-if="viewCollege.college_code" class="ml-2">
                                    (Code: {{ viewCollege.college_code }})
                                </span>
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

                        <!-- Contact Information -->
                        <div v-if="viewCollege.contact_email || viewCollege.contact_phone"
                             class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h5 class="text-sm font-semibold text-gray-500 mb-2 flex items-center">
                                <span class="mr-2">📞</span> CONTACT INFORMATION
                            </h5>
                            <div class="space-y-2">
                                <div v-if="viewCollege.contact_email" class="flex items-center">
                                    <span class="text-gray-600 mr-2">Email:</span>
                                    <a :href="`mailto:${viewCollege.contact_email}`"
                                       class="font-semibold text-purple-600 hover:underline">
                                        {{ viewCollege.contact_email }}
                                    </a>
                                </div>
                                <div v-if="viewCollege.contact_phone" class="flex items-center">
                                    <span class="text-gray-600 mr-2">Phone:</span>
                                    <span class="font-semibold">{{ viewCollege.contact_phone }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h5 class="text-sm font-semibold text-gray-500 mb-2 flex items-center">
                                <span class="mr-2">📝</span> DESCRIPTION
                            </h5>
                            <p class="text-gray-700 leading-relaxed">
                                {{ viewCollege.description || 'No description available.' }}
                            </p>
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
                            <label class="text-sm font-medium block mb-1">College Name *</label>
                            <input v-model="currentCollege.college"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   required />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">College Code</label>
                            <input v-model="currentCollege.college_code"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   placeholder="e.g., CIT, COE, CAS" />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">Department</label>
                            <input v-model="currentCollege.department"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   placeholder="Main department of the college" />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">Building</label>
                            <input v-model="currentCollege.building"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   placeholder="Main building of the college" />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">Contact Email</label>
                            <input v-model="currentCollege.contact_email"
                                   type="email"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   placeholder="college@upcebu.edu.ph" />
                        </div>

                        <div class="mb-4">
                            <label class="text-sm font-medium block mb-1">Contact Phone</label>
                            <input v-model="currentCollege.contact_phone"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                                   placeholder="+63 XXX XXX XXXX" />
                        </div>

                        <div class="mb-6">
                            <label class="text-sm font-medium block mb-1">Description</label>
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
                                :disabled="isLoading"
                                :class="[
                                    'px-4 py-2 text-white rounded-md',
                                    isLoading
                                        ? 'bg-green-400 cursor-not-allowed'
                                        : 'bg-green-600 hover:bg-green-700'
                                ]"
                            >
                                {{ isLoading ? 'Saving...' : 'Save' }}
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

/* Ensure table maintains its width */
table {
    table-layout: fixed;
    width: 100%;
}

/* Fixed column widths */
th:nth-child(1), td:nth-child(1) { width: 30%; }
th:nth-child(2), td:nth-child(2) { width: 30%; }
th:nth-child(3), td:nth-child(3) { width: 20%; }
th:nth-child(4), td:nth-child(4) { width: 20%; }

/* Prevent pagination from breaking layout */
.flex.items-center.space-x-1.overflow-hidden {
    max-width: 300px;
    flex-wrap: nowrap;
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .flex-col.md\:flex-row {
        gap: 1rem;
    }

    .space-x-2 {
        justify-content: center;
    }

    /* Adjust table for mobile */
    th, td {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }

    .px-6 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    /* Adjust pagination for mobile */
    .flex.items-center.space-x-1.overflow-hidden {
        max-width: 200px;
    }

    .px-3 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Prevent content overflow */
td {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Ensure table border stays fixed */
.border-yellow-400 {
    border-width: 1px;
}

/* Pagination button ellipsis styling */
button[disabled].bg-transparent {
    background-color: transparent !important;
    border-color: transparent !important;
    cursor: default;
}
</style>
