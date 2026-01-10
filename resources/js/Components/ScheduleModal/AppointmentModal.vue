<script setup>
import { reactive, watch, defineProps, defineEmits, ref, computed, onMounted } from 'vue';
import ClassForm from './ClassForm.vue';
import MeetingForm from './MeetingForm.vue';
import EventForm from './EventForm.vue';
import OtherActivityForm from './OtherActivityForm.vue';

const props = defineProps({
    isVisible: Boolean,
    selectedDate: { type: String, default: () => new Date().toISOString().slice(0, 10) },
    initialHour: { type: [Number, null], default: 9 },
    initialMinute: { type: [Number, null], default: 0 },
    startAmPm: { type: String, default: 'AM' },
    endHour: { type: [Number, null], default: 10 },
    endMinute: { type: [Number, null], default: 0 },
    endAmPm: { type: String, default: 'AM' },
    editingEvent: { type: Object, default: null },
    initialRoom: { type: String, default: 'Select Room' },
    rooms: { type: Array, default: () => [] },
    faculty: { type: Array, default: () => [] },
    requesters: { type: Array, default: () => [] },
    terms: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'success']);

// --- Reactive form object ---
const appointmentForm = reactive({
    // Basic Info
    room: props.initialRoom || 'Select Room',
    type: 'Meeting',
    date: props.selectedDate,
    startHour: props.initialHour !== null ? props.initialHour : 9,
    startMinute: props.initialMinute !== null ? props.initialMinute : 0,
    startAmPm: props.startAmPm || 'AM',
    endHour: props.endHour !== null ? props.endHour : 10,
    endMinute: props.endMinute !== null ? props.endMinute : 0,
    endAmPm: props.endAmPm || 'AM',
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

    // Type-specific fields
    agenda: '', // Meeting
    title: '', // Event
    organizer: '', // Event
    name: '', // Other Activity
    requester: '', // Meeting, Event, Other Activity
    subject: '', // Class
    section: '', // Class
    faculty: '', // Class
    numberOfStudents: null, // Class
    courseCode: '', // Class

    // Additional
    additionalInstructions: '',
    driveLink: '',
    cficId: '',
    status: 'pending',
    term: '',
    recurrencePattern: null
});

// Time conflict warning
const timeConflictWarning = ref('');

// Autocomplete lists
const facultySuggestions = ref([]);
const requesterSuggestions = ref([]);

// Filter suggestions based on input
const filterFaculty = (input) => {
  if (!input) {
    facultySuggestions.value = [];
    return;
  }
  facultySuggestions.value = props.faculty.filter(f =>
    `${f.first_name} ${f.last_name}`.toLowerCase().includes(input.toLowerCase()) ||
    f.email.toLowerCase().includes(input.toLowerCase())
  ).slice(0, 5);
};

const filterRequesters = (input) => {
  if (!input) {
    requesterSuggestions.value = [];
    return;
  }
  requesterSuggestions.value = props.requesters.filter(r =>
    `${r.first_name} ${r.last_name}`.toLowerCase().includes(input.toLowerCase()) ||
    r.email.toLowerCase().includes(input.toLowerCase())
  ).slice(0, 5);
};

// --- Computed properties ---
const convertTo24Hour = (hour, minute, ampm) => {
    let h = hour;
    if (ampm === 'PM' && h < 12) h += 12;
    if (ampm === 'AM' && h === 12) h = 0;
    return { hour: h, minute };
};

const startDate = computed(() => {
    const datePart = new Date(appointmentForm.date);
    const start24 = convertTo24Hour(appointmentForm.startHour, appointmentForm.startMinute, appointmentForm.startAmPm);

    return new Date(
        datePart.getFullYear(),
        datePart.getMonth(),
        datePart.getDate(),
        start24.hour,
        start24.minute
    );
});

