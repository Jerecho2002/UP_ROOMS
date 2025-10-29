<script setup>
import { ref, computed, onMounted } from 'vue';
// Assuming the path to your components is correct as provided
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
// Reference to the TableComponent to access its exposed data
const tableListRef = ref(null);

// Reactive state to hold the raw schedule items from TableComponent
const rawScheduleItems = ref([]);

// Configuration state for the CalendarView, using a Date object for the focused day.
const calendarConfig = ref({
    date: new Date(), // Initialize with today's Date object
    mode: 'list',
});

// Once the component is mounted, grab the initial data exposed by the TableList
onMounted(() => {
    // Check if the ref and the exposed property exist before assigning
    if (tableListRef.value && tableListRef.value.scheduleItems) {
        rawScheduleItems.value = tableListRef.value.scheduleItems;
    }
});

// --- Date/Time Parsing Utility (Crucial for data transformation) ---
/**
 * Converts a date string ('YYYY-MM-DD') and a time string ('HH:MM AM-HH:MM PM' or '')
 * into a structured event object with proper Date objects for start and end times.
 * @param {object} item - The raw schedule item.
 * @returns {object} The transformed calendar event object.
 */
const transformToCalendarEvent = (item) => {
    const allDay = !item.time;
    let startDate, endDate = null;

    if (allDay) {
        // For all-day events, the 'start' date is the day itself at midnight
        startDate = new Date(item.appointmentDay + 'T00:00:00');
    } else {
        // For timed events, parse the start and end times
        const [startTimeStr, endTimeStr] = item.time.split('-');

        // Parse start time
        // Note: Using a helper function (not visible in provided code) is best practice,
        // but for simplicity, we rely on the browser's date parsing here.
        // The date part is crucial for making the Date object valid.
        startDate = new Date(item.appointmentDay + ' ' + startTimeStr);
        
        // Parse end time (optional)
        if (endTimeStr) {
            endDate = new Date(item.appointmentDay + ' ' + endTimeStr);
        }
    }
    
    return {
        id: item.id,
        title: item.title,
        // The 'start' and 'end' must be Date objects for CalendarView
        start: startDate, 
        end: endDate,
        allDay: allDay,
    };
};

/**
 * Computed property to convert the raw schedule items into the format 
 * required by the CalendarView component.
 */
const calendarEvents = computed(() => {
    return rawScheduleItems.value.map(transformToCalendarEvent);
});


// --- HANDLERS ---

/**
 * Handles the 'view-details' event emitted by the TableList component.
 * It switches to the calendar view and focuses on the selected date in 'day' mode.
 * @param {string} dateString - The 'YYYY-MM-DD' date string from the schedule item.
 */
const handleViewDetails = (dateString) => {
    // 1. Switch the main layout view to 'calendar'
    currentView.value = 'calendar';

    // 2. Create a new Date object from the date string.
    // Setting it to a safe time like midday prevents timezone issues from shifting the day.
    const newFocusDate = new Date(dateString + 'T12:00:00');

    // 3. Update the calendar configuration to focus on the new date and switch to 'day' mode
    calendarConfig.value = {
        date: newFocusDate,
        mode: 'day',
    };
};

/**
 * Handlers for CalendarView updates (if needed to sync back to the parent state)
 */
const handleCalendarDateUpdate = (newDate) => {
    calendarConfig.value.date = newDate;
};

const handleCalendarModeUpdate = (newMode) => {
    calendarConfig.value.mode = newMode;
};

// Expose internal state/methods if this layout itself is intended to be used by a grand-parent
// defineExpose({}); 
</script>

<template>
   <div class="flex pt-14 min-h-screen transition-all duration-300">

        

        <Sidebar :sidebarOpen="sidebarOpen" />



        <div class="flex flex-col flex-1 overflow-hidden">

            

            <Navbar @toggleSidebar="toggleSidebar" />


            
            <main class="p-6">
                <div class="mb-6 flex justify-center space-x-4">
                    <button 
                        @click="setView('table')" 
                        :class="['px-6 py-2 rounded-lg font-medium transition', currentView === 'table' ? 'bg-[#7A0C23] text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-100 border']"
                    >
                        Schedule List Table 📋
                    </button>
                    <button 
                        @click="setView('calendar')" 
                        :class="['px-6 py-2 rounded-lg font-medium transition', currentView === 'calendar' ? 'bg-[#7A0C23] text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-100 border']"
                    >
                        Calendar View 🗓️
                    </button>
                </div>
                
                <div v-if="currentView === 'table'" class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Scheduled Appointments List</h2>
                    <TableList 
                        ref="tableListRef" 
                        @view-details="handleViewDetails" 
                    />
                </div>

                <div v-else-if="currentView === 'calendar'" class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Calendar View</h2>
                    <CalendarView 
                        :data="calendarEvents" 
                        :initial-date="calendarConfig.date"
                        :initial-mode="calendarConfig.mode"
                        @update:date="handleCalendarDateUpdate"
                        @update:mode="handleCalendarModeUpdate"
                        @event-selected="() => {}"
                    />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Add any necessary styles here */
</style>