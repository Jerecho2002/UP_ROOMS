<script setup>
import { ref, computed, reactive, watch } from 'vue';
// 1. Import the new child components
// resources/js/Components/ScheduleModal/CalendarView.vue


// ... existing imports ...
// 1. Import the new child components
import ListView from './ListView.vue'; // Correct relative path
import MonthGridView from './MonthGridView.vue'; // Correct relative path
import TimeGridView from './TimeGridView.vue'; // Correct relative path
// ... rest of the script ...

// ... rest of the template ...

// Define the props that this view will accept
const props = defineProps({
    initialDate: {
        type: [Date, String],
        default: () => new Date(),
    },
    initialMode: {
        type: String,
        default: 'list',
    },
    // Expected data structure: [{id: 1, title: '...', allDay: true/false, start: Date, end: Date|null}]
    data: {
        type: Array,
        default: () => [],
    },
});

// Define the events this component can emit to its parent
const emit = defineEmits([
    'update:date',
    'update:mode',
    'eventSelected',
    'dateClicked',
]);


// --- GLOBAL CONSTANTS & HELPERS ---

const DAYS = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
const MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

// Time Slot Configuration for Week/Day View (6:00 AM to 10:00 PM)
const START_HOUR = 6;
const END_HOUR = 22;
const SLOT_DURATION_MINUTES = 30;
const SLOT_HEIGHT_REM = 2.5; // Corresponds to the height of one 30-minute slot (h-10 class in Tailwind)

const HOUR_SLOTS = Array.from({ length: (END_HOUR - START_HOUR) * (60 / SLOT_DURATION_MINUTES) }, (_, i) => {
    const hour = START_HOUR + Math.floor(i / (60 / SLOT_DURATION_MINUTES));
    const minute = (i % (60 / SLOT_DURATION_MINUTES)) * SLOT_DURATION_MINUTES;
    const displayHour = hour % 12 || 12;
    const ampm = hour < 12 || hour === 24 ? 'AM' : 'PM';

    return {
        hour,
        minute,
        label: `${displayHour}:${String(minute).padStart(2, '0')} ${ampm}`
    };
});

