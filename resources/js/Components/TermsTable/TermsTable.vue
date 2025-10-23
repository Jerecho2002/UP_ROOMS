<script setup>
import { ref, computed } from 'vue'

// --- Mock Data: Simulates the records fetched from an API ---
const mockRecordsData = [
    { id: 1, name: 'MID TERM', status: 'NotActive', startDate: '2024-09-15', endDate: '2026-09-15' },
    { id: 2, name: 'FINALS', status: 'active', startDate: '2025-01-15', endDate: '2027-01-15' },
    { id: 3, name: 'SUMMER SEMESTER', status: 'NotActive', startDate: '2025-06-01', endDate: '2025-07-31' },
    { id: 4, name: 'ACADEMIC YEAR 2026', status: 'active', startDate: '2026-09-01', endDate: '2027-05-30' },
];
// --------------------------------------------------------

const records = ref(mockRecordsData);

// --- Status Modal State ---
const isStatusModalOpen = ref(false);
const recordToUpdate = ref(null); // The record whose status we are changing

// --- Delete Modal State (Custom Confirmation) ---
const isDeleteModalOpen = ref(false);
const recordToDelete = ref(null);

// --- Status Options Mapping (for the modal) ---
const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'NotActive', label: 'Not Active' },
    
];

// Function to determine the display class and text for the status
const getStatusDisplay = (status) => {
    switch (status) {
        case 'active':
            return {
                text: 'Active',
                // Uses a light green background and darker text/border
                class: 'bg-green-100 text-green-800 border border-green-400 hover:bg-green-200'
            };
        case 'NotActive':
            return {
                text: 'Not Active',
                // Uses a light red background and darker text/border
                class: 'bg-red-100 text-red-800 border border-red-400 hover:bg-red-200'
            };
       
        default:
            return {
                text: 'Unknown',
                class: 'bg-gray-100 text-gray-800 border border-gray-400 hover:bg-gray-200'
            };
    }
};

// --- Status Modal Logic ---

/** Opens the status change modal for a specific record. */
const openStatusModal = (record) => {
    recordToUpdate.value = record;
    isStatusModalOpen.value = true;
};

/** Closes the status change modal and resets the state. */
const closeStatusModal = () => {
    isStatusModalOpen.value = false;
    recordToUpdate.value = null;
};

/** Updates the status of the selected record. */
const updateStatus = (newStatusValue) => {
    if (recordToUpdate.value) {
        const index = records.value.findIndex(r => r.id === recordToUpdate.value.id);
        if (index !== -1) {
            records.value[index].status = newStatusValue;
        }
    }
    closeStatusModal();
};

// --- Delete Confirmation Logic (Replacing 'confirm()') ---

/** Opens the custom delete confirmation modal. */
const openDeleteModal = (record) => {
    recordToDelete.value = record;
    isDeleteModalOpen.value = true;
};

/** Closes the delete confirmation modal. */
const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    recordToDelete.value = null;
};

/** Confirms and executes the record deletion. */
const confirmDelete = () => {
    if (recordToDelete.value) {
        records.value = records.value.filter(record => record.id !== recordToDelete.value.id);
    }
    closeDeleteModal();
};

</script>

<template>
    <div class="p-6 bg-white rounded-xl shadow-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                            ACTION
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50 transition duration-150">
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
                            <!-- Delete Button now opens custom modal -->
                            <button @click="openDeleteModal(record)" class="text-red-600 hover:text-red-900 transition-colors" title="Delete Record">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="records.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No records found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- --- MODAL OVERLAY --- -->
        <!-- Base for both modals: handles backdrop and transition -->
        <Transition name="fade">
            <div v-if="isStatusModalOpen || isDeleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" @click.self="closeStatusModal(); closeDeleteModal()">

                <!-- Status Change Modal -->
                <div v-if="isStatusModalOpen && recordToUpdate" class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all duration-300 scale-100" @click.stop>
                    <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4">Change Status for: {{ recordToUpdate.name }}</h3>
                    
                    <div class="space-y-3">
                        <button
                            v-for="option in statusOptions"
                            :key="option.value"
                            @click="updateStatus(option.value)"
                            :class="[
                                'w-full text-center py-2 px-4 rounded-lg font-medium transition-colors',
                                getStatusDisplay(option.value).class.replace('cursor-pointer', '').replace('hover:bg-green-200', 'hover:bg-green-300 hover:text-green-900').replace('hover:bg-red-200', 'hover:bg-red-300 hover:text-red-900').replace('hover:bg-yellow-200', 'hover:bg-yellow-300 hover:text-yellow-900'),
                                recordToUpdate.status === option.value ? 'ring-2 ring-offset-2 ring-[#850038]' : 'hover:shadow-md'
                            ]"
                        >
                            Set to {{ option.label }}
                        </button>
                    </div>

                    <div class="mt-6 pt-4 border-t flex justify-end">
                        <button @click="closeStatusModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">Cancel</button>
                    </div>
                </div>

                <!-- Delete Confirmation Modal (Custom alert replacement) -->
                <div v-else-if="isDeleteModalOpen && recordToDelete" class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all duration-300 scale-100" @click.stop>
                    <h3 class="text-xl font-semibold text-red-700 border-b pb-2 mb-4">Confirm Deletion</h3>
                    
                    <p class="text-gray-700 mb-6">
                        Are you sure you want to permanently delete the record **{{ recordToDelete.name }}**? This action cannot be undone.
                    </p>

                    <div class="flex justify-end space-x-3">
                        <button @click="closeDeleteModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">Cancel</button>
                        <button @click="confirmDelete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition shadow-md">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </Transition>
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
</style>
