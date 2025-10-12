<!-- resources/js/Layouts/MainAccountLayout.vue -->
<script setup>
import { ref } from "vue";
import Navbar from "@/Components/Navbar.vue";

// --- Sidebar toggle state ---
// Reactive boolean controlling sidebar visibility (open/close)
const sidebarOpen = ref(true);

// Function toggles sidebarOpen state on button click or event
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

// --- Props passed from Laravel backend ---
// Receiving data from Laravel controller for display
defineProps({
  totalAccounts: Number,      // Total user accounts count
  totalDepartments: Number,   // Total departments count
  totalColleges: Number,      // Total colleges count
  totalRooms: Number,         // Total rooms count
  buildings: Array,           // List of buildings (formerly rooms renamed for clarity)
});
</script>

<template>
  <!-- Outer container: flex with full screen height -->
  <div class="flex h-screen">

    <!-- Sidebar Section -->
    <div
      id="sidebar"
      :class="[
        'fixed top-14 left-0 w-64 h-full bg-white shadow-md transform transition-transform duration-300 z-40',
        sidebarOpen ? 'translate-x-0' : '-translate-x-64' // Slide in/out animation using transform
      ]"
    >
      <!-- Sidebar menu list -->
      <ul class="flex flex-col space-y-1 p-4">

        <!-- Sidebar Title -->
        <a href="/MainAccount" class="block text-center mb-5">
          <h2 class="text-sky-500 text-lg font-bold">DASHBOARD</h2>
        </a>

        <!-- Sidebar Navigation Links -->
        <!-- Each link navigates to a different dashboard or section -->
        <li><a href="/UserAccountPage" class="block text-black text-center py-2 rounded hover:bg-[#800020]">🕴️ User Account</a></li>
        <li><a href="/building_dashboard" class="block text-black text-center py-2 rounded hover:bg-[#800020]">🏢 Building</a></li>
        <li><a href="/college_dashboard" class="block text-black text-center py-2 rounded hover:bg-[#800020]">🎓 Colleges</a></li>
        <li><a href="/Department" class="block text-black text-center py-2 rounded hover:bg-[#800020]">🏬 Department</a></li>
        <li><a href="/equipment" class="block text-black text-center py-2 rounded hover:bg-[#800020]">⚙️ Equipment</a></li>
        <li><a href="/roomtypes" class="block text-black text-center py-2 rounded hover:bg-[#800020]">🏠 Room Types</a></li>
        <li><a href="/room" class="block text-black text-center py-2 rounded hover:bg-[#800020]">🚪 Room</a></li>
        <li><a href="/schedule" class="block text-black text-center py-2 rounded hover:bg-[#800020]">📅 Schedules</a></li>
        <li><a href="#" class="block text-black text-center py-2 rounded hover:bg-[#800020]">📝 Terms</a></li>
      </ul>
    </div>

    <!-- Main Content Area (Right side) -->
    <div class="flex-1 flex flex-col w-full">

      <!-- Navbar on top, emits event to toggle sidebar -->
      <Navbar @toggleSidebar="toggleSidebar" />

      <!-- Page content -->
      <main
        id="main"
        class="transition-all duration-300 pt-20 px-6"
        :class="sidebarOpen ? 'ml-64' : 'ml-0'"  <!-- Shift content right when sidebar open -->
      >

        <!-- Dashboard Summary Cards Container -->
        <div class="flex flex-wrap gap-6 mb-10">

          <!-- Card: Total Accounts -->
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-cyan-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Accounts</h3>
            </div>
            <div class="bg-white p-3">
              <!-- Display total accounts passed from props -->
              <p class="text-3xl font-bold text-gray-800">{{ totalAccounts }}</p>
            </div>
          </div>

          <!-- Card: Total Departments -->
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-purple-600 text-white p-3">
              <h3 class="text-lg font-semibold">Total Departments</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">{{ totalDepartments }}</p>
            </div>
          </div>

          <!-- Card: Total Colleges -->
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-orange-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Colleges</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">{{ totalColleges }}</p>
            </div>
          </div>

          <!-- Card: Total Rooms -->
          <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
            <div class="bg-red-500 text-white p-3">
              <h3 class="text-lg font-semibold">Total Rooms</h3>
            </div>
            <div class="bg-white p-3">
              <p class="text-3xl font-bold text-gray-800">{{ totalRooms }}</p>
            </div>
          </div>

        </div>

        <!-- Buildings Data Table Container -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-xl">

          <!-- Table for Buildings -->
          <table class="min-w-full text-sm text-center border-collapse">

            <!-- Table Header -->
            <thead class="bg-[#800020] text-white">
              <tr>
                <th class="px-4 py-3 font-semibold text-left">Name</th>
                <th class="px-4 py-3 font-semibold text-left">Address</th>
                <th class="px-4 py-3 font-semibold">Total Space</th>
                <th class="px-4 py-3 font-semibold">Lift</th>
                <th class="px-4 py-3 font-semibold">Parking</th>
                <th class="px-4 py-3 font-semibold">Action</th>
              </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="divide-y divide-gray-200">

              <!-- Loop through buildings prop to create rows -->
              <tr
                v-for="building in buildings"
                :key="building.id"
                class="odd:bg-white even:bg-gray-50 hover:bg-gray-100"
              >
                <!-- Building name -->
                <td class="px-4 py-3 text-left">{{ building.name }}</td>
                <!-- Building address -->
                <td class="px-4 py-3 text-left">{{ building.address }}</td>
                <!-- Total space available -->
                <td class="px-4 py-3">{{ building.total_space }}</td>
                <!-- Lift availability -->
                <td class="px-4 py-3">{{ building.lift }}</td>
                <!-- Parking availability -->
                <td class="px-4 py-3">{{ building.parking }}</td>

                <!-- Action buttons -->
                <td class="px-4 py-3 space-x-2">
                  <!-- View action: placeholder href for future link -->
                  <a href="#" class="text-blue-600 hover:text-blue-800" title="View">👁️</a>
                  <!-- Edit action -->
                  <a href="#" class="text-blue-600 hover:text-blue-800" title="Edit">✏️</a>
                  <!-- Delete action -->
                  <a href="#" class="text-red-600 hover:text-red-800" title="Delete">🗑️</a>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

      </main>
    </div>
  </div>
</template>
