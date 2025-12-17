<script setup>
import { ref, computed, watchEffect } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faChevronLeft,
    faChevronRight,
    faCalendarDay,
    faCalendarWeek,
    faCalendar,
    faListUl,
    faPenToSquare,
    faTrash,
    faPlusCircle
} from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
    initialDate: {
        type: Date,
        default: () => new Date()
    },
    initialMode: {
        type: String,
        default: 'month'
    },
    MonthGridViewComponent: { type: Object, required: true },
    TimeGridViewComponent: { type: Object, required: true },
    ListViewComponent: { type: Object },
});

const emit = defineEmits([
    'update:date',
    'update:mode',
    'dateClicked',
    'selectEvent',
    'editEvent',
    'deleteEvent',
    'addAppointment',
]);

// --- Core State ---
const currentReferenceDate = ref(props.initialDate);
const currentMode = ref(props.initialMode);

// Sync initial props
watchEffect(() => {
    currentReferenceDate.value = props.initialDate;
    currentMode.value = props.initialMode;
});

// --- Icons for List View ---
const listIcons = {
    edit: faPenToSquare,
    delete: faTrash,
    add: faPlusCircle,
};

// --- Utilities ---
const dateToTimeString = (date) => {
    if (!date || isNaN(date)) return '';
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
};

const dateToDayString = (date) => {
    if (!date || isNaN(date)) return '';
    const d = new Date(date);
    return d.toISOString().split('T')[0];
};

