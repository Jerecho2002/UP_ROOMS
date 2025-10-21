<script setup>
import { ref, computed } from 'vue'

// --- Mock Data: Simulates the records fetched from an API ---
const mockRecordsData = [
    { id: 1, name: 'MID TERM', status: 'expired', startDate: '2024-09-15', endDate: '2026-09-15' },
    { id: 2, name: 'FINALS', status: 'active', startDate: '2025-01-15', endDate: '2027-01-15' },
    { id: 3, name: 'SUMMER SEMESTER', status: 'not_registered', startDate: '2025-06-01', endDate: '2025-07-31' },
    { id: 4, name: 'ACADEMIC YEAR 2026', status: 'active', startDate: '2026-09-01', endDate: '2027-05-30' },
];
// --------------------------------------------------------

const records = ref(mockRecordsData);

// Function to determine the display class and text for the status
const getStatusDisplay = (status) => {
    switch (status) {
        case 'active':
            return {
                text: 'Active',
                // Uses a light green background and darker text/border
                class: 'bg-green-100 text-green-800 border border-green-400'
            };
        case 'expired':
            return {
                text: 'Expired',
                // Uses a light red background and darker text/border, matching the image
                class: 'bg-red-100 text-red-800 border border-red-400'
            };
        case 'not_registered':
            return {
                text: 'Not Registered',
                // Uses a light yellow/orange for the new status
                class: 'bg-yellow-100 text-yellow-800 border border-yellow-400'
            };
        default:
            return {
                text: 'Unknown',
                class: 'bg-gray-100 text-gray-800 border border-gray-400'
            };
    }
};

// Placeholder action handler
const deleteRecord = (id) => {
    if (confirm(`Are you sure you want to delete record ID: ${id}?`)) {
        records.value = records.value.filter(record => record.id !== id);
    }
};

</script>

<template>
    <div class="overflow-x-auto shadow-sm rounded-lg">
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
                <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50">
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
                        <span
                            :class="[
                                'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                getStatusDisplay(record.status).class
                            ]"
                        >
                            {{ getStatusDisplay(record.status).text }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="deleteRecord(record.id)" class="text-red-600 hover:text-red-900 transition-colors">
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
</template>

<style scoped>
/* Scoped styles are generally not needed when using Tailwind, 
   but you can add them here if you were using a custom icon font for the trash can
   or if you needed specific overrides. */
</style>