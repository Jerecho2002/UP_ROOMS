<script setup>
import { ref } from 'vue'
import Termstable from '@/Components/Termstable.vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

const sidebarOpen = ref(true)
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)
</script>

<template>
  <div class="flex h-screen bg-gray-100 overflow-hidden">
    <!-- SIDEBAR -->
    <Sidebar :sidebarOpen="sidebarOpen" />

    <!-- MAIN CONTENT -->
    <div class="flex flex-col flex-1">
      <!-- NAVBAR -->
      <Navbar @toggleSidebar="toggleSidebar" />

      <!-- MAIN PAGE CONTENT -->
      <main class="p-6 bg-white flex-1 overflow-y-auto">
        <Termstable />
      </main>
    </div>
  </div>
</template>