const endDate = computed(() => {
    const datePart = new Date(appointmentForm.date);
    const end24 = convertTo24Hour(appointmentForm.endHour, appointmentForm.endMinute, appointmentForm.endAmPm);

    let endDateTime = new Date(
        datePart.getFullYear(),
        datePart.getMonth(),
        datePart.getDate(),
        end24.hour,
        end24.minute
    );

    // If end time is earlier than start time, add one day
    if (endDateTime <= startDate.value) {
        endDateTime.setDate(endDateTime.getDate() + 1);
    }

    return endDateTime;
});

const totalDurationMinutes = computed(() => {
    const diffMs = endDate.value.getTime() - startDate.value.getTime();
    return Math.max(0, Math.floor(diffMs / 60000));
});

const formatTime = (hour, minute, ampm) => {
    return `${hour}:${minute.toString().padStart(2, '0')} ${ampm}`;
};

const formatDuration = computed(() => {
    const hours = Math.floor(totalDurationMinutes.value / 60);
    const minutes = totalDurationMinutes.value % 60;

    if (hours === 0) return `${minutes} minutes`;
    if (minutes === 0) return `${hours} hours`;
    return `${hours} hours ${minutes} minutes`;
});

const isEndTimeValid = computed(() => {
    return endDate.value > startDate.value;
});

// Get title based on appointment type
const getAppointmentTitle = computed(() => {
    switch (appointmentForm.type) {
        case 'Class':
            return appointmentForm.subject || 'Class';
        case 'Meeting':
            return appointmentForm.agenda || 'Meeting';
        case 'Event':
            return appointmentForm.title || 'Event';
        case 'Other type of activity':
            return appointmentForm.name || 'Activity';
        default:
            return 'Untitled Appointment';
    }
});

const getInitialFormState = () => ({
    type: 'Meeting',
    startHour: 9,
    startMinute: 0,
    startAmPm: 'AM',
    endHour: 10,
    endMinute: 0,
    endAmPm: 'AM',
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
    courseCode: '',
    cficId: '',
    status: 'pending',
    term: '',
    recurrencePattern: null
});

// Watch props for initial data
watch(() => [props.selectedDate, props.initialHour, props.initialMinute, props.initialRoom], ([newDate, newHour, newMinute, newRoom]) => {
    if (!props.editingEvent) {
        Object.assign(appointmentForm, getInitialFormState());
        appointmentForm.date = newDate;
        appointmentForm.room = newRoom || 'Select Room';

        if (newHour !== null && newMinute !== null) {
            appointmentForm.startHour = newHour;
            appointmentForm.startMinute = newMinute;
            appointmentForm.startAmPm = props.startAmPm || 'AM';

            appointmentForm.endHour = props.endHour !== null ? props.endHour : newHour + 1;
            appointmentForm.endMinute = props.endMinute !== null ? props.endMinute : newMinute;
            appointmentForm.endAmPm = props.endAmPm || 'AM';
        }
    }
}, { immediate: true });

