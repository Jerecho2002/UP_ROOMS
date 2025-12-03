<script setup>
// ============================================================
// Imports
// ============================================================
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import BuildingTable from '@/Components/BuildingModals/BuildingTable.vue'
import BuildingModal from '@/Components/BuildingModals/BuildingModal.vue'
import Messagefunction from '@/Components/Messagefunction.vue'

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

// ⭐ TOAST STATE
const showCreateSuccess = ref(false)
const showEditSuccess = ref(false)
const showDeleteSuccess = ref(false)
const deletedBuildingName = ref('')
const toastTimeout = 2000 // 2 seconds

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
  }
}

// 🔁 Handle Updates from Modal
const handleDataUpdated = (data, type) => {
  if (type === 'delete') {
    // ❌ Removed confirm popup — Messagefunction handles toast instead
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
        <h2 class="text-xl font-bold mb-6 text-[#7A0C23]">Building Management Dashboard</h2>

        <BuildingTable :buildings="buildings" @openModal="handleOpenModal" />
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
/* optional custom styles */
</style>
