<script setup>
import { reactive, watch, defineProps, defineEmits, computed } from 'vue';

const props = defineProps({
    // Controls the visibility of the modal
    isVisible: Boolean,
    // The date selected from a parent calendar (e.g., '2025-11-28')
    selectedDate: String,
    // For initial time setting (e.g., 6 AM = 6)
    initialHour: { type: Number, default: 6 },
    // For initial minute setting (e.g., 0)
    initialMinute: { type: Number, default: 0 },
    // Placeholder prop for future editing capabilities
    editingEvent: {
        type: Object,
        default: null
    },
});

const emit = defineEmits(['close', 'success']);

// --- Helper Functions ---

/**
 * Converts date, 12hr hour, AM/PM, and minute components into a Date object.
 * @param {string} dateString - The date in YYYY-MM-DD format.
 * @param {number} hour - Hour component (1-12).
 * @param {string} ampm - AM or PM.
 * @param {number} minute - Minute component (0-59).
 * @returns {Date} A Date object representing the start date and time.
 */
const createDateFromForm = (dateString, hour, ampm, minute) => {
    // dateString is YYYY-MM-DD from the internal date object, but we need to convert to Date
    const [year, month, day] = dateString.split('-').map(Number);

    let hours24 = hour;
    if (ampm === 'PM' && hour !== 12) {
        hours24 += 12;
    } else if (ampm === 'AM' && hour === 12) {
        hours24 = 0;
    }

    // Month is 0-indexed in Date constructor, so month - 1
    // The provided date '2025-11-28' is used as the default if props.selectedDate is null/undefined
    return new Date(year, month - 1, day, hours24, minute);
};

/**
 * Calculates the end date/time based on start date/time and duration.
 * @param {Date} startDate - The start Date object.
 * @param {number} durationHour - Duration in hours.
 * @param {number} durationMinute - Duration in minutes.
 * @returns {{date: Date, formattedHour: number, formattedMinute: number, formattedAmpm: string}}
 */
const calculateEndDate = (startDate, durationHour, durationMinute) => {
    // Get total duration in milliseconds
    const durationMs = (durationHour * 60 + durationMinute) * 60000;
    const endDate = new Date(startDate.getTime() + durationMs);

    // Convert 24hr end time to 12hr AM/PM format
    const endHour24 = endDate.getHours();
    const endMinute = endDate.getMinutes();

    let endHour12 = endHour24 % 12 || 12; // 0 (midnight) or 12 (noon) -> 12
    const endAmpm = endHour24 >= 12 ? 'PM' : 'AM';

    return {
        date: endDate,
        formattedHour: endHour12,
        formattedMinute: endMinute,
        formattedAmpm: endAmpm
    };
};

// --- Form State Initialization ---

const getDefaultForm = () => {
    // Set a default date string for parsing, prioritizing prop
    const initialDateString = props.selectedDate || '2025-11-28';

    // Convert 24hr initialHour (default 6) to 12hr format
    const initialHour24 = props.initialHour;
    const initialHour12 = initialHour24 % 12 || 12;
    const initialAmpm = initialHour24 >= 12 ? 'PM' : 'AM';

    // Create a Date object from the initial components
    const initialDate = createDateFromForm(initialDateString, initialHour12, initialAmpm, props.initialMinute);

    return {
        room: 'UG 114',
        type: 'Meeting', // Default selection as per the images

        // Dynamic Fields (based on type)
        // Meeting
        agenda: '',
        // Event
        title: '',
        organizer: '',
        // Other type of activity
        name: '',
        // Class
        subject: '', // New field for Class
        section: '', // New field for Class
        faculty: '', // New field for Class
        numberOfStudents: null, // New field for Class (Number of Students)

        // Fields shared across types
        description: '', // Used by Class, Event, Other

        // Common Fields
        requester: '',
        deptOffice: '',
        organization: '',
        isHoliday: false,

        // Start Date/Time
        startDate: initialDate, // Internal Date object for reactivity
        // Formatted date string (DD/MM/YYYY) for display in the date input
        formattedStartDate: initialDate.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/'),
        formattedStartTime: {
            hour: initialHour12, // 1-12
            minute: props.initialMinute, // 0-59
            ampm: initialAmpm // AM/PM
        },

        // Duration
        durationHour: 0,
        durationMinute: 30, // Default duration from image

        // Other
        isRecurring: false,
        numberParticipants: null, // Used for Meeting, Event, Other
        equipment: '', // Simplified as one string field for all optional equipment
        quantity: 1,
        additionalInstructions: '', // Field from one of the images
        attachFile: '', // Field from one of the images
    };
};

