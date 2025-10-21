<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import { ref } from 'vue'

// Sidebar toggle
const sidebarVisible = ref(true)
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}
</script>

<template>
  <div class="flex pt-14 min-h-screen transition-all duration-300">
    <Sidebar v-show="sidebarVisible" />

    <div class="flex-1 flex flex-col">
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="p-6 overflow-y-auto">
        <!-- STATISTIC CARDS -->
        <div class="flex flex-wrap gap-6 mb-10">
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-cyan-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Accounts</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">20</p>
            </div>
          </div>
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-purple-600 text-white p-3">
              <h3 class="text-lg font-semibold">Total Departments</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">5</p>
            </div>
          </div>
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-orange-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Colleges</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">3</p>
            </div>
          </div>
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-red-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Rooms</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">10</p>
            </div>
          </div>
        </div>

        <!-- USER DETAILS TABLE -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
          <h2 class="font-bold text-lg mb-4">User Details</h2>

          <!-- Scrollable container -->
          <div class="overflow-y-auto max-h-64 border rounded">
            <table class="w-full text-sm border-collapse">
              <thead class="bg-[#9c1b33] text-white">
                <tr>
                  <th class="p-2">User</th>
                  <th class="p-2">Email-ID</th>
                  <th class="p-2">Phone</th>
                  <th class="p-2">Profession</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <tr v-for="i in 25" :key="i">
                  <td class="p-2">User {{ i }}</td>
                  <td class="p-2">user{{ i }}@example.com</td>
                  <td class="p-2">09123456{{ i.toString().padStart(2, '0') }}</td>
                  <td class="p-2">{{ i % 2 === 0 ? 'Instructor' : 'Student' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- LAST MONTH CREATED USER IDs -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="font-bold text-lg mb-4">Last Month Created User IDs</h2>

          <!-- Scrollable container -->
          <div class="overflow-y-auto max-h-64 border rounded">
            <table class="w-full text-sm border-collapse">
              <thead class="bg-[#9c1b33] text-white">
                <tr>
                  <th class="p-2">Email</th>
                  <th class="p-2">User-ID</th>
                  <th class="p-2">Month</th>
                  <th class="p-2">Year-of-start</th>
                  <th class="p-2">Year-of-end</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <tr v-for="i in 25" :key="i">
                  <td class="p-2">user{{ i }}@example.com</td>
                  <td class="p-2">UID{{ 1000 + i }}</td>
                  <td class="p-2">October</td>
                  <td class="p-2">{{ 2020 + (i % 5) }}</td>
                  <td class="p-2">{{ 2025 + (i % 3) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>