// Watch for editing event
watch(() => props.editingEvent, (newEvent) => {
    if (newEvent) {
        // Populate form with event data for editing
        const extProps = newEvent.extendedProps || {};

        // Reset form first
        Object.assign(appointmentForm, getInitialFormState());

        // Populate form data
        appointmentForm.room = extProps.room || 'Select Room';
        appointmentForm.type = newEvent.list || 'Meeting';
        appointmentForm.date = new Date(newEvent.start).toISOString().split('T')[0];

        if (!newEvent.allDay) {
            const startDate = new Date(newEvent.start);
            const startHour24 = startDate.getHours();
            let startHour12 = startHour24 > 12 ? startHour24 - 12 : startHour24;
            if (startHour12 === 0) startHour12 = 12;

            appointmentForm.startHour = startHour12;
            appointmentForm.startMinute = startDate.getMinutes();
            appointmentForm.startAmPm = startHour24 >= 12 ? 'PM' : 'AM';

            if (newEvent.end) {
                const endDate = new Date(newEvent.end);
                const endHour24 = endDate.getHours();
                let endHour12 = endHour24 > 12 ? endHour24 - 12 : endHour24;
                if (endHour12 === 0) endHour12 = 12;

                appointmentForm.endHour = endHour12;
                appointmentForm.endMinute = endDate.getMinutes();
                appointmentForm.endAmPm = endHour24 >= 12 ? 'PM' : 'AM';
            }
        }

        // Populate all fields from extendedProps
        appointmentForm.agenda = extProps.agenda || extProps.subject || '';
        appointmentForm.title = newEvent.title || '';
        appointmentForm.subject = extProps.subject || '';
        appointmentForm.requester = extProps.requester || '';
        appointmentForm.deptOffice = extProps.college || '';
        appointmentForm.organization = extProps.building || '';
        appointmentForm.description = extProps.description || '';
        appointmentForm.faculty = extProps.faculty || '';
        appointmentForm.section = extProps.section || '';
        appointmentForm.numberOfStudents = extProps.numberOfStudents || null;
        appointmentForm.organizer = extProps.organizer || '';
        appointmentForm.name = extProps.name || '';
        appointmentForm.courseCode = extProps.courseCode || '';
        appointmentForm.cficId = extProps.cficId || '';
        appointmentForm.status = extProps.status || 'pending';
        appointmentForm.term = extProps.term || '';
        appointmentForm.isRecurring = extProps.isRecurring || false;
        appointmentForm.recurrencePattern = extProps.recurrencePattern || null;

        // Parse equipment needed
        if (extProps.equipmentNeeded) {
            if (Array.isArray(extProps.equipmentNeeded)) {
                appointmentForm.tablesChairs = extProps.equipmentNeeded.includes('tablesChairs') || extProps.equipmentNeeded.includes('chairs');
                appointmentForm.airConditioner = extProps.equipmentNeeded.includes('airConditioner');
                appointmentForm.whiteboard = extProps.equipmentNeeded.includes('whiteboard');
            } else if (typeof extProps.equipmentNeeded === 'object') {
                appointmentForm.tablesChairs = extProps.equipmentNeeded.tablesChairs || false;
                appointmentForm.airConditioner = extProps.equipmentNeeded.airConditioner || false;
                appointmentForm.whiteboard = extProps.equipmentNeeded.whiteboard || false;
            }
        }

        // Parse additional requirements
        if (extProps.additionalRequirements) {
            appointmentForm.additionalInstructions = extProps.additionalRequirements.instructions || '';
        }
    }
}, { immediate: true });

// --- Modal handlers ---
const closeModal = () => emit('close');

const submitForm = () => {
    if (!isEndTimeValid.value) {
        alert('End time must be after start time. Please adjust the times.');
        return;
    }

    // Validate required fields
    if (!appointmentForm.deptOffice || !appointmentForm.organization) {
        alert('Department/Office and Organization are required fields.');
        return;
    }

    // Prepare data for the parent component
    const dataToSubmit = {
        // All form fields
        ...appointmentForm,

        // Computed date objects
        start: startDate.value,
        end: endDate.value,

        // Duration information
        durationHour: Math.floor(totalDurationMinutes.value / 60),
        durationMinute: totalDurationMinutes.value % 60,

        // Core event properties
        title: getAppointmentTitle.value,
        list: appointmentForm.type,
        allDay: totalDurationMinutes.value >= 1439,

        // Additional computed properties
        formattedDate: new Date(appointmentForm.date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }),
        formattedTime: formatTime(appointmentForm.startHour, appointmentForm.startMinute, appointmentForm.startAmPm) +
                      ' - ' +
                      formatTime(appointmentForm.endHour, appointmentForm.endMinute, appointmentForm.endAmPm),

        // Equipment needed as object
        equipmentNeeded: {
            tablesChairs: appointmentForm.tablesChairs,
            airConditioner: appointmentForm.airConditioner,
            whiteboard: appointmentForm.whiteboard
        }
    };

    console.log('Submitting appointment data:', dataToSubmit);

    // Emit the final structured event data
    emit('success', dataToSubmit);
    closeModal();
};

