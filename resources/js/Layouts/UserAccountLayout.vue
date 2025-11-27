<script setup>
import { ref } from 'vue'
// Mock components for context, assuming they exist in the actual project
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import UserAccountTable from '@/Components/UserAccountModal/UserAccountTable.vue'
import UserModal from '@/Components/UserAccountModal/UserModal.vue'

// --- Data State (Master Array) ---
const nextId = ref(16);
// Initialized with mock data matching the Laravel schema
const users = ref(
    Array.from({ length: 15 }, (_, i) => ({
        id: i + 1,
        username: `user_${i + 1}`,
        email: `user${i + 1}@example.com`,
        first_name: i % 2 === 0 ? `Alice${i + 1}` : `Bob${i + 1}`,
        last_name: `Smith${i + 1}`,
        role: ['ADMIN', 'Staff', 'Faculty','DPTAPR','AO','ADPD','OCS','SYSADMIN','USER TYPE NAME'][i % 3],
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
    // Assign a new ID (simulating DB insertion)
    newUser.id = nextId.value++;
    // Set a default role if none is provided in the form
    if (!newUser.role) newUser.role = 'Staff'; 
    users.value.push(newUser);
    console.log('User added:', newUser.username);
};

const updateUser = (updatedUser) => {
    const index = users.value.findIndex(u => u.id === updatedUser.id);
    if (index !== -1) {
        // Simple update/replace of the object
        users.value[index] = updatedUser;
        console.log('User updated:', updatedUser.username);
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
    <div class="bg-gray-200 font-sans">
        <!-- Assuming Navbar and Sidebar exist and are correctly imported -->
        <Navbar @toggleSidebar="toggleSidebar" />
        
        <div class="flex pt-10 min-h-screen transition-all duration-300">
            
            <Sidebar v-show="sidebarVisible" class="fixed top-5 left-0 h-full z-20 w-64 lg:relative" />

            <main id="main" class="flex-1 overflow-y-auto p-0 md:p-6 bg-gray-200">
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