<script setup>
// Props for the month grid view
const props = defineProps({
    dateGrid: {
        type: Array,
        required: true,
    },
    days: {
        type: Array,
        required: true,
    }
});

// Events to bubble up to the parent
const emit = defineEmits(['emitDateClick', 'selectEvent']);
</script>

<template>
    <div class="w-full">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 table-fixed">
            <thead>
                <tr class="bg-[#7A0C23]">
                    <th v-for="day in days" :key="day" scope="col" class="w-1/7 px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                        {{ day }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(week, index) in dateGrid" :key="index">
                    <td v-for="day in week" :key="day.date.toString()"
                        @click="emit('emitDateClick', day.date)" :class="[
                            'p-2 align-top h-24 border border-yellow-500 text-sm cursor-pointer transition duration-150',
                            day.dayClass,
                            day.isToday ? 'bg-yellow-400 ring-2 ring-red-500/50' : 'hover:bg-blue-50',
                        ]">

                        <div class="font-bold mb-1 text-right">{{ day.date.getDate() }}</div>

                        <div class="space-y-0.5 overflow-hidden max-h-16">
                            <div v-for="event in day.allDayEvents.slice(0, 1)" :key="event.id + '-all'"
                                @click.stop="emit('selectEvent', event)"
                                :class="['text-xs p-1 rounded truncate bg-green-200 text-green-800 hover:bg-green-300 transition']"
                                :title="event.title">
                                All Day: {{ event.title }}
                            </div>
                            <div v-for="event in day.events.slice(0, 1)" :key="event.id + '-time'"
                                @click.stop="emit('selectEvent', event)"
                                :class="['text-xs p-1 rounded truncate bg-indigo-200 text-indigo-800 hover:bg-indigo-300 transition']"
                                :title="event.title">
                                {{ day.events.length > 0 ? day.events[0].start.toLocaleTimeString().split(' ')[0] : '' }} {{ event.title }}
                            </div>
                            <div v-if="(day.allDayEvents.length + day.events.length) > 2" class="text-xs text-center text-gray-500 mt-1">
                                +{{ (day.allDayEvents.length + day.events.length) - 2 }} more
                            </div>
                        </div>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
