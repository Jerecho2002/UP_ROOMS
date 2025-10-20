<script setup>
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import CalendarComponent from '@/Components/ScheduleModal/CalendarComponent.vue'; // <-- New Component Import

// State for sidebar visibility
const sidebarOpen = ref(true)

// Function to toggle the sidebar's state
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)

// NOTE: The tab switching logic (activeTab, setActiveTab) was REMOVED
// because this layout is now dedicated to the Schedule/Calendar view.
</script>

<template>
  <div class="flex pt-14 min-h-screen transition-all duration-300">
    
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex flex-col flex-1 overflow-hidden">
      
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="flex-1 overflow-y-auto p-0 md:p-6 bg-gray-100">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Room Schedules Calendar</h2>
          </div>
          
          <CalendarComponent />
          
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Scoped styles from your original layout */
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
</style>