<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { ref } from 'vue'

// Import the new components
import DepartmentTable from '@/Components/DepartmentModals/DepartmentTable.vue'
import DepartmentModals from '@/Components/DepartmentModals/DepartmentModals.vue'
import MessageFunction from '@/Components/Messagefunction.vue' // <-- Imported the toast component

// Sidebar toggle state and method
const sidebarVisible = ref(true)
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}

// === TOAST STATES ===
const showCreateSuccess = ref(false)
const showEditSuccess = ref(false)
const showDeleteSuccess = ref(false)
const deletedRoomName = ref("") // Used to hold the name of the deleted department

// Show toast for 3 seconds and set its text
const triggerToast = (type, name = "") => {
    // Reset previous states
    showCreateSuccess.value = false
    showEditSuccess.value = false
    showDeleteSuccess.value = false

    // Set the state for the current toast
    if (type === "create") showCreateSuccess.value = true
    if (type === "edit") showEditSuccess.value = true
    if (type === "delete") {
        deletedRoomName.value = name
        showDeleteSuccess.value = true
    }

    // Hide the toast after 3 seconds
    setTimeout(() => {
        showCreateSuccess.value = false
        showEditSuccess.value = false
        showDeleteSuccess.value = false
        deletedRoomName.value = ""
    }, 3000)
}

// === MAIN APPLICATION DATA STATE ===
// ... (departments and students data are here)

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
// ... (modalsRef and openDepartmentModal are here)
const modalsRef = ref(null);

const openDepartmentModal = (type, data, table) => {
    if (modalsRef.value && modalsRef.value.openModal) {
        modalsRef.value.openModal(type, data, table);
    }
};

// 3. Handlers for modal events (Emitted from DepartmentModals.vue)
const handleUpdateData = (payload) => {
    const { table, data } = payload;
    const sourceRef = table === 'departments' ? departments : students;

    const idKey = table === 'departments' ? 'id' : 'id';

    const index = sourceRef.value.findIndex(item => item[idKey] === data[idKey]);

    if (index !== -1) {
        // Perform the update
        Object.assign(sourceRef.value[index], data);
        console.log(`[Layout Update Success] Data updated for ${table}.`);
        // --- TOAST INTEGRATION: EDIT ---
        triggerToast("edit"); // <-- Triggers the Edit Success Toast
    } else {
        console.error(`Item not found for update in ${table}.`);
    }
};

const handleDeleteData = (payload) => {
    const { table, data } = payload;
    const sourceRef = table === 'departments' ? departments : students;

    const idKey = table === 'departments' ? 'id' : 'id';
    const deletedItem = sourceRef.value.find(item => item[idKey] === data[idKey]);
    const name = deletedItem ? deletedItem.name : "Item"; // Get the name for the toast

    // Filter the data source to remove the item
    sourceRef.value = sourceRef.value.filter(item => item[idKey] !== data[idKey]);

    console.log(`[Layout Delete Success] Item deleted from ${table}.`);
    // --- TOAST INTEGRATION: DELETE ---
    triggerToast("delete", name); // <-- Triggers the Delete Success Toast
};

// 4. Handler for adding a new Department (opens modal)
const handleAddDepartment = () => {
    // Open the modal in 'add' mode with an empty object structure
    openDepartmentModal('add', { name: '', college: '', building: '', head: '' }, 'departments');
};

// 5. Handler for saving the new Department (called when modal emits 'saveNewData')
const handleSaveNewDepartment = (newDepartment) => {
    // Logic to add the new department to the array
    const newId = departments.value.length ? Math.max(...departments.value.map(d => d.id)) + 1 : 1;
    departments.value.push({
        id: newId,
        ...newDepartment
    });
    console.log(`[Layout Add Success] New department added with ID: ${newId}.`);
    // --- TOAST INTEGRATION: CREATE ---
    triggerToast("create"); // <-- Triggers the Create Success Toast
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
                    @addDepartment="handleAddDepartment" />

                <DepartmentModals
                    ref="modalsRef"
                    @updateData="handleUpdateData"
                    @deleteData="handleDeleteData"
                    @saveNewData="handleSaveNewDepartment"
                />
            </main>
        </div>

        <MessageFunction
            :showCreateSuccess="showCreateSuccess"
            :showEditSuccess="showEditSuccess"
            :showDeleteSuccess="showDeleteSuccess"
            :deletedRoomName="deletedRoomName"
        />
    </div>
</template>

<style>
/* Add the necessary toast transition styles to a global style sheet or scoped style if you prefer */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.35s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-15px);
}
</style>
