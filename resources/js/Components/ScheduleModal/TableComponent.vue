<script setup>
import { ref } from 'vue';

// Define the events this component can emit
const emit = defineEmits(['view-details']);

// Schedule data stored in a reactive reference.
// The structure is kept as-is, as the parent will be responsible for transformation.
const scheduleItems = ref([
    {
        id: 1,
        list: 'Training',
        title: 'Training Session', 
        appointmentDay: '2025-10-21', // Example current date
        time: '10:00 AM-12:00 PM',
    },
    {
        id: 2,
        list: 'Meeting Webinar',
        title: 'Project Kickoff Webinar', 
        appointmentDay: '2025-10-21', // Example current date
        time: '02:30 PM-03:30 PM',
    },
    {
        id: 3,
        list: 'External Review',
        title: 'Q4 Budget Review', 
        appointmentDay: '2025-10-25', // Another date
        time: '09:00 AM-11:00 AM',
    },
    {
        id: 4,
        list: 'All Day Event',
        title: 'Team Building Workshop',
        appointmentDay: '2025-10-22', 
        time: '', // Blank time indicates an all-day event
    },
]);

/**
 * Emits an event to the parent component to switch the view
 * and focus on the selected appointment's date.
 * @param {object} item - The schedule item object.
 */
const viewDetails = (item) => {
    // Emitting the YYYY-MM-DD date string
    emit('view-details', item.appointmentDay);
};

/**
 * Removes an item from the scheduleItems list by its ID.
 */
const deleteItem = (itemId) => {
    // Filter out the item with the matching ID
    scheduleItems.value = scheduleItems.value.filter(item => item.id !== itemId);
};

// Expose the scheduleItems so the parent can access the data for the CalendarView.
defineExpose({
    scheduleItems
});
</script>

<template>
    <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-red-700 text-white uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">List</th>
                    <th class="py-3 px-6 text-left">Appointment Day</th>
                    <th class="py-3 px-6 text-left">Time</th>
                    <th class="py-3 px-6 text-center">View</th>
                    <th class="py-3 px-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                <tr 
                    v-for="item in scheduleItems" 
                    :key="item.id" 
                    class="border-b border-gray-200 hover:bg-gray-50"
                >
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        <span class="font-medium">{{ item.list }}</span>
                    </td>
                    <td class="py-3 px-6 text-left">
                        {{ item.appointmentDay }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        {{ item.time || 'All Day' }}
                    </td>
                    <td class="py-3 px-6 text-center">
                        <button 
                            @click="viewDetails(item)" 
                            class="font-semibold text-green-600 hover:text-green-800 transition duration-150 ease-in-out"
                            aria-label="View details for this schedule item"
                        >
                            VIEW DETAILS
                        </button>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <button 
                            @click="deleteItem(item.id)" 
                            class="text-red-500 hover:text-red-700 transition duration-150 ease-in-out"
                            aria-label="Delete schedule item"
                        >
                            <svg 
                                xmlns="http://www.w3.org/2000/svg" 
                                class="h-5 w-5 inline-block" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor" 
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                </tr>
                <tr v-if="scheduleItems.length === 0">
                    <td colspan="5" class="py-5 text-center text-gray-400">
                        No scheduled items found. 🎉
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
/* Scoped styles for the table */
</style>