const formatDay = (date) => {
    const d = new Date(date);
    if (isNaN(d)) return '';
    return d.toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const getRequestType = (event) => {
    const type = event.extendedProps?.type?.toLowerCase() || event.list?.toLowerCase() || '';
    if (type.includes('event')) return 'Event';
    if (type.includes('class')) return 'Class';
    if (type.includes('meeting')) return 'Meeting';
    return 'Other Activity';
};

// --- Event Handlers ---
const changeView = (mode) => {
    currentMode.value = mode;
    emit('update:mode', mode);
};

const handleNavigation = (unit, direction) => {
    const newDate = new Date(currentReferenceDate.value);
    const navigationUnit = currentMode.value === 'list' ? 'day' : unit;

    if (navigationUnit === 'day') {
        newDate.setDate(newDate.getDate() + direction);
    } else if (navigationUnit === 'week') {
        newDate.setDate(newDate.getDate() + direction * 7);
    } else if (navigationUnit === 'month') {
        newDate.setMonth(newDate.getMonth() + direction);
    }

    currentReferenceDate.value = newDate;
    emit('update:date', newDate);
};

const goToToday = () => {
    currentReferenceDate.value = new Date();
    emit('update:date', currentReferenceDate.value);
    if (currentMode.value === 'list') {
        currentMode.value = 'day';
        emit('update:mode', 'day');
    }
};

const handleDateClick = (date, hour = null, minute = null) => {
    emit('dateClicked', date, hour, minute);
};

const handleEventSelected = (event) => {
    emit('selectEvent', event);
};

const handleEditEvent = (event, e = null) => {
    if (e) e.stopPropagation();
    emit('editEvent', event);
};

const handleDeleteEvent = (event, e = null) => {
    if (e) e.stopPropagation();
    emit('deleteEvent', event);
};

const handleAddRowClick = () => {
    emit('addAppointment');
};

// --- Computed Properties ---
const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

const formattedTitle = computed(() => {
    const date = currentReferenceDate.value;
    const options = { year: 'numeric', month: 'long' };

    if (currentMode.value === 'day' || currentMode.value === 'list') {
        options.day = 'numeric';
        return date.toLocaleDateString('en-US', options);
    } else if (currentMode.value === 'week') {
        const dayOfWeek = date.getDay();
        const startOfWeek = new Date(date);
        startOfWeek.setDate(date.getDate() - dayOfWeek);
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        return `${startOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
    }

    return date.toLocaleDateString('en-US', options);
});

const monthGrid = computed(() => {
    const date = currentReferenceDate.value;
    const year = date.getFullYear();
    const month = date.getMonth();
    const today = dateToDayString(new Date());

    const preparedEvents = props.data.map(event => ({
        ...event,
        startDayString: dateToDayString(event.start),
        endDayString: event.end ? dateToDayString(event.end) : dateToDayString(event.start),
    }));

    const firstDayOfMonth = new Date(year, month, 1);
    const startingDay = firstDayOfMonth.getDay();

    const dateGrid = [];
    let dayCounter = 1 - startingDay;

    for (let i = 0; i < 6; i++) {
        const week = [];
        for (let j = 0; j < 7; j++) {
            const currentDate = new Date(year, month, dayCounter);
            const dayString = dateToDayString(currentDate);

            const dayEvents = preparedEvents.filter(event => {
                return dayString >= event.startDayString && dayString <= event.endDayString;
            });

            week.push({
                date: currentDate,
                isToday: dayString === today,
                dayClass: currentDate.getMonth() === month ? '' : 'text-gray-400',
                allDayEvents: dayEvents.filter(e => e.allDay),
                events: dayEvents.filter(e => !e.allDay),
            });
            dayCounter++;
        }
        dateGrid.push(week);
    }
    return dateGrid;
});

const timeGridData = computed(() => {
    const date = currentReferenceDate.value;
    const today = dateToDayString(new Date());

    let datesToRender = [];

    if (currentMode.value === 'day') {
        datesToRender.push(date);
    } else if (currentMode.value === 'week') {
        const dayOfWeek = date.getDay();
        const startOfWeek = new Date(date);
        startOfWeek.setDate(date.getDate() - dayOfWeek);

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);
            datesToRender.push(day);
        }
    }

    const preparedEvents = props.data.map(event => ({
        ...event,
        startDayString: dateToDayString(event.start),
        endDayString: event.end ? dateToDayString(event.end) : dateToDayString(event.start),
    }));

    return datesToRender.map(currentDate => {
        const dayString = dateToDayString(currentDate);

        const dayEvents = preparedEvents.filter(event => {
            return dayString >= event.startDayString && dayString <= event.endDayString;
        });

        const dayLabel = currentDate.toLocaleDateString('en-US', {
            weekday: 'short',
            day: 'numeric',
            month: 'short'
        });

        return {
            date: currentDate,
            label: dayLabel,
            isToday: dayString === today,
            allDayEvents: dayEvents.filter(e => e.allDay),
            events: dayEvents.filter(e => !e.allDay).map(event => ({
                ...event,
                style: getTimeEventStyle(event)
            })),
        };
    });
});

const hourSlots = computed(() => {
    const slots = [];
    for (let h = 0; h < 24; h++) {
        for (let m = 0; m < 60; m += 30) {
            const time = new Date(0, 0, 0, h, m);
            slots.push({
                hour: h,
                minute: m,
                label: m === 0 ? time.toLocaleTimeString('en-US', { hour: 'numeric', hour12: true }) : '',
            });
        }
    }
    return slots;
});

const getTimeEventStyle = (event) => {
    const start = event.start;
    const end = event.end || new Date(start.getTime() + 30 * 60000);
    const startMinutes = start.getHours() * 60 + start.getMinutes();
    const endMinutes = end.getHours() * 60 + end.getMinutes();
    const durationMinutes = endMinutes - startMinutes;
    const pxPerMinute = 20 / 15;

    const topPositionPx = startMinutes * pxPerMinute;
    const heightPx = durationMinutes * pxPerMinute;

    return {
        top: `${topPositionPx}px`,
        height: `${heightPx}px`,
        minHeight: `${Math.max(20, heightPx)}px`,
        zIndex: 20,
    };
};

const tableEvents = computed(() => {
    return [...props.data]
        .sort((a, b) => new Date(a.start) - new Date(b.start))
        .map(event => {
            const start = new Date(event.start);
            const end = event.end ? new Date(event.end) : null;
            const defaultEnd = new Date(start.getTime() + 30 * 60000);

            return {
                id: event.id,
                appointment: event.title || 'Untitled Appointment',
                day: formatDay(event.start),
                time: event.allDay
                    ? 'All Day'
                    : `${dateToTimeString(start)} - ${dateToTimeString(end || defaultEnd)}`,
                requestType: getRequestType(event),
                eventObject: event,
                // Add extended props for display
                room: event.extendedProps?.room || 'N/A',
                building: event.extendedProps?.building || 'N/A',
                college: event.extendedProps?.college || 'N/A',
                subject: event.extendedProps?.subject || event.title,
            };
        });
});
</script>

<template>
    <div class="flex flex-col bg-white rounded-xl shadow-lg">
        <div class="p-4 flex items-center justify-between border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <button @click="handleNavigation(currentMode, -1)"
                    class="p-2 text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                    <FontAwesomeIcon :icon="faChevronLeft" />
                </button>
                <button @click="handleNavigation(currentMode, 1)"
                    class="p-2 text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                    <FontAwesomeIcon :icon="faChevronRight" />
                </button>

                <button @click="goToToday"
                    class="px-3 py-1 text-sm font-semibold border rounded-lg bg-[#7A0C23] text-white hover:bg-red-800 transition">
                    Today
                </button>
            </div>

            <h2 class="text-xl font-bold text-gray-800">
                {{ formattedTitle }}
            </h2>

            <div class="flex space-x-1 p-1 bg-gray-100 rounded-lg">
                <button @click="changeView('list')"
                    :class="['p-2 rounded-lg text-sm font-medium transition', currentMode === 'list' ? 'bg-white text-[#7A0C23] shadow' : 'text-gray-600 hover:bg-white']"
                    title="List View">
                    <FontAwesomeIcon :icon="faListUl" class="w-4 h-4" />
                </button>

                <button @click="changeView('day')"
                    :class="['p-2 rounded-lg text-sm font-medium transition', currentMode === 'day' ? 'bg-white text-[#7A0C23] shadow' : 'text-gray-600 hover:bg-white']"
                    title="Day View">
                    <FontAwesomeIcon :icon="faCalendarDay" class="w-4 h-4" />
                </button>

                <button @click="changeView('week')"
                    :class="['p-2 rounded-lg text-sm font-medium transition', currentMode === 'week' ? 'bg-white text-[#7A0C23] shadow' : 'text-gray-600 hover:bg-white']"
                    title="Week View">
                    <FontAwesomeIcon :icon="faCalendarWeek" class="w-4 h-4" />
                </button>

                <button @click="changeView('month')"
                    :class="['p-2 rounded-lg text-sm font-medium transition', currentMode === 'month' ? 'bg-white text-[#7A0C23] shadow' : 'text-gray-600 hover:bg-white']"
                    title="Month View">
                    <FontAwesomeIcon :icon="faCalendar" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <div class="flex-grow p-4">
            <!-- List View -->
            <div v-if="currentMode === 'list'" class="bg-white rounded-xl overflow-hidden border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23] text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">APPOINTMENT</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">ROOM</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">BUILDING</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">DAY</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">TIME</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">TYPE</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider w-32">ACTIONS</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="item in tableEvents"
                            :key="item.id"
                            class="transition hover:bg-blue-50 cursor-pointer"
                            @click="handleEditEvent(item.eventObject)"
                        >
                            <td class="px-6 py-4 text-sm font-bold text-[#7A0C23]">
                                {{ item.appointment }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ item.room }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ item.building }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ item.day }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ item.time }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    item.requestType === 'Class' ? 'bg-blue-100 text-blue-800' :
                                    item.requestType === 'Meeting' ? 'bg-green-100 text-green-800' :
                                    item.requestType === 'Event' ? 'bg-purple-100 text-purple-800' :
                                    'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ item.requestType }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-center space-x-2" @click.stop>
                                <button
                                    @click="handleEditEvent(item.eventObject, $event)"
                                    class="text-green-600 hover:text-green-800 px-2"
                                    title="Edit"
                                >
                                    <FontAwesomeIcon :icon="listIcons.edit" />
                                </button>
                                <button
                                    @click="handleDeleteEvent(item.eventObject, $event)"
                                    class="text-red-600 hover:text-red-800 px-2"
                                    title="Delete"
                                >
                                    <FontAwesomeIcon :icon="listIcons.delete" />
                                </button>
                            </td>
                        </tr>

                        <tr @click="handleAddRowClick"
                            class="bg-green-50/50 hover:bg-green-100 cursor-pointer transition">
                            <td colspan="7" class="px-6 py-4 text-center text-green-700 font-semibold text-base">
                                <FontAwesomeIcon :icon="listIcons.add" class="mr-2" />
                                Click here to schedule a new appointment...
                            </td>
                        </tr>

                        <tr v-if="tableEvents.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No appointments found. Click above to add one!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Month Grid View -->
            <component
                v-else-if="currentMode === 'month'"
                :is="MonthGridViewComponent"
                :dateGrid="monthGrid"
                :days="days"
                :dateToDayString="dateToDayString"
                @selectEvent="handleEventSelected"
                @selectDate="(date) => {
                    currentReferenceDate = date;
                    changeView('day');
                }"
                @emitDateClick="handleDateClick"
            />

            <!-- Time Grid View (Day/Week) -->
            <component
                v-else-if="currentMode === 'day' || currentMode === 'week'"
                :is="TimeGridViewComponent"
                :viewMode="currentMode"
                :weekDays="timeGridData"
                :singleDay="timeGridData[0]"
                :hourSlots="hourSlots"
                :getEventStyle="getTimeEventStyle"
                :dateToTimeString="dateToTimeString"
                @selectEvent="handleEventSelected"
                @emitDateClick="handleDateClick"
            />
        </div>
    </div>
</template>
