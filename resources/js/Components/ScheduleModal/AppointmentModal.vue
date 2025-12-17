<script setup>
import { reactive, watch, computed, defineProps, defineEmits } from 'vue';
import ClassForm from './ClassForm.vue';
import MeetingForm from './MeetingForm.vue';
import EventForm from './EventForm.vue';
import OtherActivityForm from './OtherActivityForm.vue';

// props and emits
const props = defineProps({
    isVisible: Boolean,
    // Date/Time from calendar click or default
    selectedDate: { type: String, default: () => new Date().toISOString().slice(0, 10) },
    initialHour: { type: [Number, null], default: 6 },
    initialMinute: { type: [Number, null], default: 0 },
    // For editing an existing event
    editingEvent: { type: Object, default: null }
});
const emit = defineEmits(['close', 'success']);

// --- Utility for End Date/Time calculation ---
const calculateEndTime = (start, duration) => {
    const startDate = new Date(start);
    const endTime = new Date(startDate.getTime() + duration * 60000); // duration is in minutes

    // Format the end date for display
    return `${endTime.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })} ${String(endTime.getHours()).padStart(2, '0')}:${String(endTime.getMinutes()).padStart(2, '0')}`;
};

// --- Reactive form object (central state for the modal) ---
// This state object holds ALL fields from ALL form types.
const appointmentForm = reactive({
    // Common fields (matching your template)
    room: 'UG 114',
    type: 'Meeting', // Default type
    date: props.selectedDate,
    hour: props.initialHour !== null ? props.initialHour : 6,
    minute: props.initialMinute !== null ? props.initialMinute : 0,
    amPm: (props.initialHour !== null && props.initialHour >= 12) ? 'PM' : 'AM',
    durationHour: 0,
    durationMinute: 30,
    isHoliday: false,
    recurring: false,
    numberParticipants: null,
    deptOffice: '',
    organization: '',
    description: '',

    // Equipment
    tablesChairs: false,
    airConditioner: false,
    whiteboard: false,

    // Specific fields (updated by child components via formData prop)
    agenda: '', // Meeting
    title: '', // Event
    organizer: '', // Event
    name: '', // Other Activity
    requester: '', // Meeting, Event, Other Activity, (not Class in current schema)
    subject: '', // Class
    section: '', // Class
    faculty: '', // Class
    numberOfStudents: null, // Class

    // Additional
    additionalInstructions: '',
    driveLink: ''
});

// --- Computed properties ---
// Calculates the full start date/time object
const startDate = computed(() => {
    const datePart = new Date(appointmentForm.date);
    let h = appointmentForm.hour;
    // Convert AM/PM hour to 24-hour format
    if (appointmentForm.amPm === 'PM' && h < 12) h += 12;
    if (appointmentForm.amPm === 'AM' && h === 12) h = 0;

    const start = new Date(
        datePart.getFullYear(),
        datePart.getMonth(),
        datePart.getDate(),
        h,
        appointmentForm.minute
    );
    return start;
});

// Calculate the total duration in minutes
const totalDurationMinutes = computed(() => {
    return (appointmentForm.durationHour * 60) + appointmentForm.durationMinute;
});

// Calculates and formats the End Date/Time for display
const endDateDisplay = computed(() => {
    if (startDate.value && totalDurationMinutes.value) {
        return calculateEndTime(startDate.value, totalDurationMinutes.value);
    }
    return '';
});

// Helper to get initial state (for resetting)
const getInitialFormState = () => ({
    // Reset core fields (excluding date/time inherited from calendar click)
    type: 'Meeting',
    durationHour: 0,
    durationMinute: 30,
    isHoliday: false,
    recurring: false,
    numberParticipants: null,
    deptOffice: '',
    organization: '',
    description: '',
    tablesChairs: false,
    airConditioner: false,
    whiteboard: false,
    additionalInstructions: '',
    driveLink: '',
    agenda: '',
    title: '',
    organizer: '',
    name: '',
    requester: '',
    subject: '',
    section: '',
    faculty: '',
    numberOfStudents: null,
});

// Watch props for initial data (for new appointment) and reset other fields
watch(() => [props.selectedDate, props.initialHour, props.initialMinute], ([newDate, newHour, newMinute]) => {
    if (!props.editingEvent) {
        Object.assign(appointmentForm, getInitialFormState()); // Reset specific fields
        appointmentForm.date = newDate;
        appointmentForm.hour = newHour !== null ? newHour : 6;
        appointmentForm.minute = newMinute !== null ? newMinute : 0;
        appointmentForm.amPm = (newHour !== null && newHour >= 12) ? 'PM' : 'AM';
    }
}, { immediate: true });

// --- Modal handlers ---
const closeModal = () => emit('close');

const submitForm = () => {
    // 1. Prepare data for the parent component (schedulelayout.vue)
    const dataToSubmit = {
        ...appointmentForm,
        start: startDate.value, // Pass the computed Date object
        end: new Date(startDate.value.getTime() + totalDurationMinutes.value * 60000), // Calculate and pass end Date object

        // This is the crucial part: determine the main 'title' for the calendar view
        title: appointmentForm.title || appointmentForm.agenda || appointmentForm.name || appointmentForm.subject || 'Untitled Appointment',
        list: appointmentForm.type, // Use 'type' as the list category (e.g., 'Class', 'Meeting')
        allDay: totalDurationMinutes.value >= 1439 // True if duration is 23h 59m or more
    };

    // 2. Emit the final structured event data
    emit('success', dataToSubmit);
    closeModal();
};
</script>


