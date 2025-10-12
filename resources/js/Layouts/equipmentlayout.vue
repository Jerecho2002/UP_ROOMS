<script setup>
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

// ------------------------------------
// Sidebar toggle state and method
// ------------------------------------

// Reactive state controlling if sidebar is open or closed
const sidebarOpen = ref(true)

// Toggles sidebar open/close state when called
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

// ------------------------------------
// Chart initialization after component mounts
// ------------------------------------
onMounted(() => {
  // Initialize Pie Chart for Equipment Usage
  new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
      labels: ['Student 100%', 'Room 80%', 'Building 40%'], // Pie slice labels
      datasets: [{
        data: [100, 80, 40], // Corresponding values
        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'] // Colors for each slice
      }]
    }
  })

  // Initialize Line Chart for Equipment Usage Over Time
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], // X-axis labels for months
      datasets: [{
        label: 'Usage', // Dataset label
        data: [20,40,35,50,70,60,65,55,45,50,40,35], // Usage data points
        borderColor: '#800000', // Line color
        backgroundColor: 'rgba(128,0,0,0.2)', // Fill color below the line
        fill: true,
        tension: 0.4 // Curved line smoothing
      }]
    }
  })
})
</script>

<template>
  <!-- Main Layout Container -->
  <div class="flex pt-14 min-h-screen transition-all duration-300">

    <!-- Sidebar component with open/close state passed as prop -->
    <Sidebar :sidebarOpen="sidebarOpen" />

    <!-- Main content area: vertical flex container -->
    <div class="flex-1 flex flex-col min-h-screen">

      <!-- Navbar component emits event to toggle sidebar -->
      <Navbar @toggleSidebar="toggleSidebar" />

      <!-- Page content area -->
      <main class="flex-1 bg-gray-100 p-6 transition-all duration-300">

        <!-- Equipment Availability Cards -->
        <div class="col-span-12 bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-bold mb-4">Equipment Availability</h2>

          <!-- Flex container for cards -->
          <div class="flex flex-wrap gap-6 mb-10">

            <!-- Single card showing "Student" equipment count -->
            <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-cyan-500 text-white p-3">
                <h3 class="text-lg font-semibold">Student</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">0</p>
              </div>
            </div>

            <!-- Card showing current Calendar date -->
            <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-purple-600 text-white p-3">
                <h3 class="text-lg font-semibold">Calendar</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">Oct 5, 2025</p>
              </div>
            </div>

            <!-- Card showing current Time -->
            <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-orange-500 text-white p-3">
                <h3 class="text-lg font-semibold">Time</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">10:50 PM</p>
              </div>
            </div>

            <!-- Card showing number of Checked equipment -->
            <div class="flex-1 min-w-[200px] rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-red-500 text-white p-3">
                <h3 class="text-lg font-semibold">Checked</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">0</p>
              </div>
            </div>

          </div>
        </div>

        <!-- Table of Users Using Equipment -->
        <div class="col-span-12 bg-white shadow rounded-lg p-6 overflow-x-auto">
          <h2 class="text-lg font-bold mb-4">User Using Equipment</h2>

          <table class="table-auto w-full border-collapse border">
            <!-- Table Head with maroon background -->
            <thead class="bg-maroon text-white">
              <tr>
                <th class="px-4 py-2 border">No.</th>
                <th class="px-4 py-2 border">ID #</th>
                <th class="px-4 py-2 border">User</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Burrow</th>
                <th class="px-4 py-2 border">Information</th>
              </tr>
            </thead>

            <!-- Table Body with sample user data rows -->
            <tbody>
              <!-- Row 1: Completed status with green checkmark -->
              <tr>
                <td class="border px-4 py-2">1</td>
                <td class="border px-4 py-2">1234</td>
                <td class="border px-4 py-2">Russell Evan Loquinario</td>
                <td class="border px-4 py-2 text-green-600">✔ Completed</td>
                <td class="border px-4 py-2">Chair</td>
                <td class="border px-4 py-2">
                  <!-- Button to view detailed info -->
                  <button class="bg-blue-500 text-white px-3 py-1 rounded">Details</button>
                </td>
              </tr>

              <!-- Row 2: Cancelled status with red cross -->
              <tr>
                <td class="border px-4 py-2">2</td>
                <td class="border px-4 py-2">1234</td>
                <td class="border px-4 py-2">Russell Evan Loquinario</td>
                <td class="border px-4 py-2 text-red-600">✘ Cancel</td>
                <td class="border px-4 py-2">Computer</td>
                <td class="border px-4 py-2">
                  <button class="bg-blue-500 text-white px-3 py-1 rounded">Details</button>
                </td>
              </tr>

              <!-- Row 3: Completed status -->
              <tr>
                <td class="border px-4 py-2">3</td>
                <td class="border px-4 py-2">1234</td>
                <td class="border px-4 py-2">Russell Evan Loquinario</td>
                <td class="border px-4 py-2 text-green-600">✔ Completed</td>
                <td class="border px-4 py-2">Laptop</td>
                <td class="border px-4 py-2">
                  <button class="bg-blue-500 text-white px-3 py-1 rounded">Details</button>
                </td>
              </tr>

              <!-- Row 4: Pending status with yellow hourglass -->
              <tr>
                <td class="border px-4 py-2">4</td>
                <td class="border px-4 py-2">1234</td>
                <td class="border px-4 py-2">Russell Evan Loquinario</td>
                <td class="border px-4 py-2 text-yellow-600">⌛ Pending</td>
                <td class="border px-4 py-2">White Board</td>
                <td class="border px-4 py-2">
                  <button class="bg-blue-500 text-white px-3 py-1 rounded">Details</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Spacer div for layout balance (can be removed if not needed) -->
        <div class="flex-1 bg-gray-100 p-6 transition-all duration-300"></div>

        <!-- Grid layout for population, student ratio, and charts -->
        <div class="grid grid-cols-12 gap-6">

          <!-- Population Card -->
          <div class="col-span-4 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold mb-2">Population</h2>
            <p>Student: 100,000</p>
            <p>Room: 50,000</p>
            <p>Building: 14,000</p>
          </div>

          <!-- Student Ratio Card -->
          <div class="col-span-4 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold mb-2">Student</h2>
            <p>Student: 100%</p>
            <p>Room: 80%</p>
            <p>Building: 40%</p>
          </div>

          <!-- Pie Chart Card -->
          <div class="col-span-4 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold mb-2">Graph</h2>
            <!-- Canvas element for pie chart -->
            <canvas id="pieChart"></canvas>
          </div>

          <!-- Line Chart Card (Equipment Usage Over Time) -->
          <div class="col-span-12 bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold mb-4">Equipments Percentage Use</h2>
            <!-- Canvas element for line chart -->
            <canvas id="lineChart"></canvas>
          </div>

        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Custom maroon background for table header */
.bg-maroon {
  background-color: #800000;
}
</style>
