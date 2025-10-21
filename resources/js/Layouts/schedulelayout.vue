<script setup>
import { ref, computed } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import TableList from '@/Components/ScheduleModal/TableComponent.vue';
import CalendarView from '@/Components/ScheduleModal/CalendarView.vue';

// --- Layout State ---
const sidebarOpen = ref(true);
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value);

// --- View Management State (NEW) ---
// Controls whether to show 'table' or 'calendar'
const currentView = ref('table');
const setView = (view) => {
    currentView.value = view;
};

// --- Schedule Data and View Management ---
const tableListRef = ref(null);

// Configuration state for the CalendarView, using a Date object for the focused day.
const calendarConfig = ref({
    date: new Date(), // Initialize with today's Date object
    mode: 'list',
});

// --- Date/Time Parsing Utility (Crucial for data transformation) ---
/**
 * Converts date string ('YYYY-MM-DD') and time string ('HH:MM AM/PM') to a Date object.
 */
const createDateTime = (dateStr, timeStr) => {
    const trimmedTimeStr = timeStr?.trim();
    if (!dateStr) return null;

    // Start with a new Date based on the date string (gets local midnight for that day)
    const date = new Date(dateStr); 
    if (!trimmedTimeStr) {
        return date; // Return the date set to local midnight if no time is specified
    }

    // Parse time components
    const parts = trimmedTimeStr.split(' ');
    if (parts.length < 2) return date; 

    const [time, ampm] = parts;
    let [hours, minutes] = time.split(':').map(Number);
    
    // Convert to 24-hour format
    if (ampm === 'PM' && hours !== 12) {
        hours += 12;
    } else if (ampm === 'AM' && hours === 12) {
        hours = 0; // Midnight (12:xx AM)
    }

    // Apply hours and minutes using local setters
    date.setHours(hours, minutes, 0, 0);

    return date;
};

// --- Computed Property: Data Transformation ---

const calendarEvents = computed(() => {
    const items = tableListRef.value?.scheduleItems;
    if (!items || items.length === 0) {
        return [];
    }

    return items.map(item => {
        const [startTimeStr, endTimeStr] = item.time.split('-').map(s => s.trim());
        const startTime = createDateTime(item.appointmentDay, startTimeStr);

        let endTime = endTimeStr ? createDateTime(item.appointmentDay, endTimeStr) : null;
        
        if (!endTime && startTime) {
            endTime = new Date(startTime.getTime() + 30 * 60000); // 30 mins later
        }

        return {
            id: item.id,
            title: item.title || item.list,
            start: startTime, 
            end: endTime,
            allDay: false, 
        };
    }).filter(event => event.start !== null); 
});

// --- Event Handlers (Linking Table to Calendar) ---

// Handler for the 'view-details' event emitted from TableList
const handleViewDetails = (dateString) => {
    // 1. Switch the view to the calendar
    currentView.value = 'calendar'; // <-- Switches view on click!
    // 2. Update the calendar config to focus on the date and mode
    calendarConfig.value.date = new Date(dateString); 
    calendarConfig.value.mode = 'day'; 
};

// Handlers for events emitted from CalendarView (keeping config in sync)
const handleUpdateDate = (newDate) => {
    calendarConfig.value.date = newDate;
};

const handleUpdateMode = (newMode) => {
    calendarConfig.value.mode = newMode;
};
</script>

<template>
    <div class="flex pt-14 min-h-screen transition-all duration-300">
        
        <Sidebar :sidebarOpen="sidebarOpen" />

        <div class="flex flex-col flex-1 overflow-hidden">
            
            <Navbar @toggleSidebar="toggleSidebar" />

            <main class="flex-1 overflow-y-auto p-0 md:p-6 bg-gray-100">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Room Schedules</h2>
                        
                        <div class="flex bg-gray-100 rounded-full p-1 shadow-inner">
                            <button 
                                @click="setView('table')" 
                                :class="[
                                    'py-2 px-6 rounded-full text-sm font-semibold transition-colors duration-200',
                                    currentView === 'table' ? 'bg-red-700 text-white shadow-md' : 'text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Table
                            </button>
                            <button 
                                @click="setView('calendar')" 
                                :class="[
                                    'py-2 px-6 rounded-full text-sm font-semibold transition-colors duration-200',
                                    currentView === 'calendar' ? 'bg-red-700 text-white shadow-md' : 'text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Calendar
                            </button>
                        </div>
                        </div>
                    
                    <hr class="my-6">

                    <div v-if="currentView === 'table'" class="transition-opacity duration-300">
                        <TableList 
                            ref="tableListRef" 
                            @view-details="handleViewDetails" 
                            class="mb-8"
                        />
                    </div>
                    
                    <div v-else-if="currentView === 'calendar'" class="transition-opacity duration-300">
                        <CalendarView
                            :initial-date="calendarConfig.date"
                            :initial-mode="calendarConfig.mode"
                            :data="calendarEvents"
                            @update:date="handleUpdateDate"
                            @update:mode="handleUpdateMode"
                            @event-selected="(id) => console.log('Event selected:', id)"
                        />
                    </div>
                    </div>
            </main>
        </div>
    </div>
</template>