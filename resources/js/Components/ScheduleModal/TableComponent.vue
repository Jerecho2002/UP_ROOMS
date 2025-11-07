<script setup>
import { computed, defineProps, defineEmits } from 'vue';

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
});

// Define all necessary emitters
const emit = defineEmits(['view-details', 'edit-event', 'delete-event', 'switch-to-list-mode']);

// --- Utility Functions (For Display) ---

const dateToTimeString = (date) => {
    if (!date) return 'All Day';
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const hour = (hours % 12) || 12; 
    const ampm = (hours >= 12) ? ' PM' : ' AM';
    return hour + ':' + String(minutes).padStart(2, '0') + ampm;
};

const formatTableDate = (date) => {
    if (!date) return '';
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const sortedTableEvents = computed(() => {
    // Sort events by start date/time
    const sorted = [...props.events].sort((a, b) => (+a.start) - (+b.start));
    return sorted.map(event => {
        const startTime = dateToTimeString(event.start);
        const endTime = event.end ? dateToTimeString(event.end) : '';
        
        return {
            ...event,
            appointmentDay: formatTableDate(event.start),
            time: event.allDay ? 'All Day' : (endTime ? `${startTime} - ${endTime}` : startTime),
        };
    });
});
// --- End Utility Functions ---

const viewDetails = (event) => {
    emit('view-details', event);
};

const editEvent = (event) => {
    // Emit the full event object to the parent for pre-filling the modal
    emit('edit-event', event);
};

const deleteEvent = (eventId) => {
    // Emit the ID to the parent for deletion
    emit('delete-event', eventId);
};
</script>

<template>
    <div class="bg-white p-4 shadow-lg rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-[#7A0C23] text-white uppercase text-xs">
                    <th class="px-4 py-3 text-left">Schedule</th>
                    <th class="px-4 py-3 text-left">Appointment Day</th>
                    <th class="px-4 py-3 text-left">Time</th>
                    <th class="px-4 py-3 text-center" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="event in sortedTableEvents" :key="event.id" class="hover:bg-gray-50 transition duration-150">
                    <td class="px-4 py-3 text-sm text-gray-900 font-medium truncate">{{ event.title }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ event.appointmentDay }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ event.time }}</td>
                    <td class="px-4 py-3 text-center space-x-3">
                        <button 
                            @click="viewDetails(event)" 
                            title="View in Calendar"
                            class="text-blue-600 hover:text-blue-800 transition"
                        >
                            👁️
                        </button>
                        
                        <button 
                            @click="editEvent(event)" 
                            title="Edit Appointment"
                            class="text-yellow-600 hover:text-yellow-800 transition"
                        >
                            ✏️
                        </button>

                        <button 
                            @click="deleteEvent(event.id)" 
                            title="Delete Appointment"
                            class="text-red-600 hover:text-red-800 transition"
                        >
                            🗑️
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <p v-if="events.length === 0" class="text-center py-6 text-gray-500">No appointments scheduled.</p>
    </div>
</template>