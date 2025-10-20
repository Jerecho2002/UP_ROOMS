<script setup>
import { ref, computed, onMounted, reactive } from 'vue';

// --- GLOBAL CONSTANTS & HELPERS ---

// Days and Months for calendar display
const DAYS = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
const MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const SHORT_MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

// Time Slot Configuration for Week/Day View (6:00 AM to 10:00 PM)
const START_HOUR = 6;
const END_HOUR = 22; 
const HOUR_SLOTS = Array.from({ length: (END_HOUR - START_HOUR) * 2 }, (_, i) => {
    const hour = START_HOUR + Math.floor(i / 2);
    const minute = (i % 2) * 30;
    // Creates labels like "6:00 AM", "6:30 AM", "10:00 PM"
    return { hour, minute, label: `${hour <= 12 ? hour : hour - 12}:${minute === 0 ? '00' : '30'} ${hour < 12 || hour === 24 ? 'AM' : 'PM'}` };
});

/**
 * Helper function to check if a given date object is today's date.
 * @param {Date} date - The date to check.
 * @returns {boolean} True if the date is today.
 */
const isToday = (date) => {
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

// Extend Date prototype methods needed for consistency in event comparison and display.
if (!Date.prototype.toDateInt) {
    /** Converts date to a single integer for simple comparison (Year*12*32 + Month*32 + Day) */
    Date.prototype.toDateInt = function () { 
        return ((this.getFullYear()*12) + this.getMonth())*32 + this.getDate(); 
    };
    /** Returns date as YYYY-M-D string for simple comparison. */
    Date.prototype.toDateString = function() {
        return this.getFullYear() + '-' + (this.getMonth() + 1) + '-' + this.getDate();
    };
    /** Returns time as HH:MM AM/PM string for display. */
    Date.prototype.toTimeString = function() {
        const hours = this.getHours();
        const minutes = this.getMinutes();
        const hour = (hours % 12) || 12; // 0-11 -> 12, 12-23 -> 1-11
        const ampm = (hours >= 12) ? ' PM' : ' AM';
        if (hours === 0 && minutes === 0 && this.toDateString() !== new Date().toDateString()) { return ''; } // Avoid empty string on full day event span
        return hour + ':' + String(minutes).padStart(2, '0') + ampm;
    };
}

// --- STATE MANAGEMENT ---

const state = reactive({
    date: new Date(), // The currently viewed date/month
    mode: 'month',    // The current view mode ('month', 'week', or 'day')
    data: generateFakeData(), // Event data array
});

// --- CALENDAR INTERACTION FUNCTIONS ---

/**
 * Changes the `state.date` by a specified amount and unit (year, month, week, day).
 * @param {number} amount - The number of units to move (e.g., -1 for previous, 1 for next).
 * @param {string} unit - The unit of time to change ('year', 'month', 'week', or 'day').
 */
const changeDate = (amount, unit) => {
    const newDate = new Date(state.date);
    switch (unit) {
        case 'year': newDate.setFullYear(newDate.getFullYear() + amount); break;
        case 'month': newDate.setMonth(newDate.getMonth() + amount); break;
        case 'week': newDate.setDate(newDate.getDate() + amount * 7); break;
        case 'day': newDate.setDate(newDate.getDate() + amount); break;
    }
    state.date = newDate;
};

/**
 * Sets the current view mode of the calendar.
 * @param {string} mode - The new mode ('month', 'week', or 'day').
 */
const setMode = (mode) => {
    state.mode = mode;
};

/**
 * Resets the calendar to the current date and sets the view to 'month'.
 */
const goToToday = () => {
    state.date = new Date();
    state.mode = 'month';
};

// --- CORE EVENT FILTERING ---

/**
 * Filters the global event data to find events relevant to a specific day.
 * It handles multi-day events by checking if the date falls between the start and end.
 * @param {Date} date - The target day to filter events for.
 * @returns {Array<Object>} An array of events for the specified day.
 */
const getEventsForDay = (date) => {
    return state.data.filter(event => {
        const startString = event.start.toDateString();
        // Use start date if end date is missing
        const endString = event.end ? event.end.toDateString() : startString;
        const currentString = date.toDateString();
        
        // Check if the current date is between or equal to the event's start and end dates
        return currentString >= startString && currentString <= endString;
    });
};

// --- COMPUTED PROPERTIES FOR VIEWS ---

// MONTH VIEW: Generates the 6x7 grid of dates for the current month.
const dateGrid = computed(() => {
    const first = new Date(state.date.getFullYear(), state.date.getMonth(), 1);
    const startingDayIndex = first.getDay(); // 0 (Sunday) to 6 (Saturday)
    const grid = [];
    
    let currentDate = new Date(first);
    // Rewind to the first Sunday of the calendar grid
    currentDate.setDate(currentDate.getDate() - startingDayIndex); 

    for (let j = 0; j < 6; j++) { // Loop for weeks (rows)
        const week = [];
        for (let i = 0; i < 7; i++) { // Loop for days (columns)
            const day = new Date(currentDate);
            
            week.push({
                date: day,
                // Class for graying out dates from previous/next month
                dayClass: day.getMonth() !== state.date.getMonth() ? 'text-gray-400' : 'text-gray-900',
                isToday: isToday(day),
                // Filter events into time-specific and all-day categories
                events: getEventsForDay(day).filter(e => !e.allDay),
                allDayEvents: getEventsForDay(day).filter(e => e.allDay),
            });
            currentDate.setDate(currentDate.getDate() + 1);
        }
        // Break early if we've moved into the next month after the first row (saves an empty 6th row)
        if (j > 0 && currentDate.getMonth() !== state.date.getMonth()) break;

        grid.push(week);
    }
    return grid;
});

// WEEK VIEW: Generates the 7 days for the current week.
const weekDays = computed(() => {
    const days = [];
    const startOfWeek = new Date(state.date);
    // Go back to Sunday (start of the week)
    startOfWeek.setDate(state.date.getDate() - state.date.getDay()); 

    for (let i = 0; i < 7; i++) {
        const day = new Date(startOfWeek);
        day.setDate(startOfWeek.getDate() + i);
        
        // Get all events for this day
        const dayEvents = getEventsForDay(day);

        days.push({
            date: day,
            label: `${DAYS[i].substring(0, 3)} ${day.getDate()}`,
            isToday: isToday(day),
            events: dayEvents.filter(e => !e.allDay), // Events for time slots
            allDayEvents: dayEvents.filter(e => e.allDay), // Events for all-day row
        });
    }
    return days;
});

// DAY VIEW: Generates a single day's data for the current `state.date`.
const singleDay = computed(() => {
    const day = new Date(state.date);
    const allEvents = getEventsForDay(day);

    return {
        date: day,
        label: `${DAYS[day.getDay()]} ${day.getDate()}, ${MONTHS[day.getMonth()]}`,
        isToday: isToday(day),
        allDayEvents: allEvents.filter(e => e.allDay),
        timedEvents: allEvents.filter(e => !e.allDay),
    };
});


// --- FAKE DATA GENERATOR ---

/**
 * Generates an array of fake event data for demonstration purposes.
 * Events are spread around the current date.
 */
function generateFakeData() {
    const names = ['Meeting', 'Training', 'Deadline', 'Project Review', 'Team Lunch', 'Maintenance'];
    const data = [];
    const date = new Date();
    const y = date.getFullYear();
    const m = date.getMonth();
    const d1 = date.getDate();

    for(let i = 0; i < 50; i++) {
        const d = (d1 + (i % 20) - 10); // Dates around today
        const h = (i % 8) + 9; // Hours 9 AM to 4 PM
        const min = (i % 4) * 15;
        const duration = (i % 3) + 1; // 1 to 3 hours
        
        const start = new Date(y, m, d, h, min);
        const end = new Date(start);
        end.setHours(start.getHours() + duration);
        
        // Prevent event end time from incorrectly spilling over to the next day unless intended
        if (end.getDate() !== start.getDate() && !((i % 10) === 0)) {
            end.setDate(start.getDate());
        }

        data.push({ 
            id: i,
            title: names[i % names.length], 
            start: start, 
            end: (i % 6) === 0 ? null : end, // Some events without explicit end time
            allDay: (i % 10 === 0), // Some events marked as all day
            text: "Event details or description.", 
        });
    }
    
    data.sort((a,b) => (+a.start) - (+b.start));
    return data;
}

onMounted(() => {
    // Initialization logic can go here (e.g., fetching real data, reading URL params)
});

</script>

<template>
    <div class="p-4 bg-white shadow rounded-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-3 md:space-y-0">
            
            <div class="flex space-x-2">
                <div class="flex space-x-1">
                    <button @click="changeDate(-1, state.mode)" class="p-2 border rounded-md text-gray-700 hover:bg-gray-100" aria-label="Previous Period">&lt;</button>
                    <button @click="changeDate(1, state.mode)" class="p-2 border rounded-md text-gray-700 hover:bg-gray-100" aria-label="Next Period">&gt;</button>
                </div>
                <button 
                    @click="goToToday" 
                    :class="['p-2 border rounded-md text-sm transition', isToday(state.date) ? 'bg-red-600 text-white' : 'text-gray-700 hover:bg-gray-100']"
                >
                    Today
                </button>
            </div>
            
            <h3 class="text-xl font-semibold text-gray-800">
                {{ MONTHS[state.date.getMonth()] }} {{ state.date.getFullYear() }}
            </h3>
            
            <div class="flex space-x-1 border rounded-md p-0.5 bg-gray-100">
                <button @click="setMode('month')" :class="['py-1 px-3 text-sm rounded-md transition', state.mode === 'month' ? 'bg-white shadow text-gray-900' : 'text-gray-700 hover:bg-gray-200']">Month</button>
                <button @click="setMode('week')" :class="['py-1 px-3 text-sm rounded-md transition', state.mode === 'week' ? 'bg-white shadow text-gray-900' : 'text-gray-700 hover:bg-gray-200']">Week</button>
                <button @click="setMode('day')" :class="['py-1 px-3 text-sm rounded-md transition', state.mode === 'day' ? 'bg-white shadow text-gray-900' : 'text-gray-700 hover:bg-gray-200']">Day</button>
            </div>
        </div>
        
        <div v-if="state.mode === 'month'" class="w-full">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 table-fixed">
                <thead>
                    <tr class="bg-gray-50">
                        <th v-for="day in DAYS" :key="day" scope="col" class="w-1/7 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ day }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(week, index) in dateGrid" :key="index">
                        <td v-for="day in week" :key="day.date.toDateString()" 
                            :class="[
                                'p-2 align-top h-24 border border-gray-200 text-sm cursor-pointer transition duration-150',
                                day.dayClass, // Text color for dates outside the current month
                                day.isToday ? 'bg-yellow-100 ring-2 ring-yellow-500/50' : 'hover:bg-blue-50', // Highlight for today
                                day.date.toDateString() === state.date.toDateString() ? 'bg-blue-100' : '' // Highlight for selected day
                            ]">
                            
                            <div class="font-bold mb-1 text-right">{{ day.date.getDate() }}</div>
                            
                            <div class="space-y-0.5 overflow-hidden max-h-16">
                                <div v-for="event in day.allDayEvents" :key="event.id + '-all'"
                                    :class="['text-xs p-1 rounded truncate bg-green-200 text-green-800']"
                                    :title="event.title">
                                    All Day: {{ event.title }}
                                </div>
                                <div v-for="event in day.events" :key="event.id + '-time'"
                                    :class="['text-xs p-1 rounded truncate bg-indigo-200 text-indigo-800']"
                                    :title="event.title">
                                    {{ event.start.toTimeString().split(' ')[0] }} {{ event.title }}
                                </div>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div v-else-if="state.mode === 'week'" class="w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 table-fixed">
                <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="w-16 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th v-for="day in weekDays" :key="day.date.toDateString()" 
                            :class="['w-1/7 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l', day.isToday ? 'bg-yellow-100' : '']">
                            {{ day.label }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="w-16 px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">All Day</td>
                        <td v-for="day in weekDays" :key="'all-day-' + day.date.toDateString()" 
                            :class="['p-1 border border-gray-200 align-top', day.isToday ? 'bg-yellow-50' : '']">
                            <div class="space-y-0.5">
                                <div v-for="event in day.allDayEvents" :key="event.id"
                                    :class="['text-xs p-1 rounded truncate bg-green-200 text-green-800']"
                                    :title="event.title">
                                    {{ event.title }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr v-for="(slot, index) in HOUR_SLOTS" :key="index" class="h-10">
                        <td class="w-16 text-xs text-gray-500 text-right pr-2 border-r">{{ slot.label }}</td>
                        
                        <td v-for="day in weekDays" :key="day.date.toDateString() + slot.label" 
                            :class="['p-0 border border-gray-200 align-top relative', day.isToday ? 'bg-yellow-50/50' : '']">
                            
                            <template v-for="event in day.events" :key="event.id">
                                <div v-if="event.start.getHours() === slot.hour && event.start.getMinutes() === slot.minute"
                                    class="absolute top-0 left-0 w-full text-xs p-1 rounded-sm bg-indigo-500 text-white z-10 truncate"
                                    :style="{ 
                                        // Calculates height based on duration in 30-minute intervals (1 interval = 2.5rem)
                                        height: (event.end ? (event.end.getHours() * 60 + event.end.getMinutes() - event.start.getHours() * 60 - event.start.getMinutes()) / 30 * 2.5 : 2.5) + 'rem' 
                                    }"
                                    :title="event.title + ' | ' + event.start.toTimeString() + ' - ' + event.end?.toTimeString()">
                                    {{ event.start.toTimeString().split(' ')[0] }} {{ event.title }}
                                </div>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="state.mode === 'day'" class="w-full">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="w-16 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th :class="['px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l', singleDay.isToday ? 'bg-yellow-100' : '']">
                            {{ singleDay.label }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="w-16 px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">All Day</td>
                        <td :class="['p-2 border border-gray-200 align-top', singleDay.isToday ? 'bg-yellow-50' : '']">
                            <div class="space-y-0.5">
                                <div v-for="event in singleDay.allDayEvents" :key="event.id"
                                    :class="['text-xs p-1 rounded truncate bg-green-200 text-green-800']"
                                    :title="event.title">
                                    {{ event.title }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr v-for="(slot, index) in HOUR_SLOTS" :key="index" class="h-10">
                        <td class="w-16 text-xs text-gray-500 text-right pr-2 border-r">{{ slot.label }}</td>
                        <td :class="['p-0 border border-gray-200 align-top relative', singleDay.isToday ? 'bg-yellow-50/50' : '']">
                            
                            <template v-for="event in singleDay.timedEvents" :key="event.id">
                                <div v-if="event.start.getHours() === slot.hour && event.start.getMinutes() === slot.minute"
                                    class="absolute top-0 left-0 w-full text-xs p-1 rounded-sm bg-indigo-500 text-white z-10 truncate"
                                    :style="{ 
                                        height: (event.end ? (event.end.getHours() * 60 + event.end.getMinutes() - event.start.getHours() * 60 - event.start.getMinutes()) / 30 * 2.5 : 2.5) + 'rem' 
                                    }"
                                    :title="event.title + ' | ' + event.start.toTimeString() + ' - ' + event.end?.toTimeString()">
                                    {{ event.start.toTimeString().split(' ')[0] }} {{ event.title }}
                                </div>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="text-center py-12 text-gray-500">
            <p>The current view mode (**{{ state.mode.toUpperCase() }}**) is not fully implemented.</p>
        </div>
    </div>
</template>