// Helper functions (isToday, dateToDayString, dateToTimeString)
const isToday = (date) => {
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

const dateToDayString = (date) => {
    return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
};

const dateToTimeString = (date) => {
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const hour = (hours % 12) || 12;
    const ampm = (hours >= 12) ? ' PM' : ' AM';
    return hour + ':' + String(minutes).padStart(2, '0') + ampm;
};


// --- EVENT STYLING AND CALCULATION (KEPT HERE) ---

/**
 * Calculates the CSS style for timed events in Day/Week view.
 */
const getEventStyle = (event, slotHour, slotMinute) => {
    // 1. Check if the event starts at this specific time slot
    if (event.start.getHours() !== slotHour || event.start.getMinutes() !== slotMinute) {
        return { display: 'none' };
    }

    // 2. Calculate duration in minutes
    let startMinutes = event.start.getHours() * 60 + event.start.getMinutes();
    let endMinutes = event.end ? event.end.getHours() * 60 + event.end.getMinutes() : startMinutes + SLOT_DURATION_MINUTES;

    // Handle events that start and end on the same day but span the 24:00 boundary (optional, but good practice)
    if (event.end && event.end < event.start) {
        endMinutes += 24 * 60;
    }

    // Clip events that start before the calendar's START_HOUR
    if (startMinutes < START_HOUR * 60) {
        startMinutes = START_HOUR * 60;
    }

    // Clip events that end after the calendar's END_HOUR
    if (endMinutes > END_HOUR * 60) {
        endMinutes = END_HOUR * 60;
    }

    // 3. Calculate total duration in minutes
    const durationMinutes = endMinutes - startMinutes;

    // 4. Calculate height in 'rem' based on the duration (e.g., 30 min = 2.5rem)
    const heightInRem = (durationMinutes / SLOT_DURATION_MINUTES) * SLOT_HEIGHT_REM;

    return {
        height: `${heightInRem}rem`,
        // Start position is always top-0 because we only render it in the starting slot
        top: '0',
    };
};


// --- STATE MANAGEMENT ---

const currentConfig = reactive({
    date: props.initialDate instanceof Date ? props.initialDate : new Date(props.initialDate),
    mode: props.initialMode,
});

// Watch for initialDate prop changes... (unchanged)
watch(() => props.initialDate, (newDate) => {
    const incomingDate = newDate instanceof Date ? newDate : new Date(newDate);
    if (dateToDayString(currentConfig.date) !== dateToDayString(incomingDate)) {
        currentConfig.date = incomingDate;
        currentConfig.mode = 'day';
        emit('update:date', currentConfig.date);
        emit('update:mode', 'day');
    }
}, { immediate: true });

// Watch for initialMode prop changes... (unchanged)
watch(() => props.initialMode, (newMode) => {
    currentConfig.mode = newMode;
});


// --- CALENDAR INTERACTION FUNCTIONS (KEPT HERE) ---

/**
 * Changes the `currentConfig.date` by a specified amount and unit.
 */
const changeDate = (amount, unit) => {
    const newDate = new Date(currentConfig.date);
    const effectiveUnit = (unit === 'list' || unit === 'month') ? 'month' : unit;

    switch (effectiveUnit) {
        case 'month': newDate.setMonth(newDate.getMonth() + amount); break;
        case 'week': newDate.setDate(newDate.getDate() + amount * 7); break;
        case 'day': newDate.setDate(newDate.getDate() + amount); break;
    }
    currentConfig.date = newDate;
    emit('update:date', newDate);
};

/**
 * Sets the current view mode of the calendar and notifies the parent.
 */
const setMode = (mode) => {
    currentConfig.mode = mode;
    emit('update:mode', mode);
};

/**
 * Sets the current view mode to 'day' and updates the date to the selected day.
 * Used by ListView and MonthGridView.
 */
const selectDate = (date) => {
    currentConfig.date = date;
    currentConfig.mode = 'day';
    emit('update:date', date);
    emit('update:mode', 'day');
};

/**
 * Sets the current view mode to 'day' and updates the date to the event's start day, then notifies parent.
 * Used by all child components.
 */
const selectEvent = (event) => {
    // We navigate to the event's start date
    currentConfig.date = event.start;
    currentConfig.mode = 'day';
    emit('update:date', event.start);
    emit('update:mode', 'day');
    emit('eventSelected', event.id); // Notify parent of the event ID
};

/**
 * Resets the calendar to the current date and sets the view to 'list'.
 */
const goToToday = () => {
    const today = new Date();
    currentConfig.date = today;
    currentConfig.mode = 'list';
    emit('update:date', today);
    emit('update:mode', 'list');
};

/**
 * Emits a request to create an appointment at a specific date/time.
 * Used by MonthGridView and TimeGridView.
 */
const emitDateClick = (date, hour = null, minute = null) => {
    const newDate = new Date(date);
    emit('dateClicked', newDate, hour, minute);
};


/**
 * Filters the global event data to find events relevant to a specific day.
 */
const getEventsForDay = (date) => {
    const targetDate = dateToDayString(date);

    return props.data.filter(event => {
        const eventStart = event.start;
        const eventEnd = event.end || eventStart;

        const startDateString = dateToDayString(eventStart);
        const endDateString = dateToDayString(eventEnd);

        return targetDate >= startDateString && targetDate <= endDateString;
    });
};


// --- COMPUTED PROPERTIES FOR VIEWS (KEPT HERE) ---

const getDaysInMonth = (date) => new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

const sortedEvents = computed(() => {
    const monthEvents = [];
    const currentDay = currentConfig.date;
    const year = currentDay.getFullYear();
    const month = currentDay.getMonth();
    const daysInMonth = getDaysInMonth(currentDay);

    for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
        const date = new Date(year, month, dayNum);
        const eventsForDay = getEventsForDay(date);

        if (eventsForDay.length > 0) {
            eventsForDay.sort((a, b) => {
                if (a.allDay && !b.allDay) return -1;
                if (!a.allDay && b.allDay) return 1;
                return (+a.start) - (+b.start);
            });

            monthEvents.push({
                date: date,
                events: eventsForDay,
            });
        }
    }
    return monthEvents;
});


const dateGrid = computed(() => {
    const first = new Date(currentConfig.date.getFullYear(), currentConfig.date.getMonth(), 1);
    const startingDayIndex = first.getDay();
    const grid = [];

    let currentDate = new Date(first);
    currentDate.setDate(currentDate.getDate() - startingDayIndex);

    for (let j = 0; j < 6; j++) {
        const week = [];
        let hasDatesInCurrentMonth = false;

        for (let i = 0; i < 7; i++) {
            const day = new Date(currentDate);

            if (day.getMonth() === currentConfig.date.getMonth()) {
                hasDatesInCurrentMonth = true;
            }

            const allEvents = getEventsForDay(day);
            const allDayEvents = allEvents.filter(e => e.allDay).sort((a, b) => (+a.start) - (+b.start));
            const timeEvents = allEvents.filter(e => !e.allDay).sort((a, b) => (+a.start) - (+b.start));

            week.push({
                date: day,
                dayClass: day.getMonth() !== currentConfig.date.getMonth() ? 'text-gray-400' : 'text-gray-900',
                isToday: isToday(day),
                events: timeEvents,
                allDayEvents: allDayEvents,
            });
            currentDate.setDate(currentDate.getDate() + 1);
        }

        if (j > 0 && !hasDatesInCurrentMonth && grid.length > 0) break;

        grid.push(week);
    }
    return grid;
});

