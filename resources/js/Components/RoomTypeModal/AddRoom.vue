<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
      <!-- Modal Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
          <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
            <IconButton
              icon="plus"
              title="Add Room Type"
              color="green"
              size="lg"
              disabled
            />
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Add New Room Type</h2>
            <p class="text-sm text-gray-500">Create a new room type for your facility</p>
          </div>
        </div>
        <IconButton
          icon="times"
          title="Close Modal"
          color="gray"
          size="md"
          @click="$emit('close')"
        />
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit">
        <!-- Name Field -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <IconButton
              icon="info"
              title="Required Field"
              color="blue"
              size="sm"
              class="mr-2"
              disabled
            />
            Room Type Name <span class="text-red-500 ml-1">*</span>
          </label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            required
            placeholder="e.g., Conference Room, Laboratory"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200"
          />
          <p class="mt-2 text-sm text-gray-500 flex items-center">
            <IconButton
              icon="info"
              title="Information"
              color="gray"
              size="xs"
              class="mr-1"
              disabled
            />
            Enter the display name for the room type
          </p>
        </div>

        <!-- Slug Field -->
        <div class="mb-6">
          <label for="slug" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <IconButton
              icon="info"
              title="Required Field"
              color="blue"
              size="sm"
              class="mr-2"
              disabled
            />
            Slug <span class="text-red-500 ml-1">*</span>
          </label>
          <div class="relative">
            <input
              type="text"
              id="slug"
              v-model="form.slug"
              required
              placeholder="e.g., conference-room"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200 font-mono"
              @input="generateSlug"
            />
            <button
              type="button"
              @click="autoGenerateSlug"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-sm text-[#7A0C23] hover:text-red-800 font-medium flex items-center"
            >
              <IconButton
                icon="magic"
                title="Auto-generate Slug"
                color="purple"
                size="sm"
                class="mr-1"
                disabled
              />
              Auto-generate
            </button>
          </div>
          <p class="mt-2 text-sm text-gray-500 flex items-center">
            <IconButton
              icon="info"
              title="Information"
              color="gray"
              size="xs"
              class="mr-1"
              disabled
            />
            URL-friendly version of the name (lowercase, hyphens for spaces)
          </p>
        </div>

        <!-- Description Field (Optional) -->
        <div class="mb-8">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <IconButton
              icon="info"
              title="Optional Field"
              color="gray"
              size="sm"
              class="mr-2"
              disabled
            />
            Description (Optional)
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            placeholder="Brief description of this room type..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200 resize-none"
          ></textarea>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="flex items-center px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-150 font-medium"
          >
            <IconButton
              icon="times"
              title="Cancel"
              color="gray"
              size="sm"
              class="mr-2"
              disabled
            />
            Cancel
          </button>
          <button
            type="submit"
            class="flex items-center px-5 py-2.5 bg-[#7A0C23] text-white rounded-lg hover:bg-red-800 transition-colors duration-150 font-medium shadow-sm"
          >
            <IconButton
              icon="check"
              title="Save Room Type"
              color="white"
              size="sm"
              class="mr-2"
              disabled
            />
            Add Room Type
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
  isOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['close', 'save']);

const form = ref({
  name: '',
  slug: '',
  description: ''
});

// Auto-generate slug from name
const generateSlug = () => {
  if (form.value.name && !form.value.slug) {
    form.value.slug = form.value.name
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
      .replace(/\s+/g, '-') // Replace spaces with hyphens
      .replace(/-+/g, '-') // Replace multiple hyphens with single
      .trim();
  }
};

// Manual slug generation
const autoGenerateSlug = () => {
  if (form.value.name) {
    form.value.slug = form.value.name
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  }
};

// Handle form submission
const handleSubmit = () => {
  // Basic validation
  if (!form.value.name.trim() || !form.value.slug.trim()) {
    return;
  }

  // Clean up slug
  const cleanedSlug = form.value.slug
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^a-z0-9-]/g, '');

  // Emit the new room type data
  emit('save', {
    name: form.value.name.trim(),
    slug: cleanedSlug,
    description: form.value.description.trim()
  });

  // Reset form
  form.value = {
    name: '',
    slug: '',
    description: ''
  };
};

// Watch for modal close and reset form
watch(() => props.isOpen, (isOpen) => {
  if (!isOpen) {
    form.value = {
      name: '',
      slug: '',
      description: ''
    };
  }
});
</script>

<style scoped>
/* Custom scrollbar for modal */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>
