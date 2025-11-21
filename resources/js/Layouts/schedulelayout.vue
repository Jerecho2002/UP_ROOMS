<script setup>
import {
    ref,
    computed,
    onMounted,
    nextTick
} from 'vue';

// Component Imports
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import CalendarView from '@/Components/ScheduleModal/CalendarView.vue';
import AppointmentModal from '@/Components/ScheduleModal/AppointmentModal.vue';
import TableComponent from '@/Components/ScheduleModal/TableComponent.vue';

// --- INITIAL DUMMY DATA ---
// Helper to create a Date object consistently
const createDate = (dateStr, timeStr) => {
    if (timeStr) {
        const [h, m] = timeStr.split(':').map(Number);
        const [y, M, d] = dateStr.split('-').map(Number);
        return new Date(y, M - 1, d, h, m);
    }
    const [y, M, d] = dateStr.split('-').map(Number);
    return new Date(y, M - 1, d);
}

const events = ref([
    // Dummy data
    { id: 1, title: 'Training', list: 'Work', allDay: false, start: createDate('2024-09-15', '10:00'), end: createDate('2024-09-15', '12:00') },
    { id: 2, title: 'Meeting Webinar', list: 'Work', allDay: false, start: createDate('2024-09-15', '10:00'), end: createDate('2024-09-15', '12:00') },
    { id: 3, title: 'Doctor Appointment', list: 'Personal', allDay: false, start: createDate('2025-11-07', '14:00'), end: createDate('2025-11-07', '15:00') },
]);

// --- LAYOUT & VIEW STATE ---
const sidebarOpen = ref(true);
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value);
// Initial view is set to 'table' based on the image's active state
const currentView = ref('table');

// --- CALENDAR STATE & CONTROLS ---
const currentCalendarDate = ref(new Date());
const currentCalendarMode = ref('list'); // Controls what the calendar shows
const nextEventId = computed(() => (events.value.length > 0 ? Math.max(...events.value.map(e => e.id)) : 0) + 1);

// --- MODAL STATE ---
const isModalVisible = ref(false);
const modalSelectedDate = ref(new Date().toISOString().slice(0, 10));
const modalSelectedHour = ref(null);
const modalSelectedMinute = ref(null);
const editingEvent = ref(null);

// --- UTILITIES (for parsing appointment data) ---

// Helper to format Date object into YYYY-MM-DD string
const dateToIsoDateString = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Helper to convert AM/PM time string (e.g., '2:30 PM') into [hour, minute] 24h format
const timeTo24h = (timeAmPm) => {
    if (!timeAmPm) return [0, 0];
    let [time, modifier] = timeAmPm.split(' ');
    let [hours, minutes] = time.split(':');
    let h = parseInt(hours, 10);
    let m = parseInt(minutes, 10);

    if (h === 12 && modifier === 'AM') { h = 0; }
    else if (modifier === 'PM' && h < 12) { h += 12; }

    return [h, m];
};

// --- CORE LOGIC: DATA TRANSFORMATION (Needed for saving/updating) ---

const transformDataToEvent = (data, eventId) => {
    const { title, list, appointmentDay, time, allDay } = data;
    let startDate, endDate = null;

    if (allDay) {
        startDate = createDate(appointmentDay, '00:00');
        endDate = createDate(appointmentDay, '23:59');
    } else {
        const timeParts = time.split('-');
        const [startH, startM] = timeTo24h(timeParts[0].trim());
        startDate = createDate(appointmentDay, `${String(startH).padStart(2, '0')}:${String(startM).padStart(2, '0')}`);

        const endTimeStr = timeParts[1] ? timeParts[1].trim() : null;
        if (endTimeStr) {
            const [endH, endM] = timeTo24h(endTimeStr);
            endDate = createDate(appointmentDay, `${String(endH).padStart(2, '0')}:${String(endM).padStart(2, '0')}`);
        } else {
            // Default 30-minute duration if no end time is provided
            endDate = new Date(startDate.getTime() + 30 * 60000);
        }
    }

    return {
        id: eventId,
        title,
        list,
        allDay,
        start: startDate,
        end: endDate,
    };
};


// --- HANDLERS ---
const closeModal = () => {
    isModalVisible.value = false;
    editingEvent.value = null; // Clear editing state on close
};

const handleDateClick = (date, hour, minute) => {
    editingEvent.value = null;
    modalSelectedDate.value = dateToIsoDateString(date);
    modalSelectedHour.value = hour;
    modalSelectedMinute.value = minute;
    isModalVisible.value = true;
};

