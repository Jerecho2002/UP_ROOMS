<template>
  <div v-if="isOpen && roomType" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-5">
      <!-- Modal Header -->
      <div class="flex items-center justify-between mb-5">
        <div class="flex items-center">
          <div class="flex-shrink-0 h-9 w-9 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
            <IconButton
              icon="edit"
              title="Edit Room Type"
              color="blue"
              size="md"
              disabled
            />
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Edit Room Type</h2>
            <p class="text-xs text-gray-500">Update room type information</p>
          </div>
        </div>
        <IconButton
          icon="times"
          title="Close"
          color="gray"
          size="sm"
          @click="$emit('close')"
        />
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- ID Display -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Room Type ID
          </label>
          <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded text-gray-700">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-sm font-medium bg-blue-100 text-blue-800">
              #{{ form.id }}
            </span>
          </div>
        </div>

        <!-- Name Field -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Name <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            v-model="form.name"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none"
          />
        </div>

        <!-- Slug Field -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Slug <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            v-model="form.slug"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none font-mono"
          />
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-2 pt-4 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-[#7A0C23] text-white rounded hover:bg-red-800 transition"
          >
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
  isOpen: Boolean,
  roomType: Object
});

const emit = defineEmits(['close', 'save']);

const form = ref({
  id: '',
  name: '',
  slug: ''
});

watch(() => props.roomType, (newRoomType) => {
  if (newRoomType) {
    form.value = {
      id: newRoomType.id || '',
      name: newRoomType.name || '',
      slug: newRoomType.slug || ''
    };
  }
}, { immediate: true });

const handleSubmit = () => {
  if (!form.value.name.trim() || !form.value.slug.trim()) return;

  emit('save', {
    id: form.value.id,
    name: form.value.name.trim(),
    slug: form.value.slug.toLowerCase().replace(/\s+/g, '-')
  });

  form.value = { id: '', name: '', slug: '' };
};

watch(() => props.isOpen, (isOpen) => {
  if (!isOpen) form.value = { id: '', name: '', slug: '' };
});
</script>