const appointmentForm = reactive(getDefaultForm());

// --- Computed Properties for Date/Time Management ---

// Recalculates the actual start Date object whenever a start time/date component changes
const calculatedStartDate = computed(() => {
    // Get the YYYY-MM-DD part from the internal date object
    const dateString = appointmentForm.startDate.toISOString().slice(0, 10);
    return createDateFromForm(
        dateString,
        appointmentForm.formattedStartTime.hour,
        appointmentForm.formattedStartTime.ampm,
        appointmentForm.formattedStartTime.minute
    );
});

// Recalculates the end Date/Time details whenever start or duration changes
const calculatedEndDateDetails = computed(() => {
    return calculateEndDate(
        calculatedStartDate.value,
        appointmentForm.durationHour,
        appointmentForm.durationMinute
    );
});

// Formats the end date/time for display (e.g., November 28, 2025 6:30PM)
const formattedEndDateDisplay = computed(() => {
    const end = calculatedEndDateDetails.value;
    // Use 'en-US' locale for 'Month Day, Year' format as seen in the console output
    const datePart = end.date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    const timePart = `${end.formattedHour}:${String(end.formattedMinute).padStart(2, '0')}${end.formattedAmpm}`;
    return `${datePart} ${timePart}`;
});

// Formats the start date/time for display (e.g., November 28, 2025 6:00AM)
const formattedStartTimeDisplay = computed(() => {
    const start = appointmentForm.formattedStartTime;
    // Get the date part from the internal Date object
    const datePart = appointmentForm.startDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    const timePart = `${start.hour}:${String(start.minute).padStart(2, '0')}${start.ampm}`;
    return `${datePart} ${timePart}`;
});

// --- Watcher to handle initial date prop ---

watch(() => props.selectedDate, (newDate) => {
    if (newDate) {
        // Parse YYYY-MM-DD
        const [year, month, day] = newDate.split('-').map(Number);
        const currentHour = calculatedStartDate.value.getHours();
        const currentMinute = calculatedStartDate.value.getMinutes();

        // Create a new Date object with the new date and current time
        // Note: month - 1 because Date constructor is 0-indexed
        const newStartDate = new Date(year, month - 1, day, currentHour, currentMinute);

        appointmentForm.startDate = newStartDate;
        // Update the display format (DD/MM/YYYY)
        appointmentForm.formattedStartDate = newStartDate.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/');
    }
}, { immediate: true });


// --- Handlers ---

const closeModal = () => {
    emit('close');
};

