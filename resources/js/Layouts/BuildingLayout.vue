<script setup>
// ============================================================
// Imports
// ============================================================
import { ref, computed } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import BuildingTable from '@/Components/BuildingModals/BuildingTable.vue'
import BuildingModal from '@/Components/BuildingModals/BuildingModal.vue'
import Messagefunction from '@/Components/MessageFunction.vue'
import IconButton from '@/Components/IconButton.vue'

// ============================================================
// State - Building Data
// ============================================================
const nextId = ref(6)
const buildings = ref([
  { id: 1, name: 'Main Campus Admin', address: '123 University Ave', parking: true },
  { id: 2, name: 'Science & Tech Annex', address: '456 Innovation Rd', parking: false },
  { id: 3, name: 'Dormitory Delta', address: '789 Residential St', parking: false },
  { id: 4, name: 'Library Hub', address: '202 Central Plaza', parking: true },
  { id: 5, name: 'Art Studio Block', address: '301 Creative Lane', parking: true }
])

// ⭐ PAGINATION STATE
const currentPage = ref(1)
const itemsPerPage = ref(5)

// ⭐ TOAST STATE
const showCreateSuccess = ref(false)
const showEditSuccess = ref(false)
const showDeleteSuccess = ref(false)
const deletedBuildingName = ref('')
const toastTimeout = 2000 // 2 seconds

// ============================================================
// Computed Properties - Pagination
// ============================================================

// Paginated buildings
const paginatedBuildings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return buildings.value.slice(start, end)
})

// Total pages
const totalPages = computed(() => {
  return Math.ceil(buildings.value.length / itemsPerPage.value)
})

// Showing range
const showingRange = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value + 1
  const end = Math.min(currentPage.value * itemsPerPage.value, buildings.value.length)
  const total = buildings.value.length
  return { start, end, total }
})

// ============================================================
// Pagination Functions
// ============================================================

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

const resetPagination = () => {
  currentPage.value = 1
}

// ============================================================
// Sidebar Controls
// ============================================================
const sidebarVisible = ref(true)
const toggleSidebar = () => sidebarVisible.value = !sidebarVisible.value

// ============================================================
// Modal Controls
// ============================================================
const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const handleOpenModal = (type, data = null) => {
  modalType.value = type
  modalData.value = data
  isModalVisible.value = true
}

const handleCloseModal = () => {
  isModalVisible.value = false
  modalType.value = null
  modalData.value = null
}

// ============================================================
// CRUD Functions (with Toast Logic)
// ============================================================

// ➕ Add
const addBuilding = (data) => {
  const newBuilding = {
    id: nextId.value++,
    name: data.name,
    address: data.address,
    parking: data.parking === 'true'
  }
  buildings.value.push(newBuilding)

  showCreateSuccess.value = true
  setTimeout(() => showCreateSuccess.value = false, toastTimeout)

  // Reset pagination to show new building
  resetPagination()
}

// ✏️ Edit
const updateBuilding = (data) => {
  const i = buildings.value.findIndex(b => b.id === data.id)
  if (i !== -1) {
    buildings.value[i] = { ...data, parking: data.parking === 'true' }

    showEditSuccess.value = true
    setTimeout(() => showEditSuccess.value = false, toastTimeout)
  }
}

// 🗑️ Delete (no popup here anymore)
const deleteBuilding = (id) => {
  const buildingToDelete = buildings.value.find(b => b.id === id)

  if (buildingToDelete) {
    deletedBuildingName.value = buildingToDelete.name
    buildings.value = buildings.value.filter(b => b.id !== id)

    showDeleteSuccess.value = true
    setTimeout(() => showDeleteSuccess.value = false, toastTimeout)

    // Reset pagination if needed
    resetPagination()
  }
}

// 🔁 Handle Updates from Modal
const handleDataUpdated = (data, type) => {
  if (type === 'delete') {
    deleteBuilding(data.id)
  } else if (type === 'add') {
    addBuilding(data)
  } else if (type === 'edit') {
    updateBuilding(data)
  }
  handleCloseModal()
}
</script>

