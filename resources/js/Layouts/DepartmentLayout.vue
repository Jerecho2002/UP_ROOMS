<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { ref } from 'vue'

// -------------------------------------------
// Sidebar visibility state and toggle method
// -------------------------------------------

// Reactive boolean to control whether the sidebar is shown or hidden
const sidebarVisible = ref(true)

// Toggles the sidebar visibility state
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}
</script>

<template>
  <!-- MAIN LAYOUT CONTAINER -->
  <div class="flex pt-14 min-h-screen transition-all duration-300">
    
    <!-- SIDEBAR COMPONENT -->
    <!-- Conditionally shown based on `sidebarVisible` -->
     <Sidebar v-show="sidebarVisible" />

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col">

      <!-- TOP NAVBAR -->
      <!-- Emits event handled here to toggle sidebar visibility -->
      <Navbar @toggleSidebar="toggleSidebar" />

      <!-- PAGE CONTENT -->
      <main class="p-6 overflow-y-auto">

        <!-- --------------------------------- -->
        <!-- STATISTIC CARDS SECTION -->
        <!-- --------------------------------- -->
        <!-- Flex container with responsive wrapping and gaps -->
        <div class="flex flex-wrap gap-6 mb-10">

          <!-- Each card shows a category with count (currently 0) -->
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-cyan-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Accounts</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
          </div>

          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-purple-600 text-white p-3">
              <h3 class="text-lg font-semibold">Total Departments</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
          </div>

          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-orange-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Colleges</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
          </div>

          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-red-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Rooms</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">0</p>
            </div>
          </div>

        </div> <!-- End Stats Cards -->

        <!-- --------------------------------- -->
        <!-- USER DETAILS TABLE -->
        <!-- --------------------------------- -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <h2 class="font-bold text-lg mb-4">User Details</h2>

          <!-- Table showing a simple list of users -->
          <table class="w-full text-sm border-collapse">
            <thead>
              <tr class="bg-[#9c1b33] text-white">
                <th class="p-2">User</th>
                <th class="p-2">Email-ID</th>
                <th class="p-2">Phone</th>
                <th class="p-2">Profession</th>
              </tr>
            </thead>

            <tbody>
              <!-- Static user rows -->
              <tr class="text-center">
                <td class="p-2">SystemAdmin</td>
                <td class="p-2">russell@gmail.com</td>
                <td class="p-2">09982077429</td>
                <td class="p-2">Instructor</td>
              </tr>
              <tr class="text-center">
                <td class="p-2">Russell Evan</td>
                <td class="p-2">loquinario@gmail.com</td>
                <td class="p-2">09982077429</td>
                <td class="p-2">User</td>
              </tr>
            </tbody>
          </table>
        </div> <!-- End User Details -->

        <!-- --------------------------------- -->
        <!-- LAST MONTH CREATED USER IDs TABLE -->
        <!-- --------------------------------- -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="font-bold text-lg mb-4">Last Month Created User IDs</h2>

          <!-- Table showing last month created user records -->
          <table class="w-full text-sm border-collapse">
            <thead>
              <tr class="bg-[#9c1b33] text-white">
                <th class="p-2">Email</th>
                <th class="p-2">User-ID</th>
                <th class="p-2">Month</th>
                <th class="p-2">Year-of-start</th>
                <th class="p-2">Year-of-end</th>
              </tr>
            </thead>

            <tbody class="text-center">
              <!-- Static rows for demo -->
              <tr>
                <td class="p-2">russellevanloquinario@gmail.com</td>
                <td class="p-2">1234567</td>
                <td class="p-2">September 09</td>
                <td class="p-2">2025</td>
                <td class="p-2">2028</td>
              </tr>
              <tr>
                <td class="p-2">panfilo@gmail.com</td>
                <td class="p-2">1234567</td>
                <td class="p-2">August 08</td>
                <td class="p-2">2024</td>
                <td class="p-2">2026</td>
              </tr>
              <tr>
                <td class="p-2">joriesolatos@gmail.com</td>
                <td class="p-2">1234567</td>
                <td class="p-2">October 25</td>
                <td class="p-2">2022</td>
                <td class="p-2">2025</td>
              </tr>
            </tbody>
          </table>
        </div> <!-- End Last Month User IDs -->

      </main>
    </div> <!-- End Main Content Area -->
  </div> <!-- End Main Layout Container -->
</template>
