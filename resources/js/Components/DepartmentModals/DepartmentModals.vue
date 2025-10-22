<script setup>
import { ref, computed } from 'vue';

// =======================================================
// === MODAL LOGIC (CENTRALIZED) ===
// =======================================================

// State for the modal
const isModalOpen = ref(false);
const modalType = ref(null); // 'view', 'edit', 'delete'
const currentData = ref(null); // The item being viewed/edited/deleted
const currentTable = ref(null); // 'users' or 'lastMonthUsers'

// NOTE: The actual data sources (users and lastMonthUsers) are kept in the parent
// (DepartmentLayout.vue in a full app) or would be passed down as props if this was the final parent.
// Since we don't have the parent's code structure here, we'll assume a mechanism to perform the update.
// For demonstration, we'll define a simple event to notify the parent/table to perform the actual data manipulation.

const emit = defineEmits(['updateData', 'deleteData']);

// Helper function for deep cloning to break reactivity link
const deepClone = (obj) => JSON.parse(JSON.stringify(obj));

const closeModal = () => {
    isModalOpen.value = false;
    currentData.value = null;
    modalType.value = null;
    currentTable.value = null;
};

// --- PUBLIC ACTION HANDLERS (Exposed for use by DepartmentTable) ---
// These functions will be called by the parent component via a template ref.

const openModal = (type, data, table) => {
    modalType.value = type;
    currentTable.value = table;
    isModalOpen.value = true;
    
    // For view/edit, clone the data to prevent direct mutation of the source array item
    if (type === 'view' || type === 'edit') {
        currentData.value = deepClone(data);
    } else { // delete
        currentData.value = { ...data }; // Shallow clone is okay for delete
    }
};

// =======================================================
// === MODAL SUBMISSION LOGIC ===
// =======================================================

const submitEdit = () => {
    if (!currentData.value) return;

    // Emit an event to the parent component/context to handle the actual array update
    // The parent/data source is responsible for finding the item and performing Object.assign(source[index], currentData.value);
    emit('updateData', {
        table: currentTable.value,
        data: currentData.value
    });

    console.log(`[Edit Request] Emitted update for ${currentTable.value}.`);
    closeModal();
};

const confirmDelete = () => {
    if (!currentData.value) return;

    // Emit an event to the parent component/context to handle the actual array deletion
    // The parent/data source is responsible for finding and filtering out the item.
    emit('deleteData', {
        table: currentTable.value,
        data: currentData.value
    });

    console.log(`[Delete Request] Emitted delete for ${currentTable.value}.`);
    closeModal();
};

// =======================================================
// === COMPUTED PROPERTIES FOR MODAL UI ===
// =======================================================

// Title for the modal
const modalTitle = computed(() => {
    if (!currentTable.value || !modalType.value) return 'Details';
    const table = currentTable.value === 'users' ? 'User Details' : 'Last Month User';
    switch (modalType.value) {
        case 'view': return `View ${table}`;
        case 'edit': return `Edit ${table}`;
        case 'delete': return `Delete ${table}`;
        default: return 'Details';
    }
});

// A simplified list of keys for the current table's data, used for the form/view display
const currentDataKeys = computed(() => {
    if (!currentData.value) return [];

    if (currentTable.value === 'users') {
        return [
            { key: 'id', label: 'ID', type: 'number', readOnly: true },
            { key: 'name', label: 'User Name', type: 'text' },
            { key: 'email', label: 'Email-ID', type: 'email' },
            { key: 'phone', label: 'Phone', type: 'tel' },
            { key: 'profession', label: 'Profession', type: 'text' },
        ];
    } else if (currentTable.value === 'lastMonthUsers') {
        return [
            { key: 'userId', label: 'User ID', type: 'text', readOnly: true },
            { key: 'email', label: 'Email', type: 'email' },
            { key: 'month', label: 'Month', type: 'text' },
            { key: 'yearStart', label: 'Year Start', type: 'number' },
            { key: 'yearEnd', label: 'Year End', type: 'number' },
        ];
    }
    return [];
});

// Expose the necessary function so the parent component can call it
defineExpose({
    openModal
});
</script>

<template>
    <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div v-if="modalType === 'delete'" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div v-else class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg v-if="modalType === 'edit'" class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            <svg v-else class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>

                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                {{ modalTitle }}
                            </h3>
                            <div class="mt-4">

                                <form v-if="modalType === 'view' || modalType === 'edit'" @submit.prevent="modalType === 'edit' ? submitEdit() : null" class="space-y-4">
                                    <div v-for="keyInfo in currentDataKeys" :key="keyInfo.key">
                                        <label :for="keyInfo.key" class="block text-sm font-medium text-gray-700">{{ keyInfo.label }}</label>
                                        <input
                                            :id="keyInfo.key"
                                            :type="keyInfo.type"
                                            v-model="currentData[keyInfo.key]"
                                            :readonly="modalType === 'view' || keyInfo.readOnly"
                                            :disabled="modalType === 'view' || keyInfo.readOnly"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2"
                                            :class="{'bg-gray-100 cursor-not-allowed': modalType === 'view' || keyInfo.readOnly, 'focus:ring-indigo-500 focus:border-indigo-500 border': modalType === 'edit' && !keyInfo.readOnly}"
                                        />
                                    </div>
                                </form>

                                <div v-else-if="modalType === 'delete'">
                                    <p class="text-lg text-gray-700">
                                        Are you sure you want to delete the following record? This action cannot be undone.
                                    </p>
                                    <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200">
                                        <p v-for="keyInfo in currentDataKeys" :key="keyInfo.key" class="text-sm text-red-800">
                                            <span class="font-semibold">{{ keyInfo.label }}:</span> {{ currentData[keyInfo.key] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <template v-if="modalType === 'edit'">
                        <button type="button" @click="submitEdit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            Save Changes
                        </button>
                    </template>
                    <template v-else-if="modalType === 'delete'">
                        <button type="button" @click="confirmDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            Confirm Delete
                        </button>
                    </template>

                    <button type="button" @click="closeModal" :class="{'bg-gray-200 text-gray-700 hover:bg-gray-300': modalType === 'view', 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300': modalType === 'edit' || modalType === 'delete'}" class="mt-3 w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        {{ modalType === 'view' ? 'Close' : 'Cancel' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Scoped styles can be added here if needed, but Tailwind classes cover most of the styling */
</style>