<script setup>
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash, faClock } from '@fortawesome/free-solid-svg-icons';

const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    clock: faClock,
};

const props = defineProps({
    events: {
        type: Array,
        required: true
    }
});

const emit = defineEmits([
    'view-details',
    'edit-event',
    'delete-event',
    'row-clicked'
]);

const handleAction = (eventName, eventObject, e) => {
    e.stopPropagation();
    emit(eventName, eventObject);
};

const handleRowClick = (eventObject) => {
    emit('row-clicked', eventObject);
};

const formatTime = (date) => {
    if (!date) return '';
    const d = date instanceof Date ? date : new Date(date);
    if (isNaN(d)) return '';
    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

const formatDate = (date) => {
    if (!date) return '';
    const d = date instanceof Date ? date : new Date(date);
    if (isNaN(d)) return '';
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const formatEndDate = (date) => {
    if (!date) return '';
    const d = date instanceof Date ? date : new Date(date);
    if (isNaN(d)) return '';
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const formatTimeSlot = (event) => {
    if (event.allDay) {
        return 'All Day';
    } else {
        const startTime = formatTime(event.start);
        const endTime = event.end ? formatTime(event.end) : '';
        return `${startTime} ${endTime ? '- ' + endTime : ''}`;
    }
};

const getEventType = (event) => {
    const type = event.extendedProps?.type?.toLowerCase() || event.list?.toLowerCase() || '';
    if (type.includes('event')) return 'Event';
    if (type.includes('class')) return 'Class';
    if (type.includes('meeting')) return 'Meeting';
    return 'Other Activity';
};

const tableEvents = computed(() => {
    const sortedEvents = [...props.events].sort((a, b) =>
        new Date(a.start).getTime() - new Date(b.start).getTime()
    );

    return sortedEvents.map(event => {
        const extendedProps = event.extendedProps || {};

        return {
            id: event.id,
            title: event.title,
            room: extendedProps.room || event.title,
            building: extendedProps.building || 'N/A',
            college: extendedProps.college || 'N/A',
            subject: extendedProps.subject || event.title,
            startDate: formatDate(event.start),
            endDate: event.end ? formatEndDate(event.end) : formatDate(event.start),
            timeSlot: formatTimeSlot(event),
            isRecurring: event.rrule ? 'Yes' : 'No',
            eventType: getEventType(event),
            eventObject: event,
            allDay: event.allDay || false,
            type: event.list || 'Event',
            isOccupied: true, // All events in the table are occupied
            requester: extendedProps.requester || 'N/A',
            description: extendedProps.description || ''
        };
    });
});
</script>

<template>
    <div class="bg-white shadow-lg rounded-xl overflow-auto">
        <table class="min-w-full divide-y divide-gray-500">
            <thead class="bg-[#7A0C23] text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">APPOINTMENT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ROOM</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">BUILDING</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">COLLEGE</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">START DATE</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">TIME SLOT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">RECURRING</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider w-40">ACTIONS</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-300 bg-white">
                <tr
                    v-for="item in tableEvents"
                    :key="item.id"
                    @click="handleRowClick(item.eventObject)"
                    class="transition duration-150 cursor-pointer hover:bg-blue-50"
                >
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-[#7A0C23]">{{ item.title }}</div>
                        <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ item.description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">{{ item.room }}</div>
                            <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                <FontAwesomeIcon :icon="icons.clock" class="w-3 h-3 mr-1" />
                                Occupied
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.building }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.college }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.startDate }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.timeSlot }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <span :class="[
                            'px-2 py-1 rounded-full text-xs font-medium',
                            item.eventType === 'Class' ? 'bg-blue-100 text-blue-800' :
                            item.eventType === 'Meeting' ? 'bg-green-100 text-green-800' :
                            item.eventType === 'Event' ? 'bg-purple-100 text-purple-800' :
                            'bg-yellow-100 text-yellow-800'
                        ]">
                            {{ item.eventType }}
                        </span>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-3" @click.stop>
                            <button
                                @click="handleAction('view-details', item.eventObject, $event)"
                                title="View Details"
                                class="text-blue-500 hover:text-blue-700 transition-transform hover:scale-110"
                            >
                                <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                            </button>
                            <button
                                @click="handleAction('edit-event', item.eventObject, $event)"
                                title="Edit Event"
                                class="text-green-600 hover:text-green-800 transition-transform hover:scale-110"
                            >
                                <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                            </button>
                            <button
                                @click="handleAction('delete-event', item.eventObject, $event)"
                                title="Delete Event"
                                class="text-red-600 hover:text-red-800 transition-transform hover:scale-110"
                            >
                                <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                            </button>
                        </div>
                    </td>
                </tr>

                <tr v-if="tableEvents.length === 0">
                    <td :colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-600">No appointments scheduled</p>
                            <p class="text-sm text-gray-500 mt-1">Click "New Appointment" to schedule one</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
/* Custom styles for better alignment */
table {
    table-layout: fixed;
}

td {
    vertical-align: middle;
}

/* Action buttons container */
.flex.items-center.justify-center.space-x-3 {
    min-width: 120px;
}

/* Hover effects for buttons */
button.transition-transform:hover {
    transform: scale(1.1);
}

/* Prevent text selection on buttons */
button {
    user-select: none;
}

/* Center align text in table cells */
td.text-center {
    text-align: center;
}

/* Ensure consistent icon sizing */
.h-5.w-5 {
    height: 1.25rem;
    width: 1.25rem;
}

/* Better spacing for action buttons */
.space-x-3 > * + * {
    margin-left: 0.75rem;
}

/* Row hover effect */
tr:hover {
    background-color: #f0f9ff;
}

/* Make sure the action column doesn't shrink */
.w-40 {
    width: 10rem;
}
</style>
