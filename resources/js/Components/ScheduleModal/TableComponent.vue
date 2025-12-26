<script setup>
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash } from '@fortawesome/free-solid-svg-icons';

const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
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
        };
    });
});
</script>

<template>
    <div class="bg-white shadow-lg rounded-xl overflow-auto">
        <table class="min-w-full divide-y divide-gray-500">
            <thead class="bg-[#7A0C23] text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ROOM</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">BUILDING</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">COLLEGE</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">SUBJECT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">START DATE</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">END DATE</th>

                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">RECURRING</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider w-32">ACTION</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-300 bg-white">
                <tr
                    v-for="item in tableEvents"
                    :key="item.id"
                    @click="handleRowClick(item.eventObject)"
                    class=" transition duration-150 cursor-pointer"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#7A0C23]">
                        {{ item.room }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.building }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.college }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ item.subject }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.startDate }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ item.endDate }}
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

                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium" @click.stop>
                        <div class="flex items-center justify-center space-x-2">
                            <button
                                @click="handleAction('view-details', item.eventObject, $event)"
                                title="View in Calendar"
                                class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition px-2"
                            >
                                <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                            </button>
                            <button
                                @click="handleAction('edit-event', item.eventObject, $event)"
                                title="Edit Event"
                                class="text-green-600 hover:text-green-800 transform hover:scale-110 transition px-2"
                            >
                                <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                            </button>
                            <button
                                @click="handleAction('delete-event', item.eventObject, $event)"
                                title="Delete Event"
                                class="text-red-600 hover:text-red-800 transform hover:scale-110 transition px-2"
                            >
                                <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                            </button>
                        </div>
                    </td>
                </tr>

                <tr v-if="tableEvents.length === 0">
                    <td :colspan="9" class="px-6 py-4 text-center text-gray-500">
                        No scheduled appointments found. Click "New Appointment" to create one.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
tr {
    cursor: pointer;
}

tr:hover {
    background-color: #f0f9ff;
}

td:last-child {
    cursor: default;
}

td:last-child:hover {
    background-color: transparent;
}

.bg-\[\#7A0C23\] {
    background-color: #7A0C23;
}
</style>
