<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { ref } from 'vue'

// Import the new components
import DepartmentTable from '@/Components/DepartmentModals/DepartmentTable.vue'
import MessageFunction from '@/Components/Messagefunction.vue'

// Sidebar toggle state and method
const sidebarVisible = ref(true)
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}

// === TOAST STATES ===
const showCreateSuccess = ref(false)
const showEditSuccess = ref(false)
const showDeleteSuccess = ref(false)
const deletedDeptName = ref("") // Changed from deletedRoomName to deletedDeptName

// === MAIN APPLICATION DATA STATE ===
const departments = ref([
    { id: 1, college: 'College of Engineering', department: 'Computer Engineering', dean: 'Dr. Emily Carter' },
    { id: 2, college: 'College of Arts and Sciences', department: 'Psychology', dean: 'Prof. David Lee' },
    { id: 3, college: 'College of Business', department: 'Accountancy', dean: 'Dr. Samantha Wells' },
    { id: 4, college: 'College of Engineering', department: 'Civil Engineering', dean: 'Dr. Emily Carter' },
    { id: 5, college: 'College of Arts and Sciences', department: 'Biology', dean: 'Prof. David Lee' },
    { id: 6, college: 'College of Information Technology', department: 'BS Information Technology', dean: 'Dr. Michael Chen' },
    { id: 7, college: 'College of Medicine', department: 'Doctor of Medicine', dean: 'Dr. Sarah Johnson' },
    { id: 8, college: 'College of Law', department: 'Juris Doctor', dean: 'Prof. Robert Wilson' },
    { id: 9, college: 'College of Education', department: 'BS Elementary Education', dean: 'Dr. Lisa Brown' },
    { id: 10, college: 'College of Nursing', department: 'BS Nursing', dean: 'Dr. Maria Garcia' },
]);

// === TOAST TRIGGER FUNCTIONS ===
const triggerToast = (type, name = "") => {
    // Reset all toast states first
    showCreateSuccess.value = false
    showEditSuccess.value = false
    showDeleteSuccess.value = false

    // Set the appropriate toast state
    if (type === "create") {
        showCreateSuccess.value = true
    } else if (type === "edit") {
        showEditSuccess.value = true
    } else if (type === "delete") {
        deletedDeptName.value = name
        showDeleteSuccess.value = true
    }

    // Auto-hide the toast after 3 seconds
    setTimeout(() => {
        showCreateSuccess.value = false
        showEditSuccess.value = false
        showDeleteSuccess.value = false
        deletedDeptName.value = ""
    }, 3000)
}

// === CRUD HANDLERS ===
const handleCreateDepartment = (newDept) => {
    const newId = Math.max(...departments.value.map(d => d.id), 0) + 1;
    departments.value.push({
        id: newId,
        ...newDept
    });

    // Trigger create toast
    triggerToast("create");
}

const handleEditDepartment = (updatedDept) => {
    const index = departments.value.findIndex(d => d.id === updatedDept.id);
    if (index !== -1) {
        departments.value[index] = { ...updatedDept };

        // Trigger edit toast
        triggerToast("edit");
    }
}

const handleDeleteDepartment = (deptId) => {
    const deptToDelete = departments.value.find(d => d.id === deptId);
    if (deptToDelete && confirm(`Are you sure you want to delete: ${deptToDelete.department}?`)) {
        const deptName = deptToDelete.department;
        departments.value = departments.value.filter(d => d.id !== deptId);

        // Trigger delete toast with department name
        triggerToast("delete", deptName);
    }
}

// === EVENT HANDLERS FOR DEPARTMENTTABLE ===
const handleDepartmentCreated = () => {
    triggerToast("create");
}

const handleDepartmentEdited = () => {
    triggerToast("edit");
}

const handleDepartmentDeleted = (deptName) => {
    triggerToast("delete", deptName);
}
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

                <!-- Department Table Component -->
                <DepartmentTable
                    :departments="departments"
                    @created="handleDepartmentCreated"
                    @edited="handleDepartmentEdited"
                    @deleted="handleDepartmentDeleted"
                />
            </main>
        </div>

        <!-- MessageFunction Component -->
        <MessageFunction
            :showCreateSuccess="showCreateSuccess"
            :showEditSuccess="showEditSuccess"
            :showDeleteSuccess="showDeleteSuccess"
            :deletedRoomName="deletedDeptName"
        />
    </div>
</template>

<style>
/* Add the necessary toast transition styles */
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
