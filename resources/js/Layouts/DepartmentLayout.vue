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

// === MAIN APPLICATION DATA STATE ===

// 1. Departments Data (Main Table Focus)
const departments = ref([
    { id: 1, name: 'BS Information Technology', college: 'College of Information Technology', building: 'IT Building', head: 'Dr. Smith' },
    { id: 2, name: 'BS Civil Engineering', college: 'College of Engineering', building: 'Eng Building', head: 'Engr. Jones' },
    { id: 3, name: 'BS Psychology', college: 'College of Arts and Sciences', building: 'Arts Building', head: 'Prof. Garcia' },
    { id: 4, name: 'BS Business Administration', college: 'College of Business', building: 'Business Building', head: 'Dean Lee' },
]);

// 2. Students Data (Kept for the second table example/modals)
const students = ref(
    Array.from({ length: 25 }, (_, i) => ({
        id: i + 1,
        name: `Student ${i + 1}`,
        email: `student${i + 1}@example.com`,
        phone: `09123456${(i + 1).toString().padStart(2, '0')}`,
        profession: (i + 1) % 2 === 0 ? 'Instructor' : 'Student',
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
    const sourceRef = table === 'departments' ? departments : students;
    
    // Determine the ID key based on the table
    const idKey = table === 'departments' ? 'id' : 'id'; 
    
    const index = sourceRef.value.findIndex(item => item[idKey] === data[idKey]);

    if (index !== -1) {
        // Perform the update
        Object.assign(sourceRef.value[index], data);
        console.log(`[Layout Update Success] Data updated for ${table}.`);
    } else {
        console.error(`Item not found for update in ${table}.`);
    }
};

const handleDeleteData = (payload) => {
    const { table, data } = payload;
    const sourceRef = table === 'departments' ? departments : students;

    // Determine the ID key based on the table
    const idKey = table === 'departments' ? 'id' : 'id'; 

    // Filter the data source to remove the item
    sourceRef.value = sourceRef.value.filter(item => item[idKey] !== data[idKey]);
    
    console.log(`[Layout Delete Success] Item deleted from ${table}.`);
};

// 4. Handler for adding a new Department
const handleAddDepartment = () => {
    // Open the modal in 'add' mode with an empty object structure
    openDepartmentModal('add', { name: '', college: '', building: '', head: '' }, 'departments');
};

const handleSaveNewDepartment = (newDepartment) => {
    // Logic to add the new department to the array
    const newId = departments.value.length ? Math.max(...departments.value.map(d => d.id)) + 1 : 1;
    departments.value.push({
        id: newId,
        ...newDepartment
    });
    console.log(`[Layout Add Success] New department added with ID: ${newId}.`);
};

</script>


<template>
    <div class="flex flex-col h-screen bg-gray-200">

        <Navbar @toggleSidebar="toggleSidebar" />
        
        <div :class="[
            'flex flex-1 h-full overflow-hidden relative mt-14', 
            sidebarVisible ? 'lg:grid lg:grid-cols-[256px_1fr]' : 'flex'
        ]">
            
            <Sidebar :sidebarOpen="sidebarVisible" @toggleSidebar="toggleSidebar" :class="[
                'lg:relative lg:translate-x-0 lg:h-full', 
                sidebarVisible ? 'lg:block' : 'lg:hidden'
            ]" />

            <main :class="[
                'flex-1 p-6 overflow-y-auto transition-all duration-300',
                sidebarVisible ? '' : '' 
            ]">
                <h1 class="text-xl font-extrabold text-[#7A0C23] mt-3 mb-5">Department Dashboard</h1>

                
                

                <DepartmentTable 
                    :departments="departments"
                    :students="students"
                    :openModal="openDepartmentModal"
                />
                
                <DepartmentModals 
                    ref="modalsRef" 
                    @updateData="handleUpdateData"
                    @deleteData="handleDeleteData"
                    @saveNewData="handleSaveNewDepartment" 
                />
            </main>
        </div>
    </div>
</template>