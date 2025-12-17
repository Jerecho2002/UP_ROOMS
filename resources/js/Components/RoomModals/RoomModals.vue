<script setup>
import { reactive, computed, watch, defineProps, defineEmits } from 'vue';

import ClassForm from './forms/ClassForm.vue';
import MeetingForm from './forms/MeetingForm.vue';
import EventForm from './forms/EventForm.vue';
import OtherActivityForm from './forms/OtherActivityForm.vue';



/* ================= PROPS & EMITS ================= */
const props = defineProps({
    isVisible: Boolean,
    selectedDate: String,
    editingEvent: Object
});

const emit = defineEmits(['close', 'success']);

/* ================= FORM STATE ================= */
const form = reactive({
    type: 'Meeting',
    room: 'UG 114',

    // shared
    requester: '',
    description: '',
    numberParticipants: null,

    // date/time
    startDate: new Date(),
    durationHour: 0,
    durationMinute: 30,

    // class
    subject: '',
    section: '',
    faculty: '',
    numberOfStudents: null,

    // meeting
    agenda: '',

    // event
    title: '',
    organizer: '',

    // other
    name: ''
});

/* ================= WATCH DATE ================= */
watch(() => props.selectedDate, (val) => {
    if (val) {
        const [y, m, d] = val.split('-').map(Number);
        form.startDate = new Date(y, m - 1, d);
    }
}, { immediate: true });

/* ================= COMPUTED ================= */
const startISO = computed(() => form.startDate.toISOString());
const endISO = computed(() =>
    new Date(form.startDate.getTime() +
        (form.durationHour * 60 + form.durationMinute) * 60000
    ).toISOString()
);

/* ================= SUBMIT ================= */
const submitForm = () => {
    emit('success', {
        ...form,
        startDateTime: startISO.value,
        endDateTime: endISO.value
    });
    emit('close');
};
</script>

<template>
<div v-if="isVisible" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg">

        <!-- HEADER -->
        <div class="flex justify-between p-4 border-b">
            <h2 class="font-bold text-lg">{{ form.room }}</h2>
            <button @click="$emit('close')">✕</button>
        </div>

        <!-- BODY -->
        <form @submit.prevent="submitForm" class="p-6 space-y-4">

            <!-- TYPE -->
            <select v-model="form.type" class="border p-2 w-full">
                <option>Class</option>
                <option>Meeting</option>
                <option>Event</option>
                <option>Other type of activity</option>
            </select>

            <!-- DYNAMIC FORMS -->
            <ClassForm v-if="form.type === 'Class'" v-model="form" />
            <MeetingForm v-else-if="form.type === 'Meeting'" v-model="form" />
            <EventForm v-else-if="form.type === 'Event'" v-model="form" />
            <OtherActivityForm v-else v-model="form" />

            <!-- DURATION -->
            <div class="flex gap-2">
                <input type="number" v-model.number="form.durationHour" placeholder="Hr" class="border p-2 w-20">
                <input type="number" v-model.number="form.durationMinute" placeholder="Min" class="border p-2 w-20">
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="$emit('close')" class="border px-4 py-2">
                    Cancel
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
</template>