const handleAppointmentSuccess = (data) => {
    const eventId = editingEvent.value ? editingEvent.value.id : nextEventId.value;
    const newOrUpdatedEvent = transformDataToEvent(data, eventId);

    if (editingEvent.value) {
        const index = events.value.findIndex(e => e.id === eventId);
        if (index !== -1) {
            events.value[index] = newOrUpdatedEvent;
        }
    } else {
        events.value.push(newOrUpdatedEvent);
    }

    closeModal();

    currentCalendarDate.value = newOrUpdatedEvent.start;
    currentCalendarMode.value = newOrUpdatedEvent.allDay ? 'list' : 'day';
};

const handleAddAppointment = () => {
    editingEvent.value = null;
    const dateToFocus = currentView.value === 'calendar'
        ? dateToIsoDateString(currentCalendarDate.value)
        : new Date().toISOString().slice(0, 10);

    modalSelectedDate.value = dateToFocus;
    modalSelectedHour.value = null;
    modalSelectedMinute.value = null;
    isModalVisible.value = true;
}


// --- TABLE COMPONENT HANDLERS ---

const selectEventInCalendar = (event) => {
    currentView.value = 'calendar';
    currentCalendarDate.value = event.start;
    currentCalendarMode.value = event.allDay ? 'list' : 'day';
};

const handleEditEvent = (event) => {
    editingEvent.value = event;
    modalSelectedDate.value = dateToIsoDateString(event.start);
    modalSelectedHour.value = event.start.getHours();
    modalSelectedMinute.value = event.start.getMinutes();
    isModalVisible.value = true;
};

const handleDeleteEvent = (eventId) => {
    if (confirm('Are you sure you want to delete this appointment?')) {
        events.value = events.value.filter(e => e.id !== eventId);
    }
};

</script>

<template>
    <div class="bg-gray-200 font-sans min-h-screen">

        <Navbar @toggleSidebar="toggleSidebar" class="fixed top-0 left-0 right-0 z-30" />

        <div class="pt-14 min-h-screen transition-all duration-300">

            <Sidebar
                :sidebarOpen="sidebarOpen"
                :class="['fixed top-14 left-0 h-[calc(100vh-3.5rem)] z-20 transition-all duration-300 w-64',
                         // Show/hide using transform:
                         sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
            />

            <main :class="['p-6 overflow-y-auto transition-all duration-300 min-h-[calc(100vh-3.5rem)]',
                           // CRITICAL: Use margin-left to push content past the fixed sidebar when OPEN.
                           // This ensures NO GAP and NO OVERLAP by reserving the space.
                           sidebarOpen ? 'ml-64' : 'ml-0']">

                <div class="mb-6 flex items-center justify-between">
                    <div class="text-2xl font-semibold text-[#7A0C23]">Schedule</div>
                    <div class="text-sm text-gray-500">UPCEBU > SCHEDULES</div>
                </div>

                <div class="mb-6 flex items-center justify-between">
                    <div class="flex justify-start space-x-2">
                        <button
                            @click="currentView = 'table'"
                            :class="['px-4 py-2 rounded-lg font-medium transition flex items-center', currentView === 'table' ? 'bg-[#7A0C23] text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
                        >
                            Appointment LIST <span class="ml-2"></span>
                        </button>
                        <button
                            @click="currentView = 'calendar'"
                            :class="['px-4 py-2 rounded-lg font-medium transition flex items-center', currentView === 'calendar' ? 'bg-[#7A0C23] text-white shadow-lg' : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
                        >
                            Calendar List <span class="ml-2"></span>
                        </button>
                    </div>

                    <button
                        @click="handleAddAppointment"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition duration-150"
                    >
                        NEW APPOINTMENT
                    </button>
                </div>

                <div v-if="currentView === 'table'" class="mt-4">
                    <TableComponent
                        :events="events"
                        @view-details="selectEventInCalendar"
                        @edit-event="handleEditEvent"
                        @delete-event="handleDeleteEvent"
                    />
                </div>

                <div v-else-if="currentView === 'calendar'" class="mt-4">
                    <div class="shadow-lg rounded-lg">
                        <CalendarView
                            :data="events"
                            :initial-date="currentCalendarDate"
                            :initial-mode="currentCalendarMode"
                            @update:date="(date) => currentCalendarDate = date"
                            @update:mode="(mode) => currentCalendarMode = mode"
                            @dateClicked="handleDateClick"
                            @eventSelected="(id) => console.log('Event Selected ID:', id)"
                        />
                    </div>
                </div>

            </main>
        </div>

        <AppointmentModal
            :is-visible="isModalVisible"
            :selected-date="modalSelectedDate"
            :selected-hour="modalSelectedHour"
            :selected-minute="modalSelectedMinute"
            :editing-event="editingEvent"
            @close="closeModal"
            @success="handleAppointmentSuccess"
        />
    </div>
</template>