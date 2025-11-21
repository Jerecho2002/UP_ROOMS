<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { ref } from 'vue'

// Import the new components
import DepartmentTable from '@/Components/DepartmentModals/DepartmentTable.vue'
import DepartmentModals from '@/Components/DepartmentModals/DepartmentModals.vue'

// Sidebar toggle state and method
const sidebarVisible = ref(true)
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}

// === DATA MOCKUP (Moved from DepartmentTable.vue to the parent) ===
const users = ref(
    Array.from({ length: 25 }, (_, i) => ({
        id: i + 1,
        name: `User ${i + 1}`,
        email: `user${i + 1}@example.com`,
        phone: `09123456${(i + 1).toString().padStart(2, '0')}`,
        profession: (i + 1) % 2 === 0 ? 'Instructor' : 'Student',
    }))
);

const lastMonthUsers = ref(
    Array.from({ length: 25 }, (_, i) => ({
        email: `user${i + 1}@example.com`,
        userId: `UID${1000 + i + 1}`,
        month: 'October',
        yearStart: 2020 + ((i + 1) % 5),
        yearEnd: 2025 + ((i + 1) % 3),
    }))
);

// === MODAL COORDINATION LOGIC ===

// 1. Template ref for DepartmentModals component
const modalsRef = ref(null);

// 2. Function passed to DepartmentTable.vue to open the modal
const openDepartmentModal = (type, data, table) => {
    if (modalsRef.value && modalsRef.value.openModal) {
        modalsRef.value.openModal(type, data, table);
    }
};

// 3. Handlers for modal events (Emitted from DepartmentModals.vue)
const handleUpdateData = (payload) => {
    const { table, data } = payload;
    const source = table === 'users' ? users.value : lastMonthUsers.value;
    
    let index;
    if (table === 'users') {
        index = source.findIndex(item => item.id === data.id);
    } else { // lastMonthUsers
        index = source.findIndex(item => item.userId === data.userId);
    }

    if (index !== -1) {
        // Perform the update
        Object.assign(source[index], data);
        console.log(`[Layout Update Success] Data updated for ${table}.`);
    } else {
        console.error(`Item not found for update in ${table}.`);
    }
};

const handleDeleteData = (payload) => {
    const { table, data } = payload;
    const sourceRef = table === 'users' ? users : lastMonthUsers;

    // Filter the data source to remove the item
    if (table === 'users') {
        sourceRef.value = sourceRef.value.filter(item => item.id !== data.id);
    } else { // lastMonthUsers
        sourceRef.value = sourceRef.value.filter(item => item.userId !== data.userId);
    }
    
    console.log(`[Layout Delete Success] Item deleted from ${table}.`);
};

</script>


<template>
    <div class="flex flex-col h-screen bg-gray-200">

        <Navbar @toggleSidebar="toggleSidebar" />
        
        <div :class="[
            'flex flex-1 h-full overflow-hidden relative mt-14', 
            sidebarVisible ? 'lg:grid lg:grid-cols-[256px_1fr]' : 'flex' // 256px = w-64
        ]">
            
            <Sidebar :sidebarOpen="sidebarVisible" @toggleSidebar="toggleSidebar" :class="[
                'lg:relative lg:translate-x-0 lg:h-full', 
                sidebarVisible ? 'lg:block' : 'lg:hidden'
            ]" />

            <main :class="[
                'flex-1 p-6 overflow-y-auto transition-all duration-300',
                // Removed ml-64/lg:ml-64. The grid now handles the spacing automatically,
                // making the transition seamless and gap-free on desktop.
                sidebarVisible ? '' : '' 
            ]">
                <h1 class="text-3xl font-extrabold text-[#7A0C23] mt-3 mb-5">Department Dashboard</h1>

               

                <DepartmentTable 
                    :users="users"
                    :lastMonthUsers="lastMonthUsers"
                    :openModal="openDepartmentModal"
                />
                
                <DepartmentModals 
                    ref="modalsRef" 
                    @updateData="handleUpdateData"
                    @deleteData="handleDeleteData"
                />
            </main>
        </div>
    </div>
</template>