const submitForm = () => {
    // 1. Collect base data
    const submissionData = {
        room: appointmentForm.room,
        type: appointmentForm.type,
        // Common fields
        deptOffice: appointmentForm.deptOffice,
        organization: appointmentForm.organization,
        isHoliday: appointmentForm.isHoliday,
        isRecurring: appointmentForm.isRecurring,

        // Equipment (handling the simplified field)
        equipment: appointmentForm.equipment,
        quantity: appointmentForm.quantity,
        additionalInstructions: appointmentForm.additionalInstructions,
        attachFile: appointmentForm.attachFile,

        // Date/Time (ISO 8601 for a backend)
        startDateTime: calculatedStartDate.value.toISOString(),
        endDateTime: calculatedEndDateDetails.value.date.toISOString(),
        durationHour: appointmentForm.durationHour,
        durationMinute: appointmentForm.durationMinute,
    };

    // 2. Add dynamic fields based on type
    if (appointmentForm.type === 'Meeting') {
        submissionData.requester = appointmentForm.requester; // Meeting uses Requester
        submissionData.agenda = appointmentForm.agenda;
        submissionData.numberParticipants = appointmentForm.numberParticipants;
    } else if (appointmentForm.type === 'Event') {
        submissionData.requester = appointmentForm.requester; // Event uses Requester
        submissionData.title = appointmentForm.title;
        submissionData.organizer = appointmentForm.organizer;
        submissionData.description = appointmentForm.description;
        submissionData.numberParticipants = appointmentForm.numberParticipants;
    } else if (appointmentForm.type === 'Class') {
        submissionData.faculty = appointmentForm.faculty; // Class uses Faculty instead of Requester/Organizer
        submissionData.subject = appointmentForm.subject;
        submissionData.section = appointmentForm.section;
        submissionData.numberOfStudents = appointmentForm.numberOfStudents;
    } else if (appointmentForm.type === 'Other type of activity') {
        submissionData.requester = appointmentForm.requester; // Other uses Requester
        submissionData.name = appointmentForm.name;
        submissionData.description = appointmentForm.description;
        submissionData.numberParticipants = appointmentForm.numberParticipants;
    }

    // Emit the success event with the data for the parent component
    emit('success', submissionData);

    // Close the modal
    closeModal();
};
</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-0 max-h-screen overflow-y-auto">

            <div class="sticky top-0 bg-white flex justify-between items-center p-4 border-b z-10">
                <h3 class="text-xl font-bold text-gray-800">
                    {{ appointmentForm.room }}
                </h3>
                <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form @submit.prevent="submitForm" class="p-6 space-y-4">

                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Requesting for a</label>
                    <select v-model="appointmentForm.type" class="border rounded-md px-3 py-1 text-sm bg-white border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Class</option>
                        <option>Meeting</option>
                        <option>Event</option>
                        <option>Other type of activity</option>
                    </select>
                </div>

                <div v-if="appointmentForm.type === 'Class'">
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject*</label>
                    <input type="text" id="subject" v-model="appointmentForm.subject" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <label for="section" class="block text-sm font-medium text-gray-700 mt-4">Section*</label>
                    <input type="text" id="section" v-model="appointmentForm.section" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">

                    <label for="faculty" class="block text-sm font-medium text-gray-700 mt-4">Faculty*</label>
                    <input type="text" id="faculty" v-model="appointmentForm.faculty" required placeholder="Search"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div v-else-if="appointmentForm.type === 'Meeting'">
                    <label for="agenda" class="block text-sm font-medium text-gray-700">Agenda*</label>
                    <input type="text" id="agenda" v-model="appointmentForm.agenda" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div v-else-if="appointmentForm.type === 'Event'">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title*</label>
                    <input type="text" id="title" v-model="appointmentForm.title" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <label for="organizer" class="block text-sm font-medium text-gray-700 mt-4">Organizer*</label>
                    <input type="text" id="organizer" v-model="appointmentForm.organizer" required placeholder="Search"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div v-else-if="appointmentForm.type === 'Other type of activity'">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name*</label>
                    <input type="text" id="name" v-model="appointmentForm.name" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div v-if="appointmentForm.type !== 'Class'">
                    <label for="requester" class="block text-sm font-medium text-gray-700">Requester*</label>
                    <input type="text" id="requester" v-model="appointmentForm.requester" required placeholder="Search"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="deptOffice" class="block text-sm font-medium text-gray-700">Dept/Office</label>
                    <input type="text" id="deptOffice" v-model="appointmentForm.deptOffice"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="organization" class="block text-sm font-medium text-gray-700">Organization</label>
                    <input type="text" id="organization" v-model="appointmentForm.organization"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center">
                    <input id="isHoliday" type="checkbox" v-model="appointmentForm.isHoliday"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="isHoliday" class="ml-2 block text-sm text-gray-900">Is this date a recognized holiday?</label>
                </div>

                <hr class="border-t border-gray-200" />

                <div class="space-y-2">
                    <div class="font-medium text-gray-700">Start Date: <span class="text-indigo-600">{{ formattedStartTimeDisplay }}</span></div>

                    <div class="flex flex-wrap items-end space-x-2">
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500">Date*</label>
                            <input type="text" :value="appointmentForm.formattedStartDate"
                                class="border rounded-md p-2 w-28 text-sm bg-gray-100" disabled>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500">Hour*</label>
                            <input type="number" v-model.number="appointmentForm.formattedStartTime.hour" min="1" max="12" required
                                class="border rounded-md p-2 w-16 text-sm text-center focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500">Minute*</label>
                            <select v-model.number="appointmentForm.formattedStartTime.minute" required
                                class="border rounded-md p-2 w-16 text-sm bg-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option :value="0">00</option>
                                <option :value="15">15</option>
                                <option :value="30">30</option>
                                <option :value="45">45</option>
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500">AM/PM*</label>
                            <select v-model="appointmentForm.formattedStartTime.ampm" required
                                class="border rounded-md p-2 w-16 text-sm bg-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option>AM</option>
                                <option>PM</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="font-medium text-gray-700">Duration</label>
                    <div class="flex items-end space-x-2">
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500">Hour*</label>
                            <input type="number" v-model.number="appointmentForm.durationHour" min="0" required
                                class="border rounded-md p-2 w-16 text-sm text-center focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500">Minute*</label>
                            <select v-model.number="appointmentForm.durationMinute" required
                                class="border rounded-md p-2 w-16 text-sm bg-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option :value="0">0</option>
                                <option :value="15">15</option>
                                <option :value="30">30</option>
                                <option :value="45">45</option>
                            </select>
                        </div>
                        <span class="text-sm self-end">min</span>
                    </div>
                </div>

                <div class="text-sm font-medium text-gray-700">
                    End Date: <span class="text-indigo-600">{{ formattedEndDateDisplay }}</span>
                </div>

                <div class="flex items-center">
                    <input id="isRecurring" type="checkbox" v-model="appointmentForm.isRecurring"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="isRecurring" class="ml-2 block text-sm text-gray-900">Recurring Meeting</label>
                </div>

                <div v-if="appointmentForm.type === 'Class'">
                    <label for="students" class="block text-sm font-medium text-gray-700">Number of Students*</label>
                    <input type="number" id="students" v-model.number="appointmentForm.numberOfStudents" required min="1"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div v-else>
                    <label for="participants" class="block text-sm font-medium text-gray-700">Number of Participants*</label>
                    <input type="number" id="participants" v-model.number="appointmentForm.numberParticipants" required min="1"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div v-if="appointmentForm.type === 'Event' || appointmentForm.type === 'Other type of activity'">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description*</label>
                    <textarea id="description" v-model="appointmentForm.description" rows="3" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-gray-700">Equipment</h4>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <input id="tables-chairs" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="tables-chairs" class="ml-2 text-sm text-gray-900">tables and chairs (Qty: 1)</label>
                        </div>
                        <div class="flex items-center">
                            <input id="air-conditioner" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="air-conditioner" class="ml-2 text-sm text-gray-900">air conditioner (Qty: 1)</label>
                        </div>
                        <div class="flex items-center">
                            <input id="whiteboard" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            <label for="whiteboard" class="ml-2 text-sm text-gray-900">whiteboard (Qty: 1)</label>
                        </div>
                    </div>

                    <div class="flex space-x-4 items-end">
                        <div class="flex-grow">
                            <label for="equipment" class="block text-sm font-medium text-gray-700">Other Equipment (e.g., Projector)</label>
                            <input type="text" id="equipment" v-model="appointmentForm.equipment"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Projector">
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="quantity" v-model.number="appointmentForm.quantity" min="1"
                                class="mt-1 block w-20 border border-gray-300 rounded-md shadow-sm p-2 text-center focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700">Additional instructions</label>
                    <textarea id="instructions" v-model="appointmentForm.additionalInstructions" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label for="attach-file" class="block text-sm font-medium text-gray-700">Attach File <span class="font-normal text-xs italic">(i)</span></label>
                    <input type="text" id="attach-file" v-model="appointmentForm.attachFile" placeholder="Attach your Google Drive Link here"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="bg-red-50 p-3 rounded-md border border-red-200">
                    <h4 class="text-sm font-bold text-red-700">Room Reminders:</h4>
                    <ul class="list-disc ml-5 text-sm text-red-600">
                        <li>Food is not allowed inside.</li>
                    </ul>
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="closeModal"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Close
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Scoped styles can be used for custom overrides, but this example relies purely on Tailwind CSS classes */
</style>
