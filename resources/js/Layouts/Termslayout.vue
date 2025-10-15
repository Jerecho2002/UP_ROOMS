<script setup>
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// Import the table components from the new structure
import EmployeeTable from '@/Components/TermsTable/EmployeeTable.vue';
import StudentTable from '@/Components/TermsTable/StudentTable.vue';

// State for sidebar visibility
const sidebarOpen = ref(true)

// Function to toggle the sidebar's state
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)

// --- Tab Switching Logic (Moved from Termstable.vue) ---
// State to track the currently active tab: 'employee' or 'student'.
// Set to 'student' to match the image display on first load.
const activeTab = ref('student');

const setActiveTab = (tab) => {
  activeTab.value = tab;
};
// --------------------------------------------------------
</script>

<template>
  <div class="flex pt-14 min-h-screen transition-all duration-300">
    
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex flex-col flex-1 overflow-hidden">
      
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="flex-1 overflow-y-auto p-0 md:p-6 bg-gray-100">
        <div class="bg-white rounded-lg shadow-md p-0">
          
          <div class="p-6">
            <div class="mb-6">
              <h2 class="text-xl font-semibold text-gray-800">Terms</h2>
            </div>

            <div class="flex items-center bg-gray-100 rounded-full w-full md:w-fit p-1 mb-6">
              <button 
                @click="setActiveTab('employee')"
                :class="[
                  'flex-1 font-medium py-2 px-6 rounded-full transition text-sm md:text-base',
                  activeTab === 'employee' ? 'bg-[#850038] text-white' : 'text-black hover:bg-gray-200'
                ]"
              >
                Employee
              </button>
              <button 
                @click="setActiveTab('student')"
                :class="[
                  'flex-1 font-medium py-2 px-6 rounded-full transition text-sm md:text-base',
                  activeTab === 'student' ? 'bg-[#850038] text-white' : 'text-black hover:bg-gray-200'
                ]"
              >
                Students
              </button>
            </div>

            <Transition name="fade" mode="out-in">
              <component :is="activeTab === 'employee' ? EmployeeTable : StudentTable" :key="activeTab" />
            </Transition>
          </div>
          </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Optional: Adjustments for mobile view and to refine the look */
main {
  /* Padding adjustment for a full-width look on the card within the layout */
  padding: 0;
}

@media (min-width: 768px) {
  /* Re-apply padding on desktop to allow the card to 'float' with margin/padding */
  main {
    padding: 1.5rem; /* p-6 equivalent */
  }
}

/* Simple fade transition for a smoother tab switch (from Termstable.vue) */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>