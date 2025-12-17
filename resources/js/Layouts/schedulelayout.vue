<script setup>
import { ref, computed, onMounted } from 'vue';

// --- CENTRALIZED COMPONENT IMPORTS ---
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AppointmentModal from '@/Components/ScheduleModal/AppointmentModal.vue';
import CalendarView from '@/Components/ScheduleModal/CalendarView.vue';
import TableComponent from '@/Components/ScheduleModal/TableComponent.vue';
import MonthGridView from '@/Components/ScheduleModal/MonthGridView.vue';
import TimeGridView from '@/Components/ScheduleModal/TimeGridView.vue';
import MessageFunction from '@/Components/Messagefunction.vue';

/* ---------------- INITIAL DUMMY DATA ---------------- */
const createDate = (dateStr, timeStr) => {
    if (timeStr) {
        const [h, m] = timeStr.split(':').map(Number);
        const [y, M, d] = dateStr.split('-').map(Number);
        return new Date(y, M - 1, d, h, m);
    }
    const [y, M, d] = dateStr.split('-').map(Number);
    return new Date(y, M - 1, d);
};

// Load from localStorage if available
const loadEventsFromStorage = () => {
    const saved = localStorage.getItem('scheduleEvents');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            // Convert string dates back to Date objects
            return parsed.map(event => ({
                ...event,
                start: new Date(event.start),
                end: event.end ? new Date(event.end) : null
            }));
        } catch (e) {
            console.error('Failed to load events from storage:', e);
        }
    }
    return [
        {
            id: 1,
            title: 'Class: CS 101',
            list: 'Class',
            allDay: false,
            start: createDate('2025-11-15', '10:00'),
            end: createDate('2025-11-15', '12:00'),
            extendedProps: {
                room: 'UG 114',
                building: 'Engineering',
                college: 'CompSci',
                subject: 'Class: CS 101',
                type: 'Class'
            }
        },
        {
            id: 2,
            title: 'Faculty Meeting',
            list: 'Meeting',
            allDay: false,
            start: createDate('2025-11-15', '14:00'),
            end: createDate('2025-11-15', '15:30'),
            extendedProps: {
                room: 'UG 114',
                building: 'Engineering',
                college: 'Admin',
                subject: 'Faculty Meeting',
                type: 'Meeting'
            }
        },
        {
            id: 3,
            title: 'All Day Event',
            list: 'Event',
            allDay: true,
            start: createDate('2025-11-20'),
            end: createDate('2025-11-20'),
            extendedProps: {
                room: 'UG 114',
                building: 'Library',
                college: 'Admin',
                subject: 'All Day Event',
                type: 'Event'
            }
        },
    ];
};

// Mock Calendar Events (The source of truth)
const events = ref(loadEventsFromStorage());

// Save to localStorage whenever events change
const saveEventsToStorage = () => {
    const eventsToSave = events.value.map(event => ({
        ...event,
        start: event.start.toISOString(),
        end: event.end ? event.end.toISOString() : null
    }));
    localStorage.setItem('scheduleEvents', JSON.stringify(eventsToSave));
};

// Mock Room Data for selection
const availableRooms = ref([
    'UG 114',
    'AVR 201',
    'Lab 305',
    'Main Conference Room',
]);

/* ---------------- LAYOUT STATE ---------------- */
const sidebarOpen = ref(true);
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value);
const currentView = ref('table');

/* ---------------- CALENDAR STATE ---------------- */
const currentCalendarDate = ref(new Date());
const currentCalendarMode = ref('list');
const nextEventId = computed(() =>
    (events.value.length > 0 ? Math.max(...events.value.map(e => e.id)) : 0) + 1
);

/* ---------------- MODAL STATE ---------------- */
const isModalVisible = ref(false);
const modalSelectedRoom = ref('UG 114');
const modalSelectedDate = ref(new Date().toISOString().slice(0, 10));
const modalSelectedHour = ref(null);
const modalSelectedMinute = ref(null);
const editingEvent = ref(null);

/* ---------------- TOAST STATE ---------------- */
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedRoomName = ref('');