// Validate time input
const validateTimeInput = () => {
    timeConflictWarning.value = '';

    if (!isEndTimeValid.value) {
        timeConflictWarning.value = 'End time must be after start time.';
        return false;
    }

    return true;
};

// Handle time change
const handleTimeChange = () => {
    validateTimeInput();
};

// Handle faculty input for autocomplete
const handleFacultyInput = (e) => {
    filterFaculty(e.target.value);
};

// Handle requester input for autocomplete
const handleRequesterInput = (e) => {
    filterRequesters(e.target.value);
};

// Select faculty from suggestions
const selectFaculty = (faculty) => {
    appointmentForm.faculty = `${faculty.first_name} ${faculty.last_name}`;
    facultySuggestions.value = [];
};

// Select requester from suggestions
const selectRequester = (requester) => {
    appointmentForm.requester = `${requester.first_name} ${requester.last_name}`;
    requesterSuggestions.value = [];
};

// Generate CFIC ID if not provided
const generateCficId = () => {
    if (!appointmentForm.cficId) {
        appointmentForm.cficId = 'CFIC-' + Math.random().toString(36).substr(2, 9).toUpperCase();
    }
};

// Watch for CFIC ID generation
watch(() => appointmentForm.type, () => {
    if (!appointmentForm.cficId) {
        generateCficId();
    }
});

onMounted(() => {
    if (!appointmentForm.cficId) {
        generateCficId();
    }
});
</script>

