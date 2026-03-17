<script setup>
import { reactive, watch } from 'vue'
import DynamicButton from '@/Components/DynamicButton.vue'

const props = defineProps({
    type: {
        type: String, // 'add' | 'edit' | 'view' | 'delete'
        required: true
    },
    title: {
        type: String,
        required: true
    },
    data: {
        type: Object,
        default: () => ({})
    },
    fields: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['close', 'submit'])

// Internal form state
const form = reactive({})

// Populate form when data changes (edit/view)
watch(() => props.data, (val) => {
    props.fields.forEach(f => {
        const raw = val?.[f.field] ?? ''

        if (f.type === 'select') {
            if (f.options && f.options.length && typeof f.options[0].value === 'number') {
                form[f.field] = Number(raw)
            } else {
                form[f.field] = raw
            }
        } else {
            form[f.field] = raw
        }
    })
}, { immediate: true })

const handleSubmit = () => {
    emit('submit', { ...form })
}
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-md shadow-xl w-full max-w-md mx-4 max-h-[90vh] flex flex-col">

            <!-- Header -->
            <div class="flex justify-between items-center px-6 py-4 bg-[#7A0C23] rounded-t-md">
                <h2 class="text-white font-semibold text-lg">
                    {{ type === 'add' ? `Add ${title}` :
                        type === 'edit' ? `Edit ${title}` :
                            type === 'view' ? `View ${title}` :
                                `Delete ${title}` }}
                </h2>
                <button @click="$emit('close')"
                    class="text-white hover:opacity-70 text-xl leading-none">&times;</button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 space-y-4 max-h-[60vh] overflow-y-auto">

                <!-- Delete confirmation -->
                <div v-if="type === 'delete'" class="text-gray-700">
                    Are you sure you want to delete this record? This action cannot be undone.
                </div>

                <!-- Dynamic fields -->
                <template v-else>
                    <div v-for="f in fields" :key="f.field">
                        <label class="text-sm text-gray-600">{{ f.label }}</label>

                        <!-- Textarea -->
                        <textarea v-if="f.type === 'textarea'" v-model="form[f.field]" :disabled="type === 'view'"
                            class="w-full border border-yellow-300 rounded-md px-3 py-2 text-sm mt-1 resize-none"
                            rows="3" />

                        <!-- Select -->
                        <select v-else-if="f.type === 'select'" v-model="form[f.field]" :disabled="type === 'view'"
                            class="w-full border border-yellow-300 rounded-md px-3 py-2 text-sm mt-1">
                            <option value="" disabled>Select {{ f.label }}</option>
                            <option v-for="opt in f.options" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>

                        <!-- Text / Number / Email -->
                        <input v-else v-model="form[f.field]" :type="f.type ?? 'text'" :disabled="type === 'view'"
                            class="w-full border border-yellow-300 rounded-md px-3 py-2 text-sm mt-1" />
                    </div>
                </template>

            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <DynamicButton label="Cancel" variant="secondary" @click="$emit('close')" />
                <DynamicButton v-if="type !== 'view'" :label="type === 'delete' ? 'Delete' : 'Save'"
                    :variant="type === 'delete' ? 'danger' : 'primary'" @click="handleSubmit" />
            </div>

        </div>
    </div>
</template>