<script setup>
// Props for the list view
const props = defineProps({
    sortedEvents: {
        type: Array,
        required: true,
    },
    // Helper functions passed as props
    isToday: { type: Function, required: true },
    dateToDayString: { type: Function, required: true },
    dateToTimeString: { type: Function, required: true },
});

// Events to bubble up to the parent
const emit = defineEmits(['selectDate', 'selectEvent']);
</script>

<template>
    <div class="w-full space-y-4">
        <div v-if="sortedEvents.length === 0" class="text-center py-10 text-gray-500">
            No events scheduled for this month.
        </div>
        <div v-for="dayData in sortedEvents" :key="dateToDayString(dayData.date)" class="border-b pb-2">
            <h4 @click="emit('selectDate', dayData.date)" class="text-lg font-semibold cursor-pointer p-2 rounded-md hover:bg-gray-100 transition">
                {{ ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][dayData.date.getDay()] }}, {{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][dayData.date.getMonth()] }} {{ dayData.date.getDate() }}
                <span v-if="isToday(dayData.date)" class="text-red-500 text-sm ml-2">(Today)</span>
            </h4>
            <ul class="space-y-1 ml-4">
                <li v-for="event in dayData.events" :key="event.id"
                    @click="emit('selectEvent', event)"
                    :class="['cursor-pointer p-2 rounded-md hover:ring-2 transition flex justify-between items-center', event.allDay ? 'bg-green-100 text-green-800 ring-green-300' : 'bg-indigo-100 text-indigo-800 ring-indigo-300']">

                    <span class="font-medium truncate">{{ event.title }}</span>
                    <span class="text-xs ml-4 flex-shrink-0">
                        <template v-if="event.allDay">All Day</template>
                        <template v-else>{{ dateToTimeString(event.start) }} - {{ event.end ? dateToTimeString(event.end) : 'No end time' }}</template>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>
