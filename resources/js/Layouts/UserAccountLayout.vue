<script setup>
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
// Corrected import: UserAccountTable
import UserAccountTable from '@/Components/UserAccountModal/UserAccountTable.vue'
import UserModal from '@/Components/UserAccountModal/UserModal.vue'

// --- Data State (Master Array) ---
// Initialize with mock data and manage it here
const nextId = ref(16);
const users = ref(
    Array.from({ length: 15 }, (_, i) => ({
        id: i + 1,
        name: `Student Name ${i + 1}`,
        school: `School ${i % 3 + 1}`,
        age: 18 + (i % 5),
        address: `Address St. ${i + 1}`,
        room: `R${100 + i}`,
        start: `2024-01-01`,
        end: `2024-12-31`,
    }))
);


// --- Layout State ---
const sidebarVisible = ref(true)
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}

// --- Modal State ---
const isModalVisible = ref(false)
const modalType = ref(null) // 'add', 'view', 'edit', 'delete'
const modalData = ref(null) // The user object to be viewed/edited/deleted

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
            addUser(data);
            break;
        case 'edit':
            updateUser(data);
            break;
        case 'delete':
            deleteUser(data.id);
            break;
    }
    handleCloseModal();
}

const addUser = (newUser) => {
    // Assign a new ID
    newUser.id = nextId.value++;
    users.value.push(newUser);
    console.log('User added:', newUser.name);
};

const updateUser = (updatedUser) => {
    const index = users.value.findIndex(u => u.id === updatedUser.id);
    if (index !== -1) {
        users.value[index] = updatedUser;
        console.log('User updated:', updatedUser.name);
    }
};

const deleteUser = (userId) => {
    const initialLength = users.value.length;
    users.value = users.value.filter(u => u.id !== userId);
    if (users.value.length < initialLength) {
        console.log(`User with ID ${userId} deleted.`);
    }
};

</script>

<template>
    <div class="bg-gray-100 font-sans">
        <Navbar @toggleSidebar="toggleSidebar" />
        
        <div class="flex pt-10 min-h-screen transition-all duration-300">
            
            <Sidebar v-show="sidebarVisible" class="fixed top-14 left-0 h-full z-20 w-64 lg:relative" />

            <main id="main" class="flex-1 transition-all">
                <!-- Pass the users array down as a prop -->
                <UserAccountTable :users="users" @openModal="handleOpenModal" />
            </main>
        </div>

        <UserModal
            :isVisible="isModalVisible"
            :type="modalType"
            :user="modalData"
            @close="handleCloseModal"
            @dataUpdated="handleDataUpdated"
        />
    </div>
</template>
