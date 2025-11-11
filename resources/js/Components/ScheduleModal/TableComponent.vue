<script setup>
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash, faPlus, faSearch } from '@fortawesome/free-solid-svg-icons';

// 1. FIX: Define the icons object
const icons = {
    eye: faEye,
    edit: faPenToSquare, // Correct icon for edit
    delete: faTrash,     // Correct icon for delete
    add: faPlus,
    search: faSearch
};

const props = defineProps({
    events: {
        type: Array,
        required: true
    }
});

// Emitted events match the required handlers
const emit = defineEmits(['view-details', 'edit-event', 'delete-event']);

// 3. FIX: Define the handler functions to emit the correct event and payload
const handleViewDetails = (eventObject) => {
    emit('view-details', eventObject);
};
const handleEditEvent = (eventObject) => {
    emit('edit-event', eventObject);
};
const handleDeleteEvent = (eventObject) => {
    emit('delete-event', eventObject);
};

// Helper to format Date object into HH:MM AM/PM string
const formatTime = (date) => {
    if (!date) return '';
    // Ensure the input is treated as a Date object if it's not already
    const d = date instanceof Date ? date : new Date(date); 
    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

// Helper to format Date object into YYYY-MM-DD string
const formatDate = (date) => {
    if (!date) return '';
    const d = date instanceof Date ? date : new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Transform events for table display
const tableEvents = computed(() => {
    // Note: It's assumed props.events contains objects with Date objects for `start` and `end`.
    // If they are strings, new Date(event.start) is required for sorting.
    return [...props.events]
        .sort((a, b) => new Date(a.start) - new Date(b.start)) // Ensure correct date sorting
        .map(event => {
            const appointmentDay = formatDate(event.start);
            let timeStr;

            if (event.allDay) {
                timeStr = 'All Day';
            } else {
                // Pass the raw date property to formatTime, which handles conversion if needed
                const startTime = formatTime(event.start); 
                const endTime = event.end ? formatTime(event.end) : '';
                timeStr = `${startTime} - ${endTime}`;
            }

            return {
                id: event.id,
                title: event.title,
                list: event.list, // 'list' property seems unused, using 'title' instead
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
                        Event Title
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
                        <div class="flex items-center justify-center space-x-2 divide-x divide-gray-200">
                            <button @click="handleViewDetails(item.eventObject)" title="View Details"
                                class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition px-2">
                                <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                            </button>
                            <button @click="handleEditEvent(item.eventObject)" title="Edit Event"
                                class="text-green-600 hover:text-green-800 transform hover:scale-110 transition px-2">
                                <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                            </button>
                            <button @click="handleDeleteEvent(item.eventObject)" title="Delete Event"
                                class="text-red-600 hover:text-red-800 transform hover:scale-110 transition px-2">
                                <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
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