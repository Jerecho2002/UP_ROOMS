<script setup>
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTag } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    viewMode: { type: String, required: true }, // 'day' or 'week'
    // For 'week' mode, weekDays is an array of day objects
    weekDays: { type: Array, required: false, default: () => [] },
    // For 'day' mode, singleDay is the current day object
    singleDay: { type: Object, required: false, default: () => ({}) },
    hourSlots: { type: Array, required: true }, // Array of { hour, minute, label }
    // Utility functions passed from CalendarView
    getEventStyle: { type: Function, required: true },
    dateToTimeString: { type: Function, required: true },
});

const emit = defineEmits([
    'selectEvent',
    'selectDate', // Used to switch from week header to day view
    'emitDateClick', // Open modal at a specific time slot
]);

// Determines which day(s) to show (single element for 'day', 7 elements for 'week')
const daysToRender = computed(() => {
    return props.viewMode === 'day' ? [props.singleDay] : props.weekDays;
});

const getEventColor = (event) => {
    // Fallback if no extendedProps or specific color is set
    return event.extendedProps?.color || 'bg-red-400 border-red-600';
};

const formatEventTime = (event) => {
    const start = props.dateToTimeString(event.start);
    const end = event.end ? props.dateToTimeString(event.end) : '';
    return end ? `${start} - ${end}` : start;
};

const isTodayHeader = (day) => {
    return day && day.isToday ? 'bg-[#7A0C23] text-white' : 'text-gray-700 bg-white';
};
</script>

<template>
    <div class="time-grid-view border border-gray-200 rounded-lg shadow-lg bg-white">

        <div class="flex border-b border-gray-200">
            <div class="w-16 shrink-0 bg-gray-50 border-r"></div> <div class="flex-grow grid" :style="`grid-template-columns: repeat(${daysToRender.length}, minmax(0, 1fr))`">
                <div v-for="(day, index) in daysToRender" :key="index"
                     @click="viewMode === 'week' ? emit('selectDate', day.date) : null"
                     :class="['p-2 text-center text-sm font-semibold border-r last:border-r-0 cursor-pointer transition duration-150', isTodayHeader(day)]"
                >
                    {{ day.label }}
                </div>
            </div>
        </div>

        <div class="flex border-b border-gray-200">
            <div class="w-16 shrink-0 border-r py-2 px-1 text-xs font-medium text-gray-500 flex items-center justify-center bg-gray-50">
                All Day
            </div>

            <div class="flex-grow grid" :style="`grid-template-columns: repeat(${daysToRender.length}, minmax(0, 1fr))`">
                <div v-for="(day, index) in daysToRender" :key="index" class="p-1 border-r last:border-r-0 border-gray-200">
                    <div v-for="event in day.allDayEvents" :key="event.id"
                         @click="emit('selectEvent', event)"
                         class="text-xs p-1 mb-0.5 rounded-md cursor-pointer bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition truncate border-l-2 border-indigo-600"
                         :title="event.title || event.extendedProps?.subject"
                    >
                        <FontAwesomeIcon :icon="faTag" class="w-3 h-3 mr-1" />
                        {{ event.title || event.extendedProps?.subject }}
                    </div>
                    <p v-if="day.allDayEvents.length === 0" class="text-xs text-gray-400 italic py-1 text-center">None</p>
                </div>
            </div>
        </div>

        <div class="flex h-full max-h-[70vh] overflow-y-auto relative">

            <div class="w-16 shrink-0 border-r bg-gray-50 sticky left-0 z-30">
                <div class="relative w-full">
                    <div v-for="(slot, index) in hourSlots" :key="index"
                        class="absolute w-full text-xs text-gray-500 text-right pr-2"
                        :style="`top: ${index * 1.25}rem; height: 1.25rem;`"
                    >
                        <span v-if="slot.minute === 0" class="absolute top-[-0.5em] right-0 block w-full">{{ slot.label }}</span>
                    </div>
                </div>
            </div>


            <div class="flex-grow grid relative" :style="`grid-template-columns: repeat(${daysToRender.length}, minmax(0, 1fr))`">

                <div v-for="(day, dayIndex) in daysToRender" :key="dayIndex"
                     class="border-r last:border-r-0 relative"
                >
                    <div v-for="(slot, slotIndex) in hourSlots" :key="slotIndex"
                         @click="emit('emitDateClick', day.date, slot.hour, slot.minute)"
                         :class="['h-5 border-b border-dashed border-gray-100 hover:bg-blue-50/50 transition duration-100']"
                    >
                    </div>

                    <div v-for="event in day.events" :key="event.id"
                         @click="emit('selectEvent', event)"
                         class="absolute inset-x-0 mx-0.5 p-1 rounded-md cursor-pointer text-white shadow-md z-20 overflow-hidden border-l-4"
                         :class="[getEventColor(event)]"
                         :style="getEventStyle(event, event.start.getHours(), event.start.getMinutes())"
                         :title="event.title || event.extendedProps?.subject"
                    >
                        <p class="text-[0.65rem] font-bold leading-tight truncate">{{ event.title || event.extendedProps?.subject }}</p>
                        <p class="text-[0.6rem] leading-tight">{{ formatEventTime(event) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/*
    The time slot height is 1.25rem (20px), and we have 4 slots per hour
    (one for every 15 minutes), meaning 5rem (80px) per hour.
    We use 1.25rem in the time axis to align with the 4 slots.
    The h-5 class is 1.25rem (5 * 0.25rem = 1.25rem)
*/
.time-grid-view {
    /* Use flex to structure the header and the scrollable content */
    display: flex;
    flex-direction: column;
}

/* Ensure the absolute positioning of the time labels aligns correctly with the grid lines */
.w-16 {
    min-width: 4rem;
}

.bg-\[\#7A0C23\] {
    background-color: #7A0C23;
}
</style>
