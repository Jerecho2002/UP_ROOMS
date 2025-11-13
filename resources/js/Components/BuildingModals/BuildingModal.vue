<script setup>
import { ref, computed, defineProps, defineEmits, watch } from 'vue'

// ============================================================
// Props
// ============================================================
const props = defineProps({
  isVisible: Boolean,
  type: String, // add | view | edit | delete
  building: Object
})

// ============================================================
// Emits
// ============================================================
const emit = defineEmits(['close', 'dataUpdated'])

// ============================================================
// Local State
// ============================================================
const formData = ref({})

// ============================================================
// Watchers - Reset / Load Form Data Based on Modal Type
// ============================================================
watch(
  () => [props.building, props.type],
  ([b, type]) => {
    if (type === 'add') {
      formData.value = {
        name: '',
        address: '',
        total_space: '',
        lift: '',
        parking: false,
        cr_count: '',
        ramps: '',
        floors: ''
      }
    } else if (b) {
      formData.value = {
        id: b.id || null,
        name: b.name || '',
        address: b.address || '',
        total_space: b.total_space || '',
        lift: b.lift || '',
        parking: !!b.parking,
        cr_count: b.cr_count || '',
        ramps: b.ramps || '',
        floors: b.floors || ''
      }
    } else {
      formData.value = {}
    }
  },
  { immediate: true }
)

// ============================================================
// Computed - Modal Title & Type Flags
// ============================================================
const modalTitle = computed(() => {
  const name = props.building?.name || 'Building'
  switch (props.type) {
    case 'add': return 'Add New Building'
    case 'edit': return `Edit Building: ${name}`
    case 'view': return `View Details: ${name}`
    case 'delete': return `Delete Building: ${name}`
    default: return 'Building Modal'
  }
})

const isAdd = computed(() => props.type === 'add')
const isEdit = computed(() => props.type === 'edit')
const isView = computed(() => props.type === 'view')
const isDelete = computed(() => props.type === 'delete')

// ============================================================
// Handlers
// ============================================================
const handleSubmit = () => emit('dataUpdated', formData.value, props.type)
const handleDelete = () => emit('dataUpdated', props.building, 'delete')
</script>

<template>
  <Transition name="fade">
    <div
      v-if="isVisible"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
      @click.self="emit('close')">

      <!-- Modal Container -->
      <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
          <h3 class="text-2xl font-semibold text-gray-800">{{ modalTitle }}</h3>
          <button @click="emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <!-- ======================= VIEW MODE ======================= -->
        <div v-if="isView" class="space-y-3 text-gray-700">
          <p><strong>ID:</strong> {{ building.id }}</p>
          <p><strong>Name:</strong> {{ building.name }}</p>
          <p><strong>Address:</strong> {{ building.address }}</p>
          <p><strong>Total Space:</strong> {{ building.total_space }}</p>
          <p><strong>Lift:</strong> {{ building.lift }}</p>
          <p><strong>Number of CR:</strong> {{ building.cr_count }}</p>
          <p><strong>Ramps:</strong> {{ building.ramps }}</p>
          <p><strong>Floors:</strong> {{ building.floors }}</p>
          <p>
            <strong>Parking:</strong>
            <span :class="building.parking ? 'text-green-600' : 'text-red-600'">
              {{ building.parking ? 'Available' : 'Unavailable' }}
            </span>
          </p>

          <div class="mt-6 border-t pt-4 flex justify-end">
            <button @click="emit('close')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">
              Close
            </button>
          </div>
        </div>

        <!-- ======================= ADD / EDIT FORM ======================= -->
        <form v-else-if="isAdd || isEdit" @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Building Name</label>
            <input v-model="formData.name" required class="mt-1 w-full border rounded-md p-2" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Location</label>
            <input v-model="formData.address" required class="mt-1 w-full border rounded-md p-2" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Total Space</label>
            <input v-model="formData.total_space" required class="mt-1 w-full border rounded-md p-2" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Elevator</label>
              <input v-model="formData.lift" class="mt-1 w-full border rounded-md p-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Parking</label>
              <select v-model="formData.parking" class="mt-1 w-full border rounded-md p-2">
                <option :value="true">Yes</option>
                <option :value="false">No</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Number of CR</label>
              <input v-model="formData.cr_count" class="mt-1 w-full border rounded-md p-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Ramps</label>
              <input v-model="formData.ramps" class="mt-1 w-full border rounded-md p-2" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Floors</label>
            <input v-model="formData.floors" class="mt-1 w-full border rounded-md p-2" />
          </div>

          <div class="pt-4 border-t flex justify-end space-x-3">
            <button type="button" @click="emit('close')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</button>
            <button type="submit"
                    :class="isAdd ? 'bg-green-600' : 'bg-blue-600'"
                    class="text-white px-4 py-2 rounded-lg">
              {{ isAdd ? 'Add Building' : 'Save Changes' }}
            </button>
          </div>
        </form>

        <!-- ======================= DELETE CONFIRMATION ======================= -->
        <div v-else-if="isDelete" class="space-y-4">
          <p class="text-lg text-red-600 font-semibold">
            Are you sure you want to delete <strong>{{ building.name }}</strong>?
          </p>
          <p class="text-gray-600">This action cannot be undone.</p>
          <div class="mt-6 border-t pt-4 flex justify-end space-x-3">
            <button @click="emit('close')" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</button>
            <button @click="handleDelete" class="bg-red-600 text-white px-4 py-2 rounded-lg">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
