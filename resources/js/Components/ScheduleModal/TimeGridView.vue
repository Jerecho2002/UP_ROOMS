<script setup>
// Props for the week/day time grid view
const props = defineProps({
    viewMode: {
        type: String,
        required: true,
        validator: (value) => ['week', 'day'].includes(value),
    },
    weekDays: {
        type: Array,
        required: true,
    },
    singleDay: {
        type: Object,
        required: true,
    },
    hourSlots: {
        type: Array,
        required: true,
    },
    // The complex event styling logic is passed as a function prop
    getEventStyle: {
        type: Function,
        required: true,
    },
    dateToDayString: { type: Function, required: true },
    dateToTimeString: { type: Function, required: true },
    days: {
        type: Array,
        required: true,
    }
});

// Events to bubble up to the parent
const emit = defineEmits(['emitDateClick', 'selectEvent']);

// Determine which data structure to use based on viewMode
const isWeekView = props.viewMode === 'week';
const currentDayData = isWeekView ? props.weekDays : [props.singleDay];
</script>

<template>
    <div :class="['w-full', isWeekView ? 'overflow-x-auto' : '']">
        <table :class="['min-w-full divide-y divide-gray-200 border border-gray-200', isWeekView ? 'table-fixed' : '']">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col" class="w-16 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    <th v-for="day in currentDayData" :key="dateToDayString(day.date)"
                        @click="isWeekView ? emit('emitDateClick', day.date) : null" :class="['px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-l', day.isToday ? 'bg-yellow-100' : 'hover:bg-gray-100', isWeekView ? 'w-1/7 cursor-pointer' : '']">
                        {{ day.label || days[day.date.getDay()] }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="w-16 px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r">All Day</td>
                    <td v-for="day in currentDayData" :key="'all-day-' + dateToDayString(day.date)"
                        :class="['p-1 border border-gray-200 align-top', day.isToday ? 'bg-yellow-50' : '']"
                        @click="emit('emitDateClick', day.date)">
                        <div class="space-y-0.5">
                            <div v-for="event in day.allDayEvents" :key="event.id"
                                @click.stop="emit('selectEvent', event)"
                                :class="['text-xs p-1 rounded truncate bg-green-200 text-green-800 cursor-pointer hover:bg-green-300 transition']"
                                :title="event.title">
                                {{ event.title }}
                            </div>
                        </div>
                    </td>
                </tr>

                <tr v-for="(slot, index) in hourSlots" :key="index" class="h-10">
                    <td class="w-16 text-xs text-gray-500 text-right pr-2 border-r border-t" :class="slot.minute === 0 ? 'border-t-2 border-gray-300' : 'border-t'">
                        {{ slot.minute === 0 ? slot.label : '' }}
                    </td>

                    <td v-for="day in currentDayData" :key="dateToDayString(day.date) + slot.label"
                        @click="emit('emitDateClick', day.date, slot.hour, slot.minute)"
                        :class="['p-0 border border-gray-200 align-top relative', day.isToday ? 'bg-yellow-50/50' : '', slot.minute === 0 ? 'border-t-2 border-gray-300' : 'border-t']">

                        <template v-for="event in day.events || day.timedEvents" :key="event.id">
                            <div v-if="event.start.getHours() === slot.hour && event.start.getMinutes() === slot.minute"
                                @click.stop="emit('selectEvent', event)"
                                class="absolute top-0 left-0 w-full text-xs p-1 rounded-sm bg-indigo-500 text-white z-10 truncate cursor-pointer hover:bg-indigo-600 transition"
                                :style="getEventStyle(event, slot.hour, slot.minute)"
                                :title="event.title + ' | ' + dateToTimeString(event.start) + ' - ' + (event.end ? dateToTimeString(event.end) : 'No end time')">
                                {{ dateToTimeString(event.start).split(' ')[0] }} {{ event.title }}
                            </div>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
