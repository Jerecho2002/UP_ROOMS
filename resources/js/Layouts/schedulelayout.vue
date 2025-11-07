<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import TableList from '@/Components/ScheduleModal/TableComponent.vue';
import CalendarView from '@/Components/ScheduleModal/CalendarView.vue';
import ScheduleModal from '@/Components/ScheduleModal/ScheduleModal.vue'; 

// --- Layout State ---
const sidebarOpen = ref(true);
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value);

// --- View Management State ---
const currentView = ref('table');
const setView = (view) => {
    currentView.value = view;
};

// --- Modal State ---
const isModalOpen = ref(false);
const modalSelectedDate = ref(new Date().toISOString().slice(0, 10));

const openModal = (dateString = new Date().toISOString().slice(0, 10)) => {
    modalSelectedDate.value = dateString;
    isModalOpen.value = true;
};
const closeModal = () => {
    isModalOpen.value = false;
};

// --- Schedule Data and View Management ---
const tableListRef = ref(null);
const rawScheduleItems = ref([]); 

// Reliable function to get data from the TableList component
const syncRawScheduleItems = () => {
    // Check if the ref exists and exposes the scheduleItems property
    if (tableListRef.value && tableListRef.value.scheduleItems) {
        // Accessing the .value of the ref exposed by the child
        rawScheduleItems.value = tableListRef.value.scheduleItems.value;
        console.log('Data synced successfully:', rawScheduleItems.value.length, 'items.');
    } else {
        // Fallback or debug logging
        // console.warn("TableList reference or its scheduleItems is not available yet.");
    }
};

onMounted(() => {
    // Use nextTick to ensure the child component is rendered before attempting to access its ref
    nextTick(() => {
        syncRawScheduleItems(); 
    });
});

// --- Date/Time Parsing Utility (Crucial for data transformation) ---
const transformToCalendarEvent = (item) => {
    // Check for required fields
    if (!item || !item.appointmentDay || !item.title) {
        console.error("Invalid item data:", item);
        return null;
    }

    const allDay = !item.time || item.time.trim() === '';
    let startDate = null;
    let endDate = null;

    if (allDay) {
        // For all-day events, use midday to avoid timezone issues with Date objects
        startDate = new Date(item.appointmentDay + 'T12:00:00');
        // Set end date to the *next* day for all-day visualization on calendars
        endDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000); 
    } else {
        // Improved time parsing utility
        const convertTo24Hour = (timeStr) => {
            const timePart = timeStr.trim();
            const ampm = timePart.includes('AM') ? 'AM' : (timePart.includes('PM') ? 'PM' : '');
            let [hours, minutes] = timePart.replace(ampm, '').trim().split(':');
            
            hours = parseInt(hours) || 0;
            minutes = parseInt(minutes) || 0;

            if (ampm === 'PM' && hours !== 12) hours += 12;
            if (ampm === 'AM' && hours === 12) hours = 0; // Midnight 12AM is 00
            
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
        };

        const [startTimeStr, endTimeStr] = item.time.split('-').map(s => s.trim());
        
        try {
            const startTime24h = convertTo24Hour(startTimeStr);
            startDate = new Date(item.appointmentDay + 'T' + startTime24h + ':00');
            
            if (endTimeStr) {
                const endTime24h = convertTo24Hour(endTimeStr);
                endDate = new Date(item.appointmentDay + 'T' + endTime24h + ':00');
            } else {
                // Default 1-hour duration if end time is missing
                endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
            }
        } catch (e) {
            console.error("Error parsing time for item:", item, e);
            return null;
        }
    }
    
    return {
        id: item.id,
        title: item.title,
        list: item.list || 'Appointment', 
        start: startDate, 
        end: endDate,
        allDay: allDay,
    };
};

/**
 * Computed property to convert the raw schedule items into the format 
 * required by the CalendarView component. Filters out nulls from failed parsing.
 */
const calendarEvents = computed(() => {
    if (!Array.isArray(rawScheduleItems.value)) return [];
    return rawScheduleItems.value.map(transformToCalendarEvent).filter(event => event !== null);
});

// Configuration state for the CalendarView
const calendarConfig = ref({
    date: new Date(), 
    mode: 'list',
});

// --- HANDLERS ---

const handleNewScheduleItem = async (newItemData) => {
    // 1. Give the new item a temporary ID and default list property
    const tempId = Math.max(0, ...rawScheduleItems.value.map(i => i.id || 0)) + 1;
    const itemWithId = { ...newItemData, id: newItemData.id || tempId, list: newItemData.list || 'New Appointment' };

    // 2. Add the new item to the TableList's internal data
    if (tableListRef.value && tableListRef.value.addItem) {
        tableListRef.value.addItem(itemWithId);
    } else {
        // Fallback for calendar view if TableList ref is not ready
        rawScheduleItems.value.push(itemWithId);
    }

    // 3. Ensure reactivity updates have processed before re-syncing
    await nextTick(); 
    syncRawScheduleItems(); 

    // 4. Switch to calendar view and focus on the new date
    currentView.value = 'calendar';
    const newFocusDate = new Date(itemWithId.appointmentDay + 'T12:00:00');

    calendarConfig.value = {
        date: newFocusDate,
        mode: itemWithId.time ? 'day' : 'month', 
    };
    closeModal();
};

const handleViewDetails = (dateString) => {
    currentView.value = 'calendar';
    const newFocusDate = new Date(dateString + 'T12:00:00');
    calendarConfig.value = {
        date: newFocusDate,
        mode: 'day',
    };
};

const handleCalendarDateClick = (date) => {
    const dateString = date.toISOString().slice(0, 10);
    openModal(dateString);
};

const handleCalendarDateUpdate = (newDate) => {
    calendarConfig.value.date = newDate;
};

const handleCalendarModeUpdate = (newMode) => {
    calendarConfig.value.mode = newMode;
};
</script>

<template>
    <div class="bg-gray-100 font-sans min-h-screen">
        
        <Navbar @toggleSidebar="toggleSidebar" class="fixed top-0 left-0 right-0 z-30" />

        <div class="flex pt-14 min-h-screen transition-all duration-300">
            
            <Sidebar 
                :sidebarOpen="sidebarOpen" 
                v-show="sidebarOpen" 
                class="fixed top-14 left-0 h-[calc(100vh-3.5rem)] z-20 w-64 lg:relative lg:top-0 lg:h-full transition-all duration-300" 
            />

            <main class="flex-1 p-6 overflow-y-auto">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex justify-start space-x-4">
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

                    <button
                        @click="openModal()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition duration-150"
                    >
                        + Add Appointment
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
                        @date-clicked="handleCalendarDateClick" />
                </div>
            </main>
        </div>

        <ScheduleModal 
            :isVisible="isModalOpen"
            :selectedDate="modalSelectedDate"
            @close="closeModal"
            @success="handleNewScheduleItem" />
    </div>
</template>

<style scoped>
/* Add any necessary styles here */
</style>