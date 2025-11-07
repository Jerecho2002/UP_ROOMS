<script setup>
import { ref } from 'vue'
// Assuming you have Navbar and Sidebar components similar to the user account system
import Navbar from '@/Components/Navbar.vue' 
import Sidebar from '@/Components/Sidebar.vue'
import BuildingTable from '@/Components/BuildingModals/BuildingTable.vue'
import BuildingModal from '@/Components/BuildingModals/BuildingModal.vue' // <-- PATH FIXED

// --- Data State (Master Array) ---
const nextId = ref(6);
const buildings = ref([
    {
        id: 1,
        name: 'Main Campus Admin',
        address: '123 University Ave, City Center',
        total_space: '5,000 sq ft',
        lift: 'Yes (2)',
        parking: true,
    },
    {
        id: 2,
        name: 'Science & Tech Annex',
        address: '456 Innovation Rd, North Side',
        total_space: '8,200 sq ft',
        lift: 'Yes (4)',
        parking: true,
    },
    {
        id: 3,
        name: 'Dormitory Delta',
        address: '789 Residential St, East Wing',
        total_space: '12,000 sq ft',
        lift: 'No',
        parking: false,
    },
    {
        id: 4,
        name: 'Library Hub',
        address: '202 Central Plaza, Downtown',
        total_space: '3,500 sq ft',
        lift: 'Yes (1)',
        parking: true,
    },
    {
        id: 5,
        name: 'Art Studio Block',
        address: '301 Creative Lane, West End',
        total_space: '4,100 sq ft',
        lift: 'No',
        parking: false,
    },
]);


// --- Layout State (Sidebar Logic) ---
const sidebarVisible = ref(true)
const toggleSidebar = () => {
    // Toggles the visibility state of the sidebar
    sidebarVisible.value = !sidebarVisible.value
}

// --- Modal State ---
const isModalVisible = ref(false)
const modalType = ref(null) // 'add', 'view', 'edit', 'delete'
const modalData = ref(null) // The building object to be viewed/edited/deleted

const handleOpenModal = (type, data = null) => {
    modalType.value = type
    modalData.value = data
    isModalVisible.value = true
}

const handleCloseModal = () => {
    isModalVisible.value = false
    modalData.value = null
    modalType.value = null
}

// --- CRUD Operations ---

const handleDataUpdated = (data, type) => {
    switch (type) {
        case 'add':
            addBuilding(data);
            break;
        case 'edit':
            updateBuilding(data);
            break;
        case 'delete':
            deleteBuilding(data.id);
            break;
    }
    handleCloseModal();
}

const addBuilding = (newBuilding) => {
    // Assign a new ID and ensure parking is a boolean
    newBuilding.id = nextId.value++;
    newBuilding.parking = newBuilding.parking === 'true'; 
    buildings.value.push(newBuilding);
    console.log('Building added:', newBuilding.name);
};

const updateBuilding = (updatedBuilding) => {
    const index = buildings.value.findIndex(b => b.id === updatedBuilding.id);
    if (index !== -1) {
        // Ensure parking is converted to a boolean if coming from form
        updatedBuilding.parking = updatedBuilding.parking === 'true' || updatedBuilding.parking === true;
        buildings.value[index] = updatedBuilding;
        console.log('Building updated:', updatedBuilding.name);
    }
};

const deleteBuilding = (buildingId) => {
    buildings.value = buildings.value.filter(b => b.id !== buildingId);
    console.log(`Building with ID ${buildingId} deleted.`);
};

</script>

<template>
    <div class="bg-gray-100 font-sans min-h-screen">
        <Navbar @toggleSidebar="toggleSidebar" />
        
        <div class="flex pt-14 min-h-screen transition-all duration-300">
            
            <Sidebar 
                v-show="sidebarVisible" 
                class="fixed top-14 left-0 h-full z-20 w-64 lg:relative" 
            />

            <main id="main" class="flex-1 transition-all p-6">
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
    </div>
</template>