<template>
<div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white z-10 p-1 -m-1">
            <h3 class="text-xl font-bold">{{ appointmentForm.room }} {{ props.editingEvent ? '(Editing)' : '' }}</h3>
            <button @click="closeModal" class="text-xl font-semibold hover:text-red-600 transition">✕</button>
        </div>

        <!-- CFIC ID Display -->
        <div v-if="appointmentForm.cficId" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
            <div class="text-sm font-semibold text-yellow-800">Reference ID: {{ appointmentForm.cficId }}</div>
        </div>

        <div class="mb-4">
            <label class="text-sm font-medium text-gray-700 block mb-1">Requesting for a</label>
            <select v-model="appointmentForm.type" class="border rounded-md p-2 w-full focus:ring-2 focus:ring-[#7A0C23]">
                <option>Class</option>
                <option>Meeting</option>
                <option>Event</option>
                <option>Other type of activity</option>
            </select>
        </div>

        <!-- Dynamic form component based on appointment type -->
        <component
            :is="appointmentForm.type === 'Class' ? ClassForm
                : appointmentForm.type === 'Meeting' ? MeetingForm
                : appointmentForm.type === 'Event' ? EventForm
                : OtherActivityForm"
            :formData="appointmentForm"
            :facultySuggestions="facultySuggestions"
            :requesterSuggestions="requesterSuggestions"
            @faculty-input="handleFacultyInput"
            @requester-input="handleRequesterInput"
            @select-faculty="selectFaculty"
            @select-requester="selectRequester"
            class="pb-4"
        />

        <div class="mt-6 border-t pt-4">
            <h4 class="text-lg font-semibold mb-3">General Information</h4>

            <!-- Room Selection -->
            <label for="room" class="block text-sm font-medium text-gray-700">Room *</label>
            <select id="room" v-model="appointmentForm.room" required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                <option value="">Select a room</option>
                <option v-for="room in props.rooms" :key="room.id" :value="room.room_name">
                    {{ room.room_name }} ({{ room.building?.building_name || 'N/A' }})
                </option>
            </select>

            <!-- Department/Office -->
            <label for="deptOffice" class="block text-sm font-medium text-gray-700 mt-4">Dept/Office *</label>
            <input type="text" id="deptOffice" v-model="appointmentForm.deptOffice" required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

            <!-- Organization -->
            <label for="organization" class="block text-sm font-medium text-gray-700 mt-4">Organization *</label>
            <input type="text" id="organization" v-model="appointmentForm.organization" required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

            <!-- Term Selection -->
            <label for="term" class="block text-sm font-medium text-gray-700 mt-4">Term</label>
            <select id="term" v-model="appointmentForm.term"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                <option value="">Select a term</option>
                <option v-for="term in props.terms" :key="term.id" :value="term.term_name">
                    {{ term.term_name }}
                </option>
            </select>

            <!-- Status Selection -->
            <label for="status" class="block text-sm font-medium text-gray-700 mt-4">Status</label>
            <select id="status" v-model="appointmentForm.status"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </select>

            <!-- Holiday Checkbox -->
            <div class="mt-4 flex items-center">
                <input type="checkbox" id="isHoliday" v-model="appointmentForm.isHoliday" class="mr-2">
                <label for="isHoliday" class="text-sm font-medium text-gray-700">Is this date a recognized holiday?</label>
            </div>

            <!-- Recurring Meeting -->
            <div class="mt-4 flex items-center">
                <input type="checkbox" id="recurring" v-model="appointmentForm.recurring" class="mr-2">
                <label for="recurring" class="text-sm font-medium text-gray-700">Recurring Meeting</label>
            </div>

            <!-- Start Date and Time Section -->
            <div class="mt-4">
                <label class="text-sm font-medium text-gray-700 block mb-2">Start Date and Time *</label>
                <div class="grid grid-cols-3 gap-2 items-end">
                    <div class="col-span-1">
                        <label for="startDate" class="block text-xs font-medium text-gray-500">Date*</label>
                        <input type="date" id="startDate" v-model="appointmentForm.date" required
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @change="handleTimeChange">
                    </div>
                    <div>
                        <label for="startHour" class="block text-xs font-medium text-gray-500">Hour*</label>
                        <input type="number" id="startHour" v-model.number="appointmentForm.startHour"
                          required min="1" max="12" step="1"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @input="handleTimeChange">
                    </div>
                    <div>
                        <label for="startMinute" class="block text-xs font-medium text-gray-500">Minute*</label>
                        <input type="number" id="startMinute" v-model.number="appointmentForm.startMinute"
                          required min="0" max="59" step="15"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @input="handleTimeChange">
                    </div>
                    <div>
                        <label for="startAmPm" class="block text-xs font-medium text-gray-500">AM/PM*</label>
                        <select id="startAmPm" v-model="appointmentForm.startAmPm" required
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @change="handleTimeChange">
                          <option>AM</option>
                          <option>PM</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- End Time Section -->
            <div class="mt-4">
                <label class="text-sm font-medium text-gray-700 block mb-2">End Time *</label>
                <div class="grid grid-cols-3 gap-2 items-end">
                    <div>
                        <label for="endHour" class="block text-xs font-medium text-gray-500">Hour*</label>
                        <input type="number" id="endHour" v-model.number="appointmentForm.endHour"
                          required min="1" max="12" step="1"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @input="handleTimeChange"
                          :class="{ 'border-red-500': !isEndTimeValid }">
                    </div>
                    <div>
                        <label for="endMinute" class="block text-xs font-medium text-gray-500">Minute*</label>
                        <input type="number" id="endMinute" v-model.number="appointmentForm.endMinute"
                          required min="0" max="59" step="15"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @input="handleTimeChange"
                          :class="{ 'border-red-500': !isEndTimeValid }">
                    </div>
                    <div>
                        <label for="endAmPm" class="block text-xs font-medium text-gray-500">AM/PM*</label>
                        <select id="endAmPm" v-model="appointmentForm.endAmPm" required
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-2 focus:ring-[#7A0C23]"
                          @change="handleTimeChange"
                          :class="{ 'border-red-500': !isEndTimeValid }">
                          <option>AM</option>
                          <option>PM</option>
                        </select>
                    </div>
                </div>

                <!-- Duration Display -->
                <div class="mt-3 p-2 bg-gray-50 rounded border">
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-gray-700">Duration:</span>
                        <span class="font-semibold" :class="{ 'text-red-500': !isEndTimeValid, 'text-green-600': isEndTimeValid }">
                            {{ formatDuration }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>Start: {{ formatTime(appointmentForm.startHour, appointmentForm.startMinute, appointmentForm.startAmPm) }}</span>
                        <span>End: {{ formatTime(appointmentForm.endHour, appointmentForm.endMinute, appointmentForm.endAmPm) }}</span>
                    </div>
                    <div v-if="!isEndTimeValid" class="mt-1 text-xs text-red-500 font-medium">
                        ⚠️ End time must be after start time
                    </div>
                    <div v-if="timeConflictWarning" class="mt-1 text-xs text-red-500 font-medium">
                        ⚠️ {{ timeConflictWarning }}
                    </div>
                </div>
            </div>

            <!-- Number of Participants -->
            <label v-if="appointmentForm.type !== 'Class'" for="numParticipants" class="block text-sm font-medium text-gray-700 mt-4">Number of Participants *</label>
            <input v-if="appointmentForm.type !== 'Class'" type="number" id="numParticipants"
              v-model.number="appointmentForm.numberParticipants" required min="1"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

            <!-- Course Code (for Class) -->
            <label v-if="appointmentForm.type === 'Class'" for="courseCode" class="block text-sm font-medium text-gray-700 mt-4">Course Code</label>
            <input v-if="appointmentForm.type === 'Class'" type="text" id="courseCode" v-model="appointmentForm.courseCode"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

            <!-- Description -->
            <label for="description" class="block text-sm font-medium text-gray-700 mt-4">Description</label>
            <textarea id="description" v-model="appointmentForm.description" rows="3"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"></textarea>

            <!-- Equipment Section -->
            <div class="mt-4">
                <h5 class="text-sm font-semibold mb-2">Equipment</h5>
                <div class="flex flex-col space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-700">Tables and chairs</label>
                        <input type="checkbox" v-model="appointmentForm.tablesChairs">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-700">Air conditioner</label>
                        <input type="checkbox" v-model="appointmentForm.airConditioner">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-700">Whiteboard</label>
                        <input type="checkbox" v-model="appointmentForm.whiteboard">
                    </div>
                </div>
            </div>

            <!-- Additional Instructions -->
            <label for="additionalInstructions" class="block text-sm font-medium text-gray-700 mt-4">Additional instructions</label>
            <textarea id="additionalInstructions" v-model="appointmentForm.additionalInstructions" rows="3"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"></textarea>

            <!-- File Attachment -->
            <label for="driveLink" class="block text-sm font-medium text-gray-700 mt-4">Attach File <span class="text-gray-400 text-xs">(Google Drive Link)</span></label>
            <input type="text" id="driveLink" v-model="appointmentForm.driveLink"
              placeholder="https://drive.google.com/..."
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

            <!-- Room Reminders -->
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <h6 class="text-xs font-semibold text-red-700">Room Reminders:</h6>
                <ul class="list-disc list-inside text-sm text-red-600">
                    <li>Food is not allowed inside.</li>
                    <li>Please ensure your time slot does not conflict with existing appointments.</li>
                    <li>Return the room to its original state after use.</li>
                </ul>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                Cancel
            </button>
            <button
                type="button"
                @click="submitForm"
                class="px-4 py-2 bg-[#7A0C23] text-white rounded-md hover:bg-[#8A1C33] disabled:opacity-50 disabled:cursor-not-allowed transition"
                :disabled="!isEndTimeValid || timeConflictWarning || !appointmentForm.deptOffice || !appointmentForm.organization || !appointmentForm.room || appointmentForm.room === 'Select Room'"
            >
                {{ props.editingEvent ? 'Update Appointment' : 'Submit Appointment' }}
            </button>
        </div>
    </div>
</div>
</template>
