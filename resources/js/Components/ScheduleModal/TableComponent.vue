<script setup>
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash } from '@fortawesome/free-solid-svg-icons'; // Removed unused icons

// --- ⚙️ Configuration: Font Awesome Icons ---

/**
 * Maps readable icon names to their Font Awesome SVG definition imports.
 */
const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    // add and search icons were imported but unused in the template and removed for brevity
};

// --- 📥 Component Properties (Props) ---

/**
 * Defines component properties. Expects an array of FullCalendar-style event objects.
 */
const props = defineProps({
    events: {
        type: Array,
        required: true
    }
});

// --- 📤 Custom Events (Emits) ---

/**
 * Defines the custom events this component can emit to its parent.
 * The emitted payload is always the original `eventObject`.
 */
const emit = defineEmits([
    'view-details', // Triggered when the view icon is clicked
    'edit-event',   // Triggered when the edit icon is clicked
    'delete-event'  // Triggered when the delete icon is clicked
]);

// --- 🔄 Action Handlers ---

/**
 * Emits the specified action event with the full event object payload.
 * @param {string} eventName - The name of the event to emit ('view-details', 'edit-event', 'delete-event').
 * @param {Object} eventObject - The original event data object.
 */
const handleAction = (eventName, eventObject) => {
    emit(eventName, eventObject);
};

// --- 📐 Formatters ---

/**
 * Formats a Date object or date string into a readable time string (HH:MM AM/PM).
 * @param {Date|string} date - The date object or string to format.
 * @returns {string} Formatted time string, or empty string if input is invalid.
 */
const formatTime = (date) => {
    if (!date) return '';
    // Safely convert to Date object
    const d = date instanceof Date ? date : new Date(date); 
    // Check if date is valid
    if (isNaN(d)) return ''; 
    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

/**
 * Formats a Date object or date string into a YYYY-MM-DD string.
 * @param {Date|string} date - The date object or string to format.
 * @returns {string} Formatted date string, or empty string if input is invalid.
 */
const formatDate = (date) => {
    if (!date) return '';
    const d = date instanceof Date ? date : new Date(date);
    if (isNaN(d)) return '';
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// --- ✨ Computed Property for Table Data ---

/**
 * Transforms the raw `props.events` array into a format optimized for table rendering.
 * It handles sorting, date/time formatting, and preserves the original event object for actions.
 */
const tableEvents = computed(() => {
    // 1. Sort events by start date to ensure chronological order
    const sortedEvents = [...props.events].sort((a, b) => 
        new Date(a.start).getTime() - new Date(b.start).getTime() // Use getTime() for safer comparison
    );

    // 2. Map and transform for display
    return sortedEvents.map(event => {
        // Handle time slot display
        let timeStr;
        if (event.allDay) {
            timeStr = 'All Day';
        } else {
            const startTime = formatTime(event.start); 
            const endTime = event.end ? formatTime(event.end) : '';
            timeStr = `${startTime} ${endTime ? '- ' + endTime : ''}`;
        }
        
        // Extract extended properties safely
        const extendedProps = event.extendedProps || {};

        return {
            id: event.id,
            room: event.title, // Assuming event.title is the Room identifier
            building: extendedProps.building || 'N/A', 
            college: extendedProps.college || 'N/A', 
            subject: extendedProps.subject || extendedProps.title || event.title, // Fallback to title
            startDate: formatDate(event.start),
            timeSlot: timeStr,
            isRecurring: event.rrule ? 'Yes' : 'No', // Check for rrule for recurring events
            eventObject: event,                      // Pass original object for actions
        };
    });
});
</script>
<template>
    <div class="bg-white shadow-lg rounded-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#7A0C23] text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        ROOM
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        BUILDING
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        COLLEGE
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        SUBJECT
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                       START
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        END
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        RECURRING
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider w-32">
                        ACTION
                    </th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-gray-200">
                <tr v-for="item in tableEvents" :key="item.id" class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ item.room }} 
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.building }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.college }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ item.subject }} 
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.startDate }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.end }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ item.isRecurring }}
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center space-x-2 divide-x divide-gray-200">
                            <button @click="handleAction('view-details', item.eventObject)" title="View Details"
                                class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition px-2">
                                <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                            </button>
                            <button @click="handleAction('edit-event', item.eventObject)" title="Edit Event"
                                class="text-green-600 hover:text-green-800 transform hover:scale-110 transition px-2">
                                <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                            </button>
                            <button @click="handleAction('delete-event', item.eventObject)" title="Delete Event"
                                class="text-red-600 hover:text-red-800 transform hover:scale-110 transition px-2">
                                <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr v-if="tableEvents.length === 0">
                    <td :colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No scheduled appointments found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<style scoped>
/* Ensure the custom color for the header is correctly applied */
.bg-\[\#7A0C23\] {
    background-color: #7A0C23; 
}

/* Tailwind CSS generally handles the rest of the styling. 
 Keep custom/utility colors defined here if necessary.
*/
</style>