/* Helper to trigger toast */
const triggerToast = (type, name = '') => {
    showCreateSuccess.value = false;
    showEditSuccess.value = false;
    showDeleteSuccess.value = false;

    if (type === 'create') showCreateSuccess.value = true;
    if (type === 'edit') showEditSuccess.value = true;
    if (type === 'delete') {
        showDeleteSuccess.value = true;
        deletedRoomName.value = name;
    }

    setTimeout(() => {
        showCreateSuccess.value = false;
        showEditSuccess.value = false;
        showDeleteSuccess.value = false;
    }, 3000);
};

/* ---------------- UTILITIES ---------------- */
const dateToIsoDateString = (date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

// Utility function to extract extended props from the full form data
const extractExtendedProps = (data) => ({
    room: data.room || data.selectedRoom || 'N/A',
    building: data.building || data.organization || 'N/A',
    college: data.college || data.deptOffice || 'N/A',
    subject: data.subject || data.title || data.agenda || data.name || 'Untitled',
    type: data.type || data.list || 'Event',
    requester: data.requester || 'N/A',
    description: data.description || '',
});

/* ---------------- HANDLERS ---------------- */

// --- Modal Handlers ---
const closeModal = () => {
    isModalVisible.value = false;
    editingEvent.value = null;
    modalSelectedRoom.value = availableRooms.value[0];
};

/**
 * Handles the submission of the appointment form
 */
const handleAppointmentSuccess = (data) => {
    const isEdit = !!editingEvent.value;
    const eventId = isEdit ? editingEvent.value.id : nextEventId.value;

    // Create the FullCalendar-style event object
    const newEvent = {
        id: eventId,
        title: data.title,
        list: data.type,
        allDay: data.allDay,
        start: data.start,
        end: data.end,
        extendedProps: extractExtendedProps(data),
    };

    if (isEdit) {
        // UPDATE existing event
        const index = events.value.findIndex(e => e.id === eventId);
        if (index !== -1) {
            events.value[index] = newEvent;
        }
        triggerToast('edit');
    } else {
        // ADD new event
        events.value.push(newEvent);
        triggerToast('create');
    }

    // Save to localStorage
    saveEventsToStorage();
    closeModal();

    // If we're in calendar view, show the new event
    if (currentView.value === 'calendar') {
        selectEventInCalendar(newEvent);
    }
};

// --- View/Action Handlers ---

/**
 * Called when user wants to view event details
 */
const selectEventInCalendar = (event) => {
    currentView.value = 'calendar';
    currentCalendarDate.value = event.start;
    currentCalendarMode.value = 'day';
};

/**
 * Opens the modal to edit an existing event
 */
const handleEditEvent = (event) => {
    editingEvent.value = event;
    modalSelectedRoom.value = event.extendedProps.room || availableRooms.value[0];
    modalSelectedDate.value = dateToIsoDateString(event.start);

    if (!event.allDay) {
        modalSelectedHour.value = event.start.getHours();
        modalSelectedMinute.value = event.start.getMinutes();
    } else {
        modalSelectedHour.value = null;
        modalSelectedMinute.value = null;
    }

    isModalVisible.value = true;
};

/**
 * Handles row clicks from TableComponent
 */
const handleRowClicked = (eventObject) => {
    editingEvent.value = eventObject;
    modalSelectedRoom.value = eventObject.extendedProps.room || availableRooms.value[0];
    modalSelectedDate.value = dateToIsoDateString(eventObject.start);

    if (!eventObject.allDay) {
        modalSelectedHour.value = eventObject.start.getHours();
        modalSelectedMinute.value = eventObject.start.getMinutes();
    } else {
        modalSelectedHour.value = null;
        modalSelectedMinute.value = null;
    }

    isModalVisible.value = true;
};

/**
 * Handles event deletion
 */
const handleDeleteEvent = (eventObject) => {
    if (confirm(`Are you sure you want to delete the appointment: ${eventObject?.title}?`)) {
        events.value = events.value.filter(e => e.id !== eventObject.id);
        saveEventsToStorage();
        triggerToast('delete', eventObject?.title || 'Appointment');
    }
};

/**
 * Handles clicks on the calendar grid to create a new appointment
 */
const handleDateClick = (date, hour = null, minute = null) => {
    const selectedRoom = prompt(`Please enter the room for this date/time (Options: ${availableRooms.value.join(', ')}):`, availableRooms.value[0]);

    if (selectedRoom && availableRooms.value.includes(selectedRoom)) {
        editingEvent.value = null;
        modalSelectedRoom.value = selectedRoom;
        modalSelectedDate.value = dateToIsoDateString(date);
        modalSelectedHour.value = hour;
        modalSelectedMinute.value = minute;
        isModalVisible.value = true;
    } else if (selectedRoom !== null) {
        alert('Invalid room selected. Please try again.');
    }
};

/**
 * Handle add appointment from CalendarView's list view
 */
const handleAddAppointment = () => {
    editingEvent.value = null;
    modalSelectedRoom.value = availableRooms.value[0];
    modalSelectedDate.value = dateToIsoDateString(new Date());
    modalSelectedHour.value = 9; // Default to 9 AM
    modalSelectedMinute.value = 0;
    isModalVisible.value = true;
};
</script>

<template>
    <div class="bg-gray-200 font-sans min-h-screen">
        <MessageFunction
            :show-create-success="showCreateSuccess"
            :show-edit-success="showEditSuccess"
            :show-delete-success="showDeleteSuccess"
            :deleted-room-name="deletedRoomName"
        />

        <Navbar @toggleSidebar="toggleSidebar" class="fixed top-0 left-0 right-0 z-30" />

        <div class="pt-14 min-h-screen transition-all duration-300">
            <Sidebar
                :sidebarOpen="sidebarOpen"
                :class="['fixed top-14 left-0 h-[calc(100vh-3.5rem)] z-20 transition-all duration-300 w-64',
                        sidebarOpen ? 'translate-x-0' : '-translate-x-full']"
            />

            <main :class="['p-6 overflow-y-auto transition-all duration-300',
                            sidebarOpen ? 'ml-64' : 'ml-0']">

                <div class="mb-6 flex items-center justify-between">
                    <div class="text-2xl font-semibold text-[#7A0C23]">Schedule</div>
                    <div class="text-sm text-gray-500">UPCEBU > SCHEDULES</div>
                </div>

                <div class="mb-6 flex items-center justify-between">
                    <div class="flex justify-start space-x-2">
                        <button
                            @click="currentView = 'table'"
                            :class="['px-4 py-2 rounded-lg font-medium transition flex items-center border-2',
                                currentView === 'table'
                                    ? 'bg-[#7A0C23] text-white shadow-lg border-[#7A0C23]'
                                    : 'bg-white text-[#7A0C23] border-[#7A0C23] hover:bg-red-50']"
                        >
                            Appointment List
                        </button>

                        <button
                            @click="currentView = 'calendar'"
                            :class="['px-4 py-2 rounded-lg font-medium transition flex items-center border-2',
                                currentView === 'calendar'
                                    ? 'bg-[#7A0C23] text-white shadow-lg border-[#7A0C23]'
                                    : 'bg-white text-[#7A0C23] border-[#7A0C23] hover:bg-red-50']"
                        >
                            Calendar View
                        </button>
                    </div>

                    <button
                        @click="handleAddAppointment"
                        class="px-4 py-2 bg-[#7A0C23] text-white rounded-lg hover:bg-red-800 transition font-medium flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Appointment
                    </button>
                </div>

                <!-- Table View -->
                <TableComponent
                    v-if="currentView === 'table'"
                    :events="events"
                    @view-details="selectEventInCalendar"
                    @edit-event="handleEditEvent"
                    @delete-event="handleDeleteEvent"
                    @row-clicked="handleRowClicked"
                />

                <!-- Calendar View -->
                <CalendarView
                    v-else
                    :data="events"
                    :initial-date="currentCalendarDate"
                    :initial-mode="currentCalendarMode"
                    :ListViewComponent="TableComponent"
                    :MonthGridViewComponent="MonthGridView"
                    :TimeGridViewComponent="TimeGridView"
                    @update:date="(date) => currentCalendarDate = date"
                    @update:mode="(mode) => currentCalendarMode = mode"
                    @dateClicked="handleDateClick"
                    @selectEvent="handleEditEvent"
                    @editEvent="handleEditEvent"
                    @deleteEvent="handleDeleteEvent"
                    @addAppointment="handleAddAppointment"
                />
            </main>
        </div>

        <AppointmentModal
            :is-visible="isModalVisible"
            :selected-date="modalSelectedDate"
            :initial-room="modalSelectedRoom"
            :initial-hour="modalSelectedHour"
            :initial-minute="modalSelectedMinute"
            :editing-event="editingEvent"
            @close="closeModal"
            @success="handleAppointmentSuccess"
        />
    </div>
</template>