<template>
<div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white z-10 p-1 -m-1">
            <h3 class="text-xl font-bold">{{ appointmentForm.room }}</h3>
            <button @click="closeModal" class="text-xl font-semibold">X</button>
        </div>

        <div class="mb-4">
            <label class="text-sm font-medium text-gray-700 block mb-1">Requesting for a</label>
            <select v-model="appointmentForm.type" class="border rounded-md p-2 w-full">
                <option>Class</option>
                <option>Meeting</option>
                <option>Event</option>
                <option>Other type of activity</option>
            </select>
        </div>

        <component
            :is="appointmentForm.type === 'Class' ? ClassForm
                : appointmentForm.type === 'Meeting' ? MeetingForm
                : appointmentForm.type === 'Event' ? EventForm
                : OtherActivityForm"
            :formData="appointmentForm"
            class="pb-4"
        />

        <div class="mt-6 border-t pt-4">
            <h4 class="text-lg font-semibold mb-3">General Information</h4>

            <label for="deptOffice" class="block text-sm font-medium text-gray-700">Dept/Office</label>
            <input type="text" id="deptOffice" v-model="appointmentForm.deptOffice"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">

            <label for="organization" class="block text-sm font-medium text-gray-700 mt-4">Organization</label>
            <input type="text" id="organization" v-model="appointmentForm.organization"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">

            <div class="mt-4 flex items-center">
                <input type="checkbox" id="isHoliday" v-model="appointmentForm.isHoliday" class="mr-2">
                <label for="isHoliday" class="text-sm font-medium text-gray-700">Is this date a recognized holiday?</label>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-2 items-end">
                <div class="col-span-4"><label class="text-sm font-medium text-gray-700">Start Date:</label></div>

                <div class="col-span-2">
                    <label for="startDate" class="block text-xs font-medium text-gray-500">Date*</label>
                    <input type="date" id="startDate" v-model="appointmentForm.date" required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                </div>
                <div>
                    <label for="startHour" class="block text-xs font-medium text-gray-500">Hour*</label>
                    <input type="number" id="startHour" v-model.number="appointmentForm.hour" required min="1" max="12"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                </div>
                <div>
                    <label for="startMinute" class="block text-xs font-medium text-gray-500">Minute*</label>
                    <input type="number" id="startMinute" v-model.number="appointmentForm.minute" required min="0" max="59" step="15"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                </div>
                <div>
                    <label for="amPm" class="block text-xs font-medium text-gray-500">AM/PM*</label>
                    <select id="amPm" v-model="appointmentForm.amPm" required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                      <option>AM</option>
                      <option>PM</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-2 items-end">
                <div class="col-span-4"><label class="text-sm font-medium text-gray-700">Duration:</label></div>

                <div>
                    <label for="durationHour" class="block text-xs font-medium text-gray-500">Hour*</label>
                    <input type="number" id="durationHour" v-model.number="appointmentForm.durationHour" required min="0"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                </div>
                <div>
                    <label for="durationMinute" class="block text-xs font-medium text-gray-500">Minute*</label>
                    <input type="number" id="durationMinute" v-model.number="appointmentForm.durationMinute" required min="0" max="59" step="15"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-500">End Date:</label>
                    <p class="mt-1 text-sm font-semibold">{{ endDateDisplay }}</p>
                </div>
            </div>

            <div class="mt-4 flex items-center">
                <input type="checkbox" id="recurring" v-model="appointmentForm.recurring" class="mr-2">
                <label for="recurring" class="text-sm font-medium text-gray-700">Recurring Meeting</label>
            </div>

            <label v-if="appointmentForm.type !== 'Class'" for="numParticipants" class="block text-sm font-medium text-gray-700 mt-4">Number of Participants*</label>
            <input v-if="appointmentForm.type !== 'Class'" type="number" id="numParticipants" v-model.number="appointmentForm.numberParticipants" required min="1"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500">

            <label v-if="appointmentForm.type === 'Other type of activity' || appointmentForm.type === 'Class'" for="description" class="block text-sm font-medium text-gray-700 mt-4">Description*</label>
            <textarea v-if="appointmentForm.type === 'Other type of activity' || appointmentForm.type === 'Class'" id="description" v-model="appointmentForm.description" rows="3" required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500"></textarea>


            <div class="mt-4">
                <h5 class="text-sm font-semibold mb-2">Equipment</h5>
                <div class="flex flex-col space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-700">tables and chairs</label>
                        <input type="checkbox" v-model="appointmentForm.tablesChairs">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-700">air conditioner</label>
                        <input type="checkbox" v-model="appointmentForm.airConditioner">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-700">whiteboard</label>
                        <input type="checkbox" v-model="appointmentForm.whiteboard">
                    </div>
                </div>
            </div>

            <label for="additionalInstructions" class="block text-sm font-medium text-gray-700 mt-4">Additional instructions</label>
            <textarea id="additionalInstructions" v-model="appointmentForm.additionalInstructions" rows="3"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500"></textarea>

            <label for="driveLink" class="block text-sm font-medium text-gray-700 mt-4">Attach File <i class="text-gray-400">(i)</i></label>
            <input type="text" id="driveLink" v-model="appointmentForm.driveLink" placeholder="Attach your Google Drive Link here"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">

            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <h6 class="text-xs font-semibold text-red-700">Room Reminders:</h6>
                <ul class="list-disc list-inside text-sm text-red-600">
                    <li>Food is not allowed inside.</li>
                </ul>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-md">Close</button>
            <button type="button" @click="submitForm" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Submit</button>
        </div>
    </div>
</div>
</template>
