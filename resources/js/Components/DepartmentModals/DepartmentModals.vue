<script setup>
import { ref, computed } from 'vue';

const emit = defineEmits(['updateData', 'deleteData', 'saveNewData']);

// State for the modal
const isModalOpen = ref(false);
const modalType = ref(null); // 'view', 'edit', 'delete', 'add'
const currentData = ref(null); // The item being viewed/edited/deleted/added
const currentTable = ref(null); // 'departments' or 'students'

// Helper function for deep cloning to break reactivity link
const deepClone = (obj) => JSON.parse(JSON.stringify(obj));

const closeModal = () => {
    isModalOpen.value = false;
    currentData.value = null;
    modalType.value = null;
    currentTable.value = null;
};

// --- PUBLIC ACTION HANDLER (Exposed for use by Parent/Table) ---

const openModal = (type, data, table) => {
    modalType.value = type;
    currentTable.value = table;
    isModalOpen.value = true;
    
    if (type === 'view' || type === 'edit') {
        // Clone the existing data for editing
        currentData.value = deepClone(data);
    } else if (type === 'add') {
        // Initialize empty object based on the table structure
        currentData.value = table === 'departments' ? 
            { name: '', college: '', building: '', head: '' } : 
            { name: '', email: '', phone: '', profession: '' };
    } else { // delete
        currentData.value = { ...data };
    }
};

// =======================================================
// === MODAL SUBMISSION LOGIC ===
// =======================================================

const submitSave = () => {
    if (!currentData.value) return;

    if (modalType.value === 'add') {
        // Add logic
        emit('saveNewData', currentData.value);
        console.log(`[Add Request] Emitted saveNewData for ${currentTable.value}.`);
    } else if (modalType.value === 'edit') {
        // Edit logic
        emit('updateData', {
            table: currentTable.value,
            data: currentData.value
        });
        console.log(`[Edit Request] Emitted update for ${currentTable.value}.`);
    }

    closeModal();
};

const confirmDelete = () => {
    if (!currentData.value) return;

    // Emit an event to the parent component/context to handle the actual array deletion
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
    const table = currentTable.value === 'departments' ? 'Department' : 'Student/User';
    switch (modalType.value) {
        case 'view': return `View ${table} Details`;
        case 'edit': return `Edit ${table} Details`;
        case 'delete': return `Delete ${table}`;
        case 'add': return `Add New ${table}`;
        default: return 'Details';
    }
});

// A simplified list of keys for the current table's data, used for the form/view display
const currentDataKeys = computed(() => {
    if (!currentData.value) return [];

    if (currentTable.value === 'departments') {
        return [
            { key: 'id', label: 'ID', type: 'number', readOnly: true },
            { key: 'name', label: 'Department Name', type: 'text' },
            { key: 'college', label: 'College', type: 'text' },
            { key: 'building', label: 'Building', type: 'text' },
            { key: 'head', label: 'Department Head', type: 'text' },
        ].filter(item => item.key !== 'id' || modalType.value !== 'add'); // Hide ID for Add
    } else if (currentTable.value === 'students') {
        return [
            { key: 'id', label: 'ID', type: 'number', readOnly: true },
            { key: 'name', label: 'User Name', type: 'text' },
            { key: 'email', label: 'Email-ID', type: 'email' },
            { key: 'phone', label: 'Phone', type: 'tel' },
            { key: 'profession', label: 'Profession', type: 'text' },
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
                             <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </div>
                        <div v-else-if="modalType === 'add'" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
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

                                <form v-if="modalType === 'view' || modalType === 'edit' || modalType === 'add'" @submit.prevent="modalType !== 'view' ? submitSave() : null" class="space-y-4">
                                    <div v-for="keyInfo in currentDataKeys" :key="keyInfo.key">
                                        <label :for="keyInfo.key" class="block text-sm font-medium text-gray-700">{{ keyInfo.label }}</label>
                                        <input
                                            :id="keyInfo.key"
                                            :type="keyInfo.type"
                                            v-model="currentData[keyInfo.key]"
                                            :readonly="modalType === 'view' || keyInfo.readOnly"
                                            :disabled="modalType === 'view' || keyInfo.readOnly"
                                            :required="modalType !== 'view' && !keyInfo.readOnly"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2"
                                            :class="{'bg-gray-100 cursor-not-allowed': modalType === 'view' || keyInfo.readOnly, 'focus:ring-indigo-500 focus:border-indigo-500 border': modalType !== 'view' && !keyInfo.readOnly}"
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
                    <template v-if="modalType === 'edit' || modalType === 'add'">
                        <button type="button" @click="submitSave" 
                                :class="{'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': modalType === 'edit', 'bg-green-600 hover:bg-green-700 focus:ring-green-500': modalType === 'add'}"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            {{ modalType === 'edit' ? 'Save Changes' : 'Add New Record' }}
                        </button>
                    </template>
                    
                    <template v-else-if="modalType === 'delete'">
                        <button type="button" @click="confirmDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            Confirm Delete
                        </button>
                    </template>

                    <button type="button" @click="closeModal" 
                        :class="{'bg-gray-200 text-gray-700 hover:bg-gray-300': modalType === 'view', 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300': modalType === 'edit' || modalType === 'delete' || modalType === 'add'}" 
                        class="mt-3 w-full inline-flex justify-center rounded-md shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        {{ modalType === 'view' ? 'Close' : 'Cancel' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>