<script setup>
import { reactive, watch, defineProps, defineEmits } from 'vue';

const props = defineProps({
    isVisible: Boolean,
    selectedDate: String, // YYYY-MM-DD
    selectedHour: Number, // Optional, for initial time setting
    selectedMinute: Number, // Optional, for initial time setting
    // New prop for editing
    editingEvent: { 
        type: Object, 
        default: null 
    },
});

const emit = defineEmits(['close', 'success']);

// Helper to convert 24hr time string (e.g., '09:00') to AM/PM format (e.g., '9:00 AM')
const formatTime = (time24h) => {
    if (!time24h) return '';
    const [hours, minutes] = time24h.split(':');
    const h = parseInt(hours);
    const m = minutes;
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour12 = h % 12 || 12; // Converts '00' or '12' to 12
    return `${hour12}:${m} ${ampm}`;
};

// Helper to get 24hr string from Date object
const dateTo24hString = (date) => {
    if (!date) return '';
    const h = String(date.getHours()).padStart(2, '0');
    const m = String(date.getMinutes()).padStart(2, '0');
    return `${h}:${m}`;
}

const getDefaultForm = () => ({
    title: '',
    list: 'New Appointment',
    appointmentDay: props.selectedDate,
    allDay: false,
    startTime: props.selectedHour !== null 
        ? `${String(props.selectedHour).padStart(2, '0')}:${String(props.selectedMinute || 0).padStart(2, '0')}`
        : '09:00',
    endTime: props.selectedHour !== null 
        ? `${String(props.selectedHour + 1).padStart(2, '0')}:${String(props.selectedMinute || 0).padStart(2, '0')}`
        : '10:00',
});

const form = reactive(getDefaultForm());

// --- Watchers for setup and editing ---

// 1. Watch for selectedDate/Hour/Minute changes (used for creation from Calendar slot click)
watch(() => [props.selectedDate, props.selectedHour, props.selectedMinute], ([newDate, newHour, newMinute]) => {
    if (!props.editingEvent) {
        form.appointmentDay = newDate;
        if (newHour !== null) {
            form.startTime = `${String(newHour).padStart(2, '0')}:${String(newMinute || 0).padStart(2, '0')}`;
            form.endTime = `${String(newHour + 1).padStart(2, '0')}:${String(newMinute || 0).padStart(2, '0')}`;
        } else {
            // Default time if only date is selected
            form.startTime = '09:00';
            form.endTime = '10:00';
        }
    }
}, { immediate: true });

// 2. Watch for editingEvent prop changes (used when clicking 'Edit' from the table)
watch(() => props.editingEvent, (newEvent) => {
    if (newEvent) {
        form.title = newEvent.title;
        form.list = newEvent.list;
        form.appointmentDay = newEvent.start.toISOString().slice(0, 10);
        form.allDay = newEvent.allDay;
        
        if (!newEvent.allDay) {
            form.startTime = dateTo24hString(newEvent.start);
            form.endTime = newEvent.end ? dateTo24hString(newEvent.end) : dateTo24hString(new Date(newEvent.start.getTime() + 30 * 60000));
        } else {
            form.startTime = '09:00';
            form.endTime = '10:00';
        }
    } else {
        // If editingEvent becomes null, reset to default (creation mode)
        Object.assign(form, getDefaultForm());
    }
}, { immediate: true });


// --- Handlers ---

const closeModal = () => {
    emit('close');
    // Reset form after closing (done by watching editingEvent = null in parent)
    Object.assign(form, getDefaultForm());
};

const handleSubmit = () => {
    let timeString = '';
    if (!form.allDay) {
        // Convert the 24hr time inputs (e.g., '09:00') to the required AM/PM format
        const start = formatTime(form.startTime);
        const end = form.endTime ? formatTime(form.endTime) : '';

        // Assemble the final time string: 'HH:MM AM-HH:MM PM'
        timeString = end ? `${start}-${end}` : start;
    }

    const eventData = {
        title: form.title,
        list: form.list,
        appointmentDay: form.appointmentDay, // YYYY-MM-DD
        allDay: form.allDay,
        time: timeString, // HH:MM AM-HH:MM PM or empty string
    };

    // Emit the success event with the data. Parent handles adding/updating based on editingEvent state.
    emit('success', eventData);
};
</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">
                {{ editingEvent ? 'Edit Appointment' : 'Add New Appointment' }}
            </h3>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="title" v-model="form.title" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                </div>

                <div>
                    <label for="list" class="block text-sm font-medium text-gray-700">Category</label>
                    <input type="text" id="list" v-model="form.list" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" id="date" v-model="form.appointmentDay" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" v-model="form.allDay" class="form-checkbox text-[#7A0C23] rounded">
                        <span class="ml-2 text-sm text-gray-700">All Day Event</span>
                    </label>
                </div>

                <div v-if="!form.allDay" class="flex space-x-4">
                    <div class="flex-1">
                        <label for="startTime" class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="time" id="startTime" v-model="form.startTime" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                    </div>
                    <div class="flex-1">
                        <label for="endTime" class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="time" id="endTime" v-model="form.endTime"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="closeModal"
                                 class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit"
                                 class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition">
                        {{ editingEvent ? 'Update Appointment' : 'Save Appointment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>