const weekDays = computed(() => {
    const days = [];
    const startOfWeek = new Date(currentConfig.date);
    // Find Sunday of the current week
    startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());

    for (let i = 0; i < 7; i++) {
        const day = new Date(startOfWeek);
        day.setDate(startOfWeek.getDate() + i);

        const dayEvents = getEventsForDay(day);

        days.push({
            date: day,
            label: `${DAYS[i].substring(0, 3)} ${day.getDate()}`,
            isToday: isToday(day),
            events: dayEvents.filter(e => !e.allDay).sort((a, b) => (+a.start) - (+b.start)),
            allDayEvents: dayEvents.filter(e => e.allDay).sort((a, b) => (+a.start) - (+b.start)),
        });
    }
    return days;
});

const singleDay = computed(() => {
    const day = new Date(currentConfig.date);
    const allEvents = getEventsForDay(day);

    return {
        date: day,
        label: `${DAYS[day.getDay()]}, ${MONTHS[day.getMonth()]} ${day.getDate()}, ${day.getFullYear()}`,
        isToday: isToday(day),
        allDayEvents: allEvents.filter(e => e.allDay).sort((a, b) => (+a.start) - (+b.start)),
        timedEvents: allEvents.filter(e => !e.allDay).sort((a, b) => (+a.start) - (+b.start)),
    };
});
</script>

<template>
    <div class="p-4 bg-white shadow rounded-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-3 md:space-y-0">

            <div class="flex space-x-2">
                <div class="flex space-x-1">
                    <button @click="changeDate(-1, currentConfig.mode)" class="p-2 border rounded-md text-gray-700 hover:bg-gray-100" aria-label="Previous Period">&lt;</button>
                    <button @click="changeDate(1, currentConfig.mode)" class="p-2 border rounded-md text-gray-700 hover:bg-gray-100" aria-label="Next Period">&gt;</button>
                </div>
                <button
                    @click="goToToday"
                    :class="['p-2 border rounded-md text-sm transition', isToday(currentConfig.date) && currentConfig.mode === 'day' ? 'bg-[#7A0C23] text-white' : 'text-gray-700 hover:bg-gray-100']"
                >
                    Today
                </button>
            </div>

            <h3 class="text-xl font-semibold text-gray-800">
                <template v-if="currentConfig.mode === 'day'">
                    {{ singleDay.label }}
                </template>
                <template v-else-if="currentConfig.mode === 'week'">
                    {{ MONTHS[weekDays[0].date.getMonth()] }} {{ weekDays[0].date.getDate() }} - {{ MONTHS[weekDays[6].date.getMonth()] }} {{ weekDays[6].date.getDate() }}, {{ currentConfig.date.getFullYear() }}
                </template>
                <template v-else>
                    {{ MONTHS[currentConfig.date.getMonth()] }} {{ currentConfig.date.getFullYear() }}
                </template>
            </h3>

            <div class="flex space-x-1 border rounded-md p-0.5 bg-gray-100">
                <button @click="setMode('list')" :class="['py-1 px-3 text-sm rounded-md transition', currentConfig.mode === 'list' ? 'bg-[#7A0C23] shadow text-white' : 'text-gray-700 hover:bg-gray-200']">List</button>
                <button @click="setMode('month')" :class="['py-1 px-3 text-sm rounded-md transition', currentConfig.mode === 'month' ? 'bg-[#7A0C23] shadow text-white' : 'text-gray-700 hover:bg-gray-200']">Month Grid</button>
                <button @click="setMode('week')" :class="['py-1 px-3 text-sm rounded-md transition', currentConfig.mode === 'week' ? 'bg-[#7A0C23] shadow text-white' : 'text-gray-700 hover:bg-gray-200']">Week</button>
                <button @click="setMode('day')" :class="['py-1 px-3 text-sm rounded-md transition', currentConfig.mode === 'day' ? 'bg-[#7A0C23] shadow text-white' : 'text-gray-700 hover:bg-gray-200']">Day</button>
            </div>
        </div>

        <ListView
            v-if="currentConfig.mode === 'list'"
            :sorted-events="sortedEvents"
            :is-today="isToday"
            :date-to-day-string="dateToDayString"
            :date-to-time-string="dateToTimeString"
            @select-date="selectDate"
            @select-event="selectEvent"
        />

        <MonthGridView
            v-else-if="currentConfig.mode === 'month'"
            :date-grid="dateGrid"
            :days="DAYS"
            @emit-date-click="emitDateClick"
            @select-event="selectEvent"
            @select-date="selectDate"
        />

        <TimeGridView
            v-else-if="currentConfig.mode === 'week' || currentConfig.mode === 'day'"
            :view-mode="currentConfig.mode"
            :week-days="weekDays"
            :single-day="singleDay"
            :hour-slots="HOUR_SLOTS"
            :get-event-style="getEventStyle"
            :date-to-day-string="dateToDayString"
            :date-to-time-string="dateToTimeString"
            :days="DAYS"
            @emit-date-click="emitDateClick"
            @select-event="selectEvent"
        />

        <div v-else class="text-center py-12 text-gray-500">
            <p>The current view mode ({{ currentConfig.mode.toUpperCase() }}) is not recognized.</p>
        </div>
    </div>
</template>
