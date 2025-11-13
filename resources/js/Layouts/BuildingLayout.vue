<script setup>
// ============================================================
// Imports
// ============================================================
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import BuildingTable from '@/Components/BuildingModals/BuildingTable.vue'
import BuildingModal from '@/Components/BuildingModals/BuildingModal.vue'

// ============================================================
// State - Building Data
// ============================================================
const nextId = ref(6)
const buildings = ref([
  { id: 1, name: 'Main Campus Admin', address: '123 University Ave' },
  { id: 2, name: 'Science & Tech Annex', address: '456 Innovation Rd' },
  { id: 3, name: 'Dormitory Delta', address: '789 Residential St' },
  { id: 4, name: 'Library Hub', address: '202 Central Plaza' },
  { id: 5, name: 'Art Studio Block', address: '301 Creative Lane' }
])

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
// CRUD Functions
// ============================================================
// ➕ Add
const addBuilding = (data) => {
  data.id = nextId.value++
  data.parking = data.parking === 'true'
  buildings.value.push(data)
}

// ✏️ Edit
const updateBuilding = (data) => {
  const i = buildings.value.findIndex(b => b.id === data.id)
  if (i !== -1) buildings.value[i] = { ...data, parking: data.parking === 'true' }
}

// 🗑️ Delete
const deleteBuilding = (id) => {
  buildings.value = buildings.value.filter(b => b.id !== id)
}

// 🔁 Handle Updates from Modal
const handleDataUpdated = (data, type) => {
  if (type === 'add') addBuilding(data)
  else if (type === 'edit') updateBuilding(data)
  else if (type === 'delete') deleteBuilding(data.id)
  handleCloseModal()
}
</script>

<template>
  <div class="bg-gray-100 font-sans min-h-screen">
    <!-- 🧭 Navbar -->
    <Navbar @toggleSidebar="toggleSidebar" />

    <div class="flex pt-14 min-h-screen">
      <!-- 📁 Sidebar -->
      <Sidebar v-show="sidebarVisible" class="fixed top-0 left-0 h-full z-20 w-64 lg:relative" />

      <!-- 📊 Main Content -->
      <main class="flex-1 p-6">
        <h2 class="text-xl font-bold mb-6 text-[#7A0C23]">Building Management Dashboard</h2>

        <!-- 🏗️ Building Table -->
        <BuildingTable :buildings="buildings" @openModal="handleOpenModal" />
      </main>
    </div>

    <!-- 🪟 Modal -->
    <BuildingModal
      :isVisible="isModalVisible"
      :type="modalType"
      :building="modalData"
      @close="handleCloseModal"
      @dataUpdated="handleDataUpdated"
    />
  </div>
</template>