<template>
  <div class="bg-gray-200 font-sans min-h-screen">
    <Navbar @toggleSidebar="toggleSidebar" />

    <div class="flex pt-14 min-h-screen">
      <Sidebar v-show="sidebarVisible" class="fixed top-0 left-0 h-full z-20 w-64 lg:relative" />

      <main class="flex-1 p-6">
        <!-- Header Section with left-aligned arrangement -->
        <div class="mb-6">
          <!-- Main Title and Breadcrumb Container -->
          <div class="flex flex-col">
            <!-- Building Management Title -->
            <h1 class="text-xl md:text-2xl font-bold text-[#7A0C23] mb-1">
              Building Management
            </h1>
            <!-- Breadcrumb - Left aligned under the title -->
            <div class="mt-20 absolute right-6 top-2 z-20">
                    <div class="text-sm text-gray-500 whitespace-nowrap ">
                        <span>UPCEBU > BUILDING</span>
                    </div>
                </div>
     </div>
</div>


        <!-- Building Table Component -->
        <BuildingTable
          :buildings="paginatedBuildings"
          @openModal="handleOpenModal"
        />

        <!-- Pagination Controls -->
        <div v-if="buildings.length > 0" class="mt-4 bg-white rounded-lg shadow p-4">
          <div class="flex flex-col md:flex-row items-center justify-between">
            <!-- Showing range -->
            <div class="text-sm text-gray-600 mb-3 md:mb-0">
              Showing {{ showingRange.start }} to {{ showingRange.end }} of {{ showingRange.total }} entries
            </div>

            <!-- Items per page selector -->
            <div class="flex items-center space-x-2 mb-3 md:mb-0">
              <span class="text-sm text-gray-600">Show:</span>
              <select
                v-model="itemsPerPage"
                @change="resetPagination"
                class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
              >
                <option value="3">3</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
              </select>
              <span class="text-sm text-gray-600">per page</span>
            </div>

            <!-- Page navigation -->
            <div class="flex items-center space-x-2">
              <!-- Previous button -->
              <IconButton
                @click="prevPage"
                :disabled="currentPage === 1"
                icon="chevronLeft"
                title="Previous Page"
                size="sm"
                color="gray"
                outlined
                :class="[
                  'px-3 py-1.5 rounded border text-sm font-medium transition-colors duration-150',
                  currentPage === 1
                    ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                ]"
              >
                Previous
              </IconButton>

              <!-- Page numbers -->
              <div class="flex items-center space-x-1">
                <button
                  v-for="page in totalPages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[
                    'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px] transition-colors duration-150',
                    currentPage === page
                      ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                      : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
              </div>

              <!-- Next button -->
              <IconButton
                @click="nextPage"
                :disabled="currentPage === totalPages"
                icon="chevronRight"
                title="Next Page"
                size="sm"
                color="gray"
                outlined
                :class="[
                  'px-3 py-1.5 rounded border text-sm font-medium transition-colors duration-150',
                  currentPage === totalPages
                    ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                ]"
              >
                Next
              </IconButton>
            </div>

            <!-- Page indicator -->
            <div class="text-sm text-gray-600 mt-3 md:mt-0">
              Page {{ currentPage }} of {{ totalPages }}
            </div>
          </div>

          <!-- Results summary -->
          <div class="mt-4 pt-3 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-500">
              Total Buildings: <span class="font-semibold text-[#7A0C23]">{{ buildings.length }}</span>
            </p>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="buildings.length === 0" class="mt-4 bg-white rounded-lg shadow p-8 text-center">
          <div class="flex flex-col items-center justify-center">
            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Buildings Found</h3>
            <p class="text-gray-500 mb-4">Get started by adding your first building.</p>
          </div>
        </div>
      </main>
    </div>

    <BuildingModal
      :isVisible="isModalVisible"
      :type="modalType"
      :building="modalData"
      @close="handleCloseModal"
      @dataUpdated="handleDataUpdated"
    />

    <Messagefunction
      :showCreateSuccess="showCreateSuccess"
      :showEditSuccess="showEditSuccess"
      :showDeleteSuccess="showDeleteSuccess"
      :deletedBuildingName="deletedBuildingName"
    />
  </div>
</template>

<style scoped>
/* Custom styles for pagination */
button:not(:disabled):hover {
  transform: translateY(-1px);
  transition: transform 0.2s ease;
}

/* Ensure pagination controls are properly spaced */
.space-x-1 > * + * {
  margin-left: 0.25rem;
}

.space-x-2 > * + * {
  margin-left: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .flex-col.md\:flex-row {
    gap: 1rem;
  }

  .space-x-2 {
    justify-content: center;
  }
}
</style>
