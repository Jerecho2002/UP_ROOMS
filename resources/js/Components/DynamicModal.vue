<script setup>
import { reactive, watch } from 'vue'
import DynamicButton from '@/Components/DynamicButton.vue'
import Multiselect from '@vueform/multiselect'

const props = defineProps({
    type: { type: String, required: true },
    title: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
    fields: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'submit'])

// Main form state
const form = reactive({})

const dynamicMultiselects = reactive({})

watch(() => props.data, (val) => {
    props.fields.forEach(f => {
        if (f.type === 'multiselect') {
            const initial = Array.isArray(val[f.field]) ? val[f.field] : []
            dynamicMultiselects[f.field] = initial.length ? [...initial] : []
        }

        const keys = f.field.split('.')
        let raw = val
        for (const key of keys) {
            raw = raw?.[key] ?? ''
            if (raw === '') break
        }

        if (f.type === 'multiselect') {
            form[f.field] = Array.isArray(raw) ? raw : []
        } else if (f.type === 'select') {
            if (f.options && f.options.length && typeof f.options[0].value === 'number') {
                form[f.field] = Number(raw)
            } else {
                form[f.field] = raw
            }
        }
        else if (f.type === 'equipment-qty') {
            const initial = Array.isArray(val[f.field]) ? val[f.field] : []
            dynamicMultiselects[f.field] = initial.length
                ? initial.map(e => ({ id: e.id, qty: e.qty ?? 0 }))
                : []
        } else {
            form[f.field] = raw
        }
    })
}, { immediate: true })

const addOption = (field, options = []) => {
    const isEquipmentQty = field in dynamicMultiselects && options.length > 0
    if (isEquipmentQty) {
        dynamicMultiselects[field].push({ id: options[0].id, qty: 0 })
    } else {
        dynamicMultiselects[field].push(null)
    }
}
const removeOption = (field, index) => dynamicMultiselects[field].splice(index, 1)

const handleSubmit = () => {
    Object.keys(dynamicMultiselects).forEach(field => {
        form[field] = dynamicMultiselects[field].filter(v => v !== null && v !== '')
    })
    emit('submit', { ...form })
}

const resolveField = (obj, path) => {
    const value = path.split('.').reduce((o, key) => o?.[key], obj)

    if (Array.isArray(value)) {
        return value.length ? value.join(', ') : 'N/A'
    }

    return value ?? 'N/A'
}

