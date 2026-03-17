<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

defineProps({
    label: { type: String, required: true },
    icon: { type: String, default: null },
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    disabled: { type: Boolean, default: false },
})

defineEmits(['click'])
</script>

<template>
    <button :type="type" :disabled="disabled" @click="$emit('click')"
        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-medium transition duration-150" :class="{
            'bg-green-600 hover:bg-green-700 text-white': variant === 'primary',
            'bg-red-600 hover:bg-red-700 text-white': variant === 'danger',
            'bg-gray-200 hover:bg-gray-300 text-gray-700': variant === 'secondary',
            'opacity-50 pointer-events-none': disabled
        }">
        <FontAwesomeIcon v-if="icon" :icon="icon" />
        {{ label }}
    </button>
</template>