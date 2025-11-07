<script setup>
import { computed } from 'vue';

const props = defineProps({
    events: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['view-details', 'edit-event', 'delete-event']);

// Helper to format Date object into HH:MM AM/PM string
const formatTime = (date) => {
    if (!date) return '';
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

// Helper to format Date object into YYYY-MM-DD string
const formatDate = (date) => {
    if (!date) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Transform events for table display
const tableEvents = computed(() => {
    // Sort events by date and then by start time for a coherent list
    return [...props.events]
        .sort((a, b) => a.start - b.start)
        .map(event => {
            const appointmentDay = formatDate(event.start);
            let timeStr;

            if (event.allDay) {
                timeStr = 'All Day';
            } else {
                const startTime = formatTime(event.start);
                const endTime = event.end ? formatTime(event.end) : '';
                timeStr = `${startTime} - ${endTime}`;
            }

            return {
                id: event.id,
                title: event.title,
                list: event.list,
                appointmentDay: appointmentDay,
                time: timeStr,
                eventObject: event, // Keep a reference to the original event for action handlers
            };
        });
});

</script>

<template>
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#7A0C23] text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        List
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Appointment Day
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Time
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider w-32">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="item in tableEvents" :key="item.id" class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ item.title }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.appointmentDay }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.time }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center space-x-0 divide-x divide-gray-200">
                            <button 
                                @click="emit('view-details', item.eventObject)" 
                                title="View Details"
                                class="p-2 text-green-600 hover:text-green-800 transition duration-150 rounded-l-lg hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>

                            <button 
                                @click="emit('edit-event', item.eventObject)" 
                                title="Edit Appointment"
                                class="p-2 text-blue-600 hover:text-blue-800 transition duration-150 hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>

                            <button 
                                @click="emit('delete-event', item.id)" 
                                title="Delete Appointment"
                                class="p-2 text-red-600 hover:text-red-800 transition duration-150 rounded-r-lg hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="tableEvents.length === 0">
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        No scheduled appointments found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
/* Optional styling to ensure the table header background matches the image */
.bg-\[\#7A0C23\] {
    background-color: #7A0C23; /* Dark red/maroon from the image */
}
</style>