const getFieldValue = (obj, path) => {
    return path.split('.').reduce((o, key) => o?.[key], obj)
}
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-md shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">

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

                <div v-if="type === 'delete'" class="text-gray-700">
                    Are you sure you want to delete this record? This action cannot be undone.
                </div>

                <!-- Dynamic fields -->
                <template v-else>
                    <div v-for="f in fields" :key="f.field">
                        <!-- Skip Departments only in add/edit -->
                        <template v-if="!(
                            (f.hideOnEdit && (type === 'add' || type === 'edit')) ||
                            (f.hideOnView && type === 'view')
                        )">

                            <label class="text-sm text-gray-600">{{ f.label }}</label>

                            <!-- VIEW MODE -->
                            <div v-if="type === 'view'" class="mt-1">
                                <!-- Display badges -->
                                <div v-if="f.type === 'display-badges'" class="flex flex-wrap gap-2">
                                    <template v-if="(f.render ? f.render(data) : []).length">
                                        <span v-for="(val, i) in (f.render ? f.render(data) : [])" :key="i"
                                            class="px-3 py-1 text-xs rounded-full bg-[#7A0C23] text-white font-medium">
                                            {{ val }}
                                        </span>
                                    </template>
                                    <span v-else class="text-sm text-gray-500">N/A</span>
                                </div>

                                <!-- Multiselect -->
                                <div v-else-if="f.type === 'multiselect'"
                                    class="text-sm text-gray-800 bg-gray-100 px-3 py-2 rounded-md">
                                    {{
                                        (f.options || []).filter(opt => (data[f.field] || []).includes(opt.value)).map(o =>
                                            o.label).join(', ') || 'N/A'
                                    }}
                                </div>

                                <!-- Select -->
                                <div v-else-if="f.type === 'select'"
                                    class="text-sm text-gray-800 bg-gray-100 px-3 py-2 rounded-md">
                                    {{
                                        (f.options || []).find(opt => opt.value === data[f.field])?.label || 'N/A'
                                    }}
                                </div>

                                <div v-else-if="f.type === 'equipment-qty'" class="space-y-2">
                                    <template v-if="(getFieldValue(data, f.field) || []).length">
                                        <div v-for="(eq, idx) in getFieldValue(data, f.field)" :key="idx"
                                            class="flex justify-between items-center bg-gray-100 px-3 py-2 rounded-md text-sm">

                                            <span class="font-medium">
                                                {{f.options.find(o => o.id === eq.id)?.name || 'Unknown'}}
                                            </span>

                                            <span class="text-gray-600 text-xs">
                                                Qty: {{ eq.qty }} |
                                                Stock: {{f.options.find(o => o.id === eq.id)?.stock ?? 0}}
                                            </span>
                                        </div>
                                    </template>

                                    <span v-else class="text-sm text-gray-500">N/A</span>
                                </div>

                                <!-- Input / Text / Email / Number / Default -->
                                <div v-else class="text-sm text-gray-800 bg-gray-100 px-3 py-2 rounded-md">
                                    {{ f.render ? f.render(data) : resolveField(data, f.field) }}
                                </div>
                            </div>

                            <!-- EDIT / ADD MODE -->
                            <template v-else>
                                <div v-if="f.type === 'multiselect'" class="space-y-2">
                                    <div v-for="(value, idx) in dynamicMultiselects[f.field]" :key="idx"
                                        class="flex gap-2 items-center">
                                        <Multiselect v-model="dynamicMultiselects[f.field][idx]" :options="f.options"
                                            :value-prop="'value'" label="label" :searchable="true"
                                            :placeholder="`Select ${f.label}`" class="w-full ms-yellow" />
                                        <button type="button" class="text-red-500 font-bold px-2"
                                            @click="removeOption(f.field, idx)">
                                            &times;
                                        </button>
                                    </div>
                                    <button type="button" class="text-green-600 font-semibold mt-1"
                                        @click="addOption(f.field)">
                                        + Add {{ f.label }}
                                    </button>
                                </div>

                                <textarea v-else-if="f.type === 'textarea'" v-model="form[f.field]"
                                    class="w-full border border-yellow-300 rounded-md px-3 py-2 text-sm mt-1 resize-none"
                                    rows="3" />

                                <Multiselect v-else-if="f.type === 'select'" v-model="form[f.field]"
                                    :options="f.options" :value-prop="'value'" label="label" :can-clear="false"
                                    :searchable="true" :placeholder="`Select ${f.label}`" class="mt-1 ms-yellow" />

                                <div v-else-if="f.type === 'equipment-qty'" class="space-y-2">
                                    <div v-for="(eq, idx) in dynamicMultiselects[f.field]" :key="idx"
                                        class="flex gap-2 items-center">
                                        <Multiselect v-model="dynamicMultiselects[f.field][idx].id" :options="f.options"
                                            :value-prop="'id'" label="name" :searchable="true"
                                            :placeholder="`Select ${f.label}`" class="w-full ms-yellow" />
                                        <input type="number" v-model.number="dynamicMultiselects[f.field][idx].qty"
                                            :max="f.options.find(o => o.id === eq.id)?.stock ?? 0" min="0"
                                            class="w-1/3 border border-yellow-300 rounded-md px-2 py-1 text-sm" />
                                        <span class="text-xs text-gray-600 w-24">
                                            Stock: {{f.options.find(o => o.id === eq.id)?.stock ?? 0}}
                                        </span>
                                        <button type="button" class="text-red-500 font-bold px-2"
                                            @click="removeOption(f.field, idx)">
                                            &times;
                                        </button>
                                    </div>
                                    <button type="button" class="text-green-600 font-semibold mt-1"
                                        @click="addOption(f.field, f.options)">
                                        + Add {{ f.label }}
                                    </button>
                                </div>

                                <input v-else v-model="form[f.field]" :type="f.type ?? 'text'"
                                    class="w-full border border-yellow-300 rounded-md px-3 py-2 text-sm mt-1" />
                            </template>
                        </template>
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
<style>
.ms-yellow {
    --ms-border-color: #fde047;
    --ms-border-color-active: #fde047;
    --ms-ring-color: transparent;
    --ms-radius: 0.375rem;
    --ms-font-size: 0.875rem;
    --ms-option-bg-selected: #7A0C23;
    --ms-option-bg-selected-pointed: #9b0f2d;
}
</style>