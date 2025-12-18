<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash, faPlus, faTimes, faSearch, faCheck, faWarning } from '@fortawesome/free-solid-svg-icons';

defineProps({
  icon: String,
  title: String,
  color: { type: String, default: 'gray' },
  size: { type: String, default: 'sm' },
  disabled: { type: Boolean, default: false }
});

const iconMap = {
  eye: faEye,
  edit: faPenToSquare,
  delete: faTrash,
  plus: faPlus,
  times: faTimes,
  search: faSearch,
  check: faCheck,
  warning: faWarning
};
</script>

<template>
  <button :title="title" :disabled="disabled">
    <FontAwesomeIcon :icon="iconMap[icon]" :class="`text-${color}-500 ${size === 'sm' ? 'h-4 w-4' : size === 'md' ? 'h-6 w-6' : 'h-8 w-8'}`" />
  </button>
</template>
