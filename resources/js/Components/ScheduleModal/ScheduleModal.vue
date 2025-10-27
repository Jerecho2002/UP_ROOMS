<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3'; // Import Inertia form helper

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false
    },
    // Pass the date selected by the user when they click the calendar
    selectedDate: {
        type: String, // YYYY-MM-DD
        default: ''
    }
});

const emit = defineEmits(['close']);

// Inertia Form for submission to Laravel
const form = useForm({
    title: '',
    list: '',
    appointmentDay: props.selectedDate,
    time: '', // e.g., '10:00 AM-12:00 PM'
    room_id: null, // Assuming you link to a Room ID
});

// Watch for selectedDate changes or modal opening to update the form date
watch(() => props.selectedDate, (newDate) => {
    form.appointmentDay = newDate;
});

watch(() => props.isVisible, (newVal) => {
    if (newVal) {
        // Reset form to initial state when opened
        form.reset();
        form.appointmentDay = props.selectedDate; // Re-apply the selected date
    }
});

const handleSubmit = () => {
    if (!form.title || !form.appointmentDay) {
        alert('Title and Appointment Day are required.');
        return;
    }

    // Post data to the Laravel controller
    form.post(route('schedule.store'), {
        onSuccess: () => {
            handleClose();
        },
        onError: (errors) => {
            console.error('Validation Errors:', errors);
            alert('Error saving appointment. Check console for details.');
        }
    });
};

const handleClose = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900 bg-opacity-50" @click.self="handleClose">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 p-6">
            <h3 class="text-2xl font-semibold text-gray-800">Add New Appointment 📅</h3>
            <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="title" v-model="form.title" required class="mt-1 block w-full border-gray-300 rounded-md p-2">
                </div>

                <div class="flex space-x-4">
                    <div class="flex-1">
                        <label for="day" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" id="day" v-model="form.appointmentDay" required class="mt-1 block w-full border-gray-300 rounded-md p-2">
                    </div>
                    <div class="flex-1">
                        <label for="time" class="block text-sm font-medium text-gray-700">Time (e.g., 10:00 AM-12:00 PM)</label>
                        <input type="text" id="time" v-model="form.time" class="mt-1 block w-full border-gray-300 rounded-md p-2">
                    </div>
                </div>

                <div>
                    <label for="list" class="block text-sm font-medium text-gray-700">List/Category</label>
                    <input type="text" id="list" v-model="form.list" class="mt-1 block w-full border-gray-300 rounded-md p-2">
                </div>
                
                <div class="flex justify-end pt-4 space-x-3">
                    <button type="button" @click="handleClose" class="py-2 px-4 border rounded-md">
                        Cancel
                    </button>
                    <button type="submit" :disabled="form.processing" class="py-2 px-4 border rounded-md text-white bg-green-600 hover:bg-green-700">
                        Save Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>