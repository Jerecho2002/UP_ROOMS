<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Add New Appointment</h3>
            
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
                        Save Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch, defineProps, defineEmits } from 'vue';

const props = defineProps({
    isVisible: Boolean,
    selectedDate: String, // YYYY-MM-DD
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

const form = reactive({
    title: '',
    list: 'New Appointment',
    appointmentDay: props.selectedDate,
    allDay: false,
    startTime: '09:00',
    endTime: '10:00',
});

// Watch for selectedDate prop changes and update the form
watch(() => props.selectedDate, (newDate) => {
    form.appointmentDay = newDate;
});

const closeModal = () => {
    emit('close');
    // Reset form for next use
    Object.assign(form, {
        title: '',
        list: 'New Appointment',
        // Set date back to today's date if the prop is null, or use a clean default
        appointmentDay: new Date().toISOString().slice(0, 10), 
        allDay: false,
        startTime: '09:00',
        endTime: '10:00',
    });
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

    const newAppointment = {
        title: form.title,
        list: form.list, 
        appointmentDay: form.appointmentDay, // YYYY-MM-DD
        time: timeString, // HH:MM AM-HH:MM PM or empty string
    };

    // Emit the success event with the new data
    emit('success', newAppointment);
    
    // Parent will call closeModal after processing the success event
};
</script>