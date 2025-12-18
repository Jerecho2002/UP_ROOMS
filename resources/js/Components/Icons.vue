<!-- Components/UI/Icons.vue -->
<script setup>
import {
  faEye,
  faPenToSquare,
  faTrash,
  faPlus,
  faSearch,
  faTimes,
  faCheck,
  faChevronLeft,
  faChevronRight,
  faList,
  faInfoCircle,
  faExclamationTriangle
} from '@fortawesome/free-solid-svg-icons';

// Export all icons as a single object
const icons = {
  eye: faEye,
  edit: faPenToSquare,
  delete: faTrash,
  plus: faPlus,
  search: faSearch,
  times: faTimes,
  check: faCheck,
  chevronLeft: faChevronLeft,
  chevronRight: faChevronRight,
  list: faList,
  info: faInfoCircle,
  warning: faExclamationTriangle
};

// Make icons available for import
defineExpose({ icons });
</script>

<template>
  <!-- This component doesn't render anything, it just exports icons -->
  <div style="display: none;"></div>
</template>
