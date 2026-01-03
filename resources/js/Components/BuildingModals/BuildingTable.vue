<template>
  <div class="p-4 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto">
      <h6 class="font-bold text-l text-[#7A0C23] mt-4">Building LIST 📃</h6>

      <div class="pt-7 mb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <!-- Search Bar -->
        <div class="relative w-full sm:w-96">
          <input
            type="text"
            v-model="searchQuery"
            @input="handleSearch"
            placeholder="Search buildings by name..."
            class="max-w-[300px] border border-yellow-300 rounded-lg pl-10 pr-4 py-2 w-full focus:ring-2 focus:ring-[#7A0C23] focus:outline-none"
          />
          <IconButton
            icon="search"
            size="sm"
            color="gray"
            class="absolute left-3 top-1/2 transform -translate-y-1/2"
          />
        </div>

        <!-- Add Button -->
        <IconButton
          @click="handleAdd"
          icon="plus"
          title="Add Building"
          size="md"
          color="green"
          outlined
          class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition"
        >
          Add Building
        </IconButton>
      </div>

      <!-- Buildings Table Container -->
      <div class="bg-white rounded-xl shadow-2xl border border-yellow-300 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-gray-800 table-fixed">
            <colgroup>
              <col class="w-[25%]"> <!-- Building Name -->
              <col class="w-[25%]"> <!-- Address -->
              <col class="w-[15%]"> <!-- College -->
              <col class="w-[10%]"> <!-- Total Floors -->
              <col class="w-[10%]"> <!-- Total Rooms -->
              <col class="w-[15%]"> <!-- Actions -->
            </colgroup>
            <thead class="bg-[#7A0C23] text-white sticky top-0 shadow z-10">
              <tr>
                <th class="px-4 py-3 text-left uppercase font-semibold truncate">Building</th>
                <th class="px-4 py-3 text-left uppercase font-semibold truncate">Address</th>
                <th class="px-4 py-3 text-left uppercase font-semibold truncate">College</th>
                <th class="px-4 py-3 text-left uppercase font-semibold truncate">Total Floors</th>
                <th class="px-4 py-3 text-left uppercase font-semibold truncate">Total Rooms</th>
                <th class="px-4 py-3 text-center uppercase font-semibold truncate">Actions</th>
              </tr>
            </thead>

            <tbody>
              <!-- Loading State -->
              <tr v-if="loading">
                <td colspan="6" class="text-center py-10">
                  <div class="flex justify-center items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#7A0C23] mr-3"></div>
                    <span class="text-gray-600">Loading buildings...</span>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="!loading && buildings.length === 0">
                <td colspan="6" class="text-center py-10 text-gray-500 bg-white">
                  No buildings found. {{ searchQuery ? 'Try a different search.' : 'Click "Add Building" to create one.' }}
                </td>
              </tr>

              <!-- Buildings Data -->
              <tr
                v-for="building in buildings"
                :key="building.id"
                class="odd:bg-white even:bg-gray-100 hover:bg-gray-300 transition border-b border-yellow-600"
              >
                <td class="px-4 py-3 font-medium truncate" :title="building.building_name">
                  {{ building.building_name }}
                </td>
                <td class="px-4 py-3 text-gray-600 truncate" :title="building.address">
                  {{ building.address }}
                </td>
                <td class="px-4 py-3 text-gray-600 truncate" :title="building.college?.college_name">
                  {{ building.college?.college_name || 'N/A' }}
                </td>
                <td class="px-4 py-3 text-center">
                  {{ building.total_floors || 'N/A' }}
                </td>
                <td class="px-4 py-3 text-center">
                  {{ building.total_rooms || 'N/A' }}
                </td>
                <td class="px-4 py-3">
                  <div class="flex justify-center space-x-2">
                    <IconButton
                      @click="handleView(building)"
                      icon="eye"
                      title="View Details"
                      size="sm"
                      color="blue"
                      class="hover:scale-110"
                    />
                    <IconButton
                      @click="handleEdit(building)"
                      icon="edit"
                      title="Edit Building"
                      size="sm"
                      color="green"
                      class="hover:scale-110"
                    />
                    <IconButton
                      @click="handleDelete(building)"
                      icon="delete"
                      title="Delete Building"
                      size="sm"
                      color="red"
                      class="hover:scale-110"
                    />
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Fixed Pagination Controls -->
        <div v-if="buildings.length > 0" class="bg-gray-50 px-6 py-4 border-t border-yellow-400">
          <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
            <!-- Showing range -->
            <div class="text-sm text-gray-600">
              Showing {{ showingRange.start }} to {{ showingRange.end }} of {{ totalItems }} entries
            </div>

            <!-- Items per page selector -->
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-600">Show:</span>
              <select
                v-model="itemsPerPage"
                @change="handleItemsPerPageChange"
                class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent w-20"
              >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
              </select>
              <span class="text-sm text-gray-600">per page</span>
            </div>

            <!-- Page navigation -->
            <div class="flex items-center space-x-2">
              <button
                @click="prevPage"
                :disabled="currentPage === 1 || loading"
                :class="[
                  'flex items-center px-3 py-1.5 rounded border text-sm font-medium transition-colors duration-150',
                  currentPage === 1 || loading
                    ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                ]"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Previous
              </button>

              <div class="flex items-center space-x-1">
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :disabled="loading || page === '...'"
                  :class="[
                    'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px] transition-colors duration-150',
                    currentPage === page
                      ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                      : page === '...'
                      ? 'bg-white text-gray-400 border-gray-300 cursor-default'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
              </div>

              <button
                @click="nextPage"
                :disabled="currentPage === totalPages || loading"
                :class="[
                  'flex items-center px-3 py-1.5 rounded border text-sm font-medium transition-colors duration-150',
                  currentPage === totalPages || loading
                    ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                ]"
              >
                Next
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>

            <!-- Page info -->
            <div class="text-sm text-gray-600">
              Page {{ currentPage }} of {{ totalPages }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import IconButton from '@/Components/IconButton.vue'

// Props & Emits
const emit = defineEmits(['openModal'])

// State
const buildings = ref([])
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const totalItems = ref(0)
const totalPages = ref(1)

// Computed Properties
const showingRange = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value + 1
  const end = Math.min(currentPage.value * itemsPerPage.value, totalItems.value)
  return { start, end }
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5

  if (totalPages.value <= maxVisible) {
    // Show all pages if total pages <= maxVisible
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i)
    }
  } else {
    // Show first page, last page, and pages around current page
    pages.push(1)

    if (currentPage.value > 3) {
      pages.push('...')
    }

    // Calculate start and end of visible pages
    let start = Math.max(2, currentPage.value - 1)
    let end = Math.min(totalPages.value - 1, currentPage.value + 1)

    // Adjust if near start or end
    if (currentPage.value <= 3) {
      end = 4
    } else if (currentPage.value >= totalPages.value - 2) {
      start = totalPages.value - 3
    }

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }

    if (currentPage.value < totalPages.value - 2) {
      pages.push('...')
    }

    pages.push(totalPages.value)
  }

  return pages
})

