<template>
  <div v-if="isOpen && roomType" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
      <!-- Modal Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
          <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
            <IconButton
              icon="edit"
              title="Edit Room Type"
              color="blue"
              size="lg"
              disabled
            />
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-900">Edit Room Type</h2>
            <p class="text-sm text-gray-500">Update room type information</p>
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
        <!-- ID Display -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
          <div class="flex items-center mb-2">
            <IconButton
              icon="id"
              title="Room Type ID"
              color="gray"
              size="sm"
              class="mr-2"
              disabled
            />
            <span class="text-sm font-medium text-gray-700">Room Type ID</span>
          </div>
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
            #{{ form.id }}
          </span>
        </div>

        <!-- Name Field -->
        <div class="mb-6">
          <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
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
            id="edit_name"
            v-model="form.room_type_name"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200"
          />
        </div>

        <!-- Slug Field -->
        <div class="mb-6">
          <label for="edit_slug" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
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
          <input
            type="text"
            id="edit_slug"
            v-model="form.slug"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200 font-mono"
          />
        </div>

        <!-- Default Capacity Field -->
        <div class="mb-6">
          <label for="edit_capacity" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <IconButton
              icon="users"
              title="Capacity Field"
              color="blue"
              size="sm"
              class="mr-2"
              disabled
            />
            Default Capacity
          </label>
          <input
            type="number"
            id="edit_capacity"
            v-model="form.default_capacity"
            min="1"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200"
          />
        </div>

        <!-- Description Field -->
        <div class="mb-6">
          <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <IconButton
              icon="description"
              title="Description Field"
              color="gray"
              size="sm"
              class="mr-2"
              disabled
            />
            Description
          </label>
          <textarea
            id="edit_description"
            v-model="form.description"
            rows="3"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition duration-200 resize-none"
          ></textarea>
        </div>

        <!-- Features Field -->
        <div class="mb-8">
          <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
            <IconButton
              icon="features"
              title="Features Field"
              color="purple"
              size="sm"
              class="mr-2"
              disabled
            />
            Features
          </label>
          <div class="space-y-2">
            <div v-for="(feature, index) in form.features" :key="index" class="flex items-center">
              <input
                type="text"
                v-model="form.features[index]"
                class="flex-1 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none"
              />
              <button
                type="button"
                @click="removeFeature(index)"
                class="ml-2 p-2 text-red-600 hover:text-red-800"
              >
                <IconButton
                  icon="times"
                  title="Remove Feature"
                  size="sm"
                />
              </button>
            </div>
            <button
              type="button"
              @click="addFeature"
              class="flex items-center text-sm text-[#7A0C23] hover:text-red-800"
            >
              <IconButton
                icon="plus"
                title="Add Feature"
                size="sm"
                class="mr-1"
              />
              Add Feature
            </button>
          </div>
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
              title="Save Changes"
              color="white"
              size="sm"
              class="mr-2"
              disabled
            />
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
  room_type_name: '',
  slug: '',
  description: '',
  default_capacity: 30,
  features: []
});

// Watch for roomType changes
watch(() => props.roomType, (newRoomType) => {
  if (newRoomType) {
    form.value = {
      id: newRoomType.id,
      room_type_name: newRoomType.room_type_name || '',
      slug: newRoomType.slug || '',
      description: newRoomType.description || '',
      default_capacity: newRoomType.default_capacity || 30,
      features: Array.isArray(newRoomType.features) ? [...newRoomType.features] : []
    };
  }
}, { immediate: true });

// Handle features
const addFeature = () => {
  form.value.features.push('');
};

const removeFeature = (index) => {
  form.value.features.splice(index, 1);
};

// Handle form submission
const handleSubmit = () => {
  if (!form.value.room_type_name.trim() || !form.value.slug.trim()) {
    return;
  }

  // Prepare data for API
  const roomData = {
    id: form.value.id,
    room_type_name: form.value.room_type_name.trim(),
    slug: form.value.slug.trim().toLowerCase().replace(/\s+/g, '-'),
    description: form.value.description.trim() || null,
    default_capacity: parseInt(form.value.default_capacity) || 30,
    features: form.value.features.filter(f => f.trim() !== '').length > 0
      ? form.value.features.filter(f => f.trim() !== '')
      : null
  };

  emit('save', roomData);
};

// Watch for modal close
watch(() => props.isOpen, (isOpen) => {
  if (!isOpen) {
    // Reset form when modal closes
    form.value = {
      id: '',
      room_type_name: '',
      slug: '',
      description: '',
      default_capacity: 30,
      features: []
    };
  }
});
</script>

<style scoped>
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
