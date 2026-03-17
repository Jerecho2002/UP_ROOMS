<script setup>
import Pagination from '@/Components/Pagination.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

defineProps({
    items: Array,
    columns: Array,
    links: Array,
})

defineEmits(['view', 'edit', 'delete'])
</script>

<template>
    <div class="bg-white rounded-xl shadow-2xl border border-yellow-300 overflow-hidden">
        <table class="min-w-full text-sm table-fixed">
            <thead class="bg-[#7A0C23] text-white">
                <tr>
                    <th v-for="col in columns" :key="col.field" class="px-4 py-3"
                        :class="col.align === 'center' ? 'text-center' : 'text-left'">
                        {{ col.label }}
                    </th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!items?.length">
                    <td :colspan="columns.length + 1" class="text-center py-10 text-gray-500">
                        No records found.
                    </td>
                </tr>
                <tr v-for="item in items" :key="item.id" class="odd:bg-white even:bg-gray-100">
                    <td v-for="col in columns" :key="col.field" class="px-4 py-3"
                        :class="col.align === 'center' ? 'text-center' : ''">
                        {{ col.render ? col.render(item) : (item[col.field] ?? 'N/A') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-center gap-2">
                            <button @click="$emit('view', item)"
                                class="px-3 py-1 text-xs rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                                <FontAwesomeIcon icon="eye" />
                            </button>
                            <button @click="$emit('edit', item)"
                                class="px-3 py-1 text-xs rounded-md bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                                <FontAwesomeIcon icon="pen" />
                            </button>
                            <button @click="$emit('delete', item)"
                                class="px-3 py-1 text-xs rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition">
                                <FontAwesomeIcon icon="trash" />
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <Pagination :links="links" />
    </div>
</template>

<style scoped>
table {
    table-layout: fixed;
    width: 100%;
}

td,
th {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>