// Methods
const fetchBuildings = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/buildings', {
      params: {
        search: searchQuery.value,
        page: currentPage.value,
        per_page: itemsPerPage.value
      }
    })

    if (response.data.success) {
      buildings.value = response.data.data.data || response.data.data
      totalItems.value = response.data.data.total || response.data.data.length
      totalPages.value = response.data.data.last_page ||
                        Math.ceil(totalItems.value / itemsPerPage.value)
    }
  } catch (error) {
    console.error('Error fetching buildings:', error)
    alert('Failed to fetch buildings. Please try again.')
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  currentPage.value = 1
  fetchBuildings()
}

const handleItemsPerPageChange = () => {
  currentPage.value = 1
  fetchBuildings()
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchBuildings()
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchBuildings()
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value && page !== '...') {
    currentPage.value = page
    fetchBuildings()
  }
}

// Modal handlers
const handleAdd = () => emit('openModal', 'add')
const handleEdit = (building) => emit('openModal', 'edit', building)
const handleDelete = (building) => emit('openModal', 'delete', building)
const handleView = (building) => emit('openModal', 'view', building)

// Lifecycle
onMounted(() => {
  fetchBuildings()
})
</script>

<style scoped>
/* Fixed table layout to prevent movement */
table {
  table-layout: fixed;
  width: 100%;
}

/* Ensure proper text handling */
td, th {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Keep pagination stable */
.pagination-container {
  position: sticky;
  bottom: 0;
  background: white;
  z-index: 10;
}
</style>
