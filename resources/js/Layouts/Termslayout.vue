<script setup>
import { ref } from 'vue'
// Assuming these components are in the correct paths:
import Termstable from '@/Components/Termstable.vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// State for sidebar visibility
const sidebarOpen = ref(true)

// Function to toggle the sidebar's state
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)
</script>

<template>



  <div class="flex pt-14 min-h-screen transition-all duration-300">
    
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex flex-col flex-1 overflow-hidden">
      
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="flex-1 overflow-y-auto p-0 md:p-6 bg-gray-100">
        <div class="bg-white rounded-lg shadow-md p-0">
           <Termstable />
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
</style>