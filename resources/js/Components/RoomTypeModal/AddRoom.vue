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
        <div class="mb-4">
          <label for="room_type_name" class="block text-sm font-medium text-gray-700 mb-2">
            Room Type Name <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            id="room_type_name"
            v-model="form.room_type_name"
            required
            placeholder="e.g., Conference Room, Laboratory"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200"
          />
          <p class="mt-1 text-xs text-gray-500">
            Enter the display name for the room type
          </p>
        </div>

        <!-- Slug Field -->
        <div class="mb-4">
          <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
            Slug
          </label>
          <div class="relative">
            <input
              type="text"
              id="slug"
              v-model="form.slug"
              placeholder="e.g., conference-room"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200 font-mono"
            />
            <button
              type="button"
              @click="autoGenerateSlug"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xs text-[#7A0C23] hover:text-red-800 font-medium"
            >
              Auto-generate
            </button>
          </div>
          <p class="mt-1 text-xs text-gray-500">
            URL-friendly version of the name (will be auto-generated if empty)
          </p>
        </div>

        <!-- Description Field -->
        <div class="mb-4">
          <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            placeholder="Brief description of this room type..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200 resize-none"
          ></textarea>
        </div>

        <!-- Default Capacity -->
        <div class="mb-4">
          <label for="default_capacity" class="block text-sm font-medium text-gray-700 mb-2">
            Default Capacity
          </label>
          <input
            type="number"
            id="default_capacity"
            v-model.number="form.default_capacity"
            min="1"
            placeholder="30"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200"
          />
          <p class="mt-1 text-xs text-gray-500">
            Default number of seats for this room type
          </p>
        </div>

        <!-- Features -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Features (Optional)
          </label>
          <div class="space-y-2">
            <div v-for="(feature, index) in form.featuresList" :key="index" class="flex gap-2">
              <input
                type="text"
                v-model="feature.key"
                placeholder="Feature name"
                class="flex-1 px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-[#7A0C23] focus:border-transparent outline-none text-sm"
              />
              <input
                type="text"
                v-model="feature.value"
                placeholder="Value"
                class="flex-1 px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-[#7A0C23] focus:border-transparent outline-none text-sm"
              />
              <button
                type="button"
                @click="removeFeature(index)"
                class="px-2 py-1 text-red-600 hover:text-red-800"
              >
                ×
              </button>
            </div>
            <button
              type="button"
              @click="addFeature"
              class="text-sm text-[#7A0C23] hover:text-red-800 font-medium flex items-center"
            >
              + Add Feature
            </button>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-150 font-medium"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="!form.room_type_name"
            :class="[
              'px-4 py-2 rounded-lg transition-colors duration-150 font-medium shadow-sm',
              form.room_type_name
                ? 'bg-[#7A0C23] text-white hover:bg-red-800'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
            ]"
          >
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
  room_type_name: '',
  slug: '',
  description: '',
  default_capacity: 30,
  featuresList: []
});

// Auto-generate slug from name
watch(() => form.value.room_type_name, (newName) => {
  if (newName && !form.value.slug) {
    form.value.slug = newName
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  }
});

// Manual slug generation
const autoGenerateSlug = () => {
  if (form.value.room_type_name) {
    form.value.slug = form.value.room_type_name
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  }
};

// Feature management
const addFeature = () => {
  form.value.featuresList.push({ key: '', value: '' });
};

const removeFeature = (index) => {
  form.value.featuresList.splice(index, 1);
};

// Handle form submission
const handleSubmit = () => {
  if (!form.value.room_type_name.trim()) {
    return;
  }

  // Convert featuresList to object
  const features = {};
  form.value.featuresList.forEach(feature => {
    if (feature.key && feature.value) {
      features[feature.key.trim()] = feature.value.trim();
    }
  });

  // Clean up slug
  const cleanedSlug = form.value.slug
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^a-z0-9-]/g, '');

  // Emit the new room type data
  emit('save', {
    name: form.value.room_type_name.trim(),
    slug: cleanedSlug || null,
    description: form.value.description.trim() || null,
    default_capacity: form.value.default_capacity,
    features: Object.keys(features).length > 0 ? features : null
  });

  // Reset form
  resetForm();
};

// Reset form
const resetForm = () => {
  form.value = {
    room_type_name: '',
    slug: '',
    description: '',
    default_capacity: 30,
    featuresList: []
  };
};

// Watch for modal close and reset form
watch(() => props.isOpen, (isOpen) => {
  if (!isOpen) {
    resetForm();
  }
});
</script>
