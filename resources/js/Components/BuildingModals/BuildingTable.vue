<script setup>
// ============================================================
// Imports
// ============================================================
import { computed, ref, defineProps, defineEmits } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faEye, faPenToSquare, faTrash } from '@fortawesome/free-solid-svg-icons'

// ============================================================
// Props - Buildings list from parent
// ============================================================
const props = defineProps({
  buildings: {
    type: Array,
    required: true,
    default: () => []
  }
})

// ============================================================
// Emits - Communicate with parent (openModal)
// ============================================================
const emit = defineEmits(['openModal'])

// ============================================================
// Local State
// ============================================================
const searchQuery = ref('')

// ============================================================
// Icons
// ============================================================
const icons = {
  view: faEye,
  edit: faPenToSquare,
  delete: faTrash
}

// ============================================================
// Computed - Filter buildings by search input
// ============================================================
const filteredBuildings = computed(() => {
  if (!searchQuery.value) return props.buildings
  const q = searchQuery.value.toLowerCase()
  return props.buildings.filter(b =>
    b.name.toLowerCase().includes(q) ||
    b.address.toLowerCase().includes(q) ||
    (b.total_space?.toLowerCase().includes(q) ?? false)
  )
})

// ============================================================
// Methods - Emit events to parent
// ============================================================
const handleAdd = () => emit('openModal', 'add')
const handleEdit = building => emit('openModal', 'edit', building)
const handleDelete = building => emit('openModal', 'delete', building)
const handleView = building => emit('openModal', 'view', building)
</script>

<template>
  <div class="p-4 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto">

      <!-- ======================= Search & Add ======================= -->
        <h6 class="font-bold text-l text-[#7A0C23] mt-4  ">TABLE LIST 📃</h6>
       
      <div class="pt-7 mb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        
        <!-- 🔍 Search Bar -->
        <div class="relative w-full sm:w-96">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search buildings by name..."
            class="max-w-[300px] border border-gray-300 rounded-lg pl-10 pr-4 py-2 w-full focus:ring-2 focus:ring-[#7A0C23] focus:outline-none"
          />
          <!-- Search Icon -->
          <svg
            class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>

        <!-- ➕ Add Button -->
        <button
          @click="handleAdd"
          class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
          Add Building
        </button>
      </div>

      <!-- ======================= Buildings Table ======================= -->
      <div class="bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
          <table class="min-w-full text-sm text-gray-800">
            <thead class="bg-[#7A0C23] text-white sticky top-0 shadow z-10">
              <tr>
                <th class="px-6 py-3 text-left uppercase font-semibold">Building</th>
                <th class="px-6 py-3 text-left uppercase font-semibold">Location</th>
                <th class="px-6 py-3 text-center uppercase font-semibold">Actions</th>
              </tr>
            </thead>

            <tbody>
              <!-- 🔁 Each Building Row -->
              <tr
                v-for="b in filteredBuildings"
                :key="b.id"
                class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition border-b border-gray-200">
                <td class="px-6 py-4 font-medium truncate">{{ b.name }}</td>
                <td class="px-6 py-4 text-gray-600 truncate">{{ b.address }}</td>
                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center space-x-3">
                    <!-- 👁️ View Button -->
                    <button
                      @click="handleView(b)"
                      title="View Details"
                      class="text-sky-500 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 p-1 rounded-full transition transform hover:scale-110">
                      <FontAwesomeIcon :icon="icons.view" class="h-5 w-5" />
                    </button>
                    <!-- ✏️ Edit Button -->
                    <button
                      @click="handleEdit(b)"
                      title="Edit Building"
                      class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 p-1 rounded-full transition transform hover:scale-110">
                      <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                    </button>
                    <!-- 🗑️ Delete Button -->
                    <button
                      @click="handleDelete(b)"
                      title="Delete Building"
                      class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 p-1 rounded-full transition transform hover:scale-110">
                      <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                    </button>
                  </div>
                </td>
              </tr>

              <!-- ❗ No Data Found -->
              <tr v-if="filteredBuildings.length === 0">
                <td colspan="3" class="text-center py-10 text-gray-500 bg-white">
                  No buildings found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</template>
