<script setup>
// -------------------------------------------
// Import components and dependencies
// -------------------------------------------
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

// -------------------------------------------
// Sidebar: Controls sidebar open/close toggle
// -------------------------------------------
const sidebarOpen = ref(true)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

// -------------------------------------------
// Chart rendering after the component mounts
// -------------------------------------------
onMounted(() => {
  // PIE CHART - Equipment usage percentage
  new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
      labels: ['Student 100%', 'Room 80%', 'Building 40%'],
      datasets: [{
        data: [100, 80, 40],
        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'],
        spacing: 0,
      }]
    },
    options: {
      plugins: {
        legend: { display: false },
      }
    }
  })

  // LINE CHART - Equipment usage trend over the year
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: 'Usage',
        data: [20,40,35,50,70,60,65,55,45,50,40,35],
        borderColor: '#800000',
        backgroundColor: 'rgba(128,0,0,0.2)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      aspectRatio: 3,
      plugins: {
        legend: { display: false },
      }
    }
  })
})
</script>

<template>
  <div class="flex pt-14 h-screen transition-all duration-300">

    <!-- Sidebar Navigation -->
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex-1 flex flex-col h-full overflow-hidden">

      <!-- Top Navbar -->
      <Navbar @toggleSidebar="toggleSidebar" />

      <!-- Main Content Area -->
      <main class="flex-1 bg-gray-100 p-3 h-full overflow-y-auto transition-all duration-300">

        <div class="grid grid-cols-12 gap-3 mb-3">

          <!-- LEFT PANEL (Population, Stats, Pie Chart) -->
          <div class="col-span-12 md:col-span-3 space-y-3">

            <!-- Total Population Card -->
            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-sm font-bold mb-1">Population</h2>
              <p class="text-xs">Student: 100,000</p>
              <p class="text-xs">Room: 50,000</p>
              <p class="text-xs">Building: 14,000</p>
            </div>

            <!-- Usage Summary Card -->
            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-sm font-bold mb-1">Student</h2>
              <p class="text-xs">Student: 100%</p>
              <p class="text-xs">Room: 80%</p>
              <p class="text-xs">Building: 40%</p>
            </div>

            <!-- Pie Chart (Usage %) -->
            <div class="bg-white shadow rounded-lg p-3 h-auto">
              <h2 class="text-sm font-bold mb-1">Graph</h2>
              <canvas id="pieChart"></canvas>
              <div class="mt-2 text-xs">
                <p class="text-green-600">■ Student: 100%</p>
                <p class="text-orange-500">■ Room: 80%</p>
                <p class="text-blue-500">■ Building: 40%</p>
              </div>
            </div>
          </div>

          <!-- RIGHT PANEL (Charts, Filters, Table) -->
          <div class="col-span-12 md:col-span-9 space-y-3">

            <!-- Top Header with Filters -->
            <div class="bg-white shadow rounded-lg p-3">
              <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold">Equipment Availability</h2>

                <!-- Filter Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                  <!-- Student Type Filter -->
                  <div class="flex items-center rounded-lg bg-purple-600 p-2 text-white">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zM8 11a2 2 0 100-4 2 2 0 000 4zM12 11a2 2 0 100-4 2 2 0 000 4zM14 13a6 6 0 00-12 0v2h12v-2z"></path></svg>
                    <span class="font-semibold text-sm">Student</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>

                  <!-- Date Filter -->
                  <div class="flex items-center rounded-lg bg-orange-500 p-2 text-white">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 2h8v12H6V4zm2 2h4v2H8V6zm0 4h4v2H8v-2zm0 4h4v2H8v-2z"></path></svg>
                    <span class="font-semibold text-sm">Oct 5, 2025</span>
                  </div>

                  <!-- Time Filter -->
                  <div class="flex items-center rounded-lg bg-red-500 p-2 text-white">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8V7h2v3h-2zm0 2v2h2v-2h-2z"></path></svg>
                    <span class="font-semibold text-sm">10:50 PM</span>
                  </div>

                  <!-- Check Status Button -->
                  <button class="rounded-lg bg-cyan-400 font-bold p-2 text-black text-sm">Checked</button>

                  <!-- Search Button -->
                  <button class="flex items-center bg-gray-200 p-2 rounded-lg text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="ml-1 text-sm">SEARCH</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- User Table with Equipment Status -->
            <div class="bg-white shadow rounded-lg p-3 overflow-x-auto">
              <h2 class="text-lg font-bold mb-2">User Using Equipment</h2>

              <div class="max-h-[300px] overflow-y-auto">
                <table class="table-auto w-full border-collapse border text-sm">
                  <thead class="bg-maroon text-white sticky top-0">
                    <tr>
                      <th class="px-2 py-1 border">No.</th>
                      <th class="px-2 py-1 border">ID #</th>
                      <th class="px-2 py-1 border">User</th>
                      <th class="px-2 py-1 border">Status</th>
                      <th class="px-2 py-1 border">Burrow</th>
                      <th class="px-2 py-1 border">Information</th>
                    </tr>
                  </thead>

                  <tbody>
                    <!-- Static rows for demonstration -->
                    <tr>
                      <td class="border px-2 py-1">1</td>
                      <td class="border px-2 py-1">1234</td>
                      <td class="border px-2 py-1">Russell Evan Loquinario</td>
                      <td class="border px-2 py-1 text-green-600">✔ Completed</td>
                      <td class="border px-2 py-1">Chair</td>
                      <td class="border px-2 py-1 text-center">
                        <button class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded">Details</button>
                      </td>
                    </tr>

                    <tr>
                      <td class="border px-2 py-1">2</td>
                      <td class="border px-2 py-1">1234</td>
                      <td class="border px-2 py-1">Russell Evan Loquinario</td>
                      <td class="border px-2 py-1 text-red-600">✘ Cancel</td>
                      <td class="border px-2 py-1">Computer</td>
                      <td class="border px-2 py-1 text-center">
                        <button class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded">Details</button>
                      </td>
                    </tr>

                    <tr>
                      <td class="border px-2 py-1">3</td>
                      <td class="border px-2 py-1">1234</td>
                      <td class="border px-2 py-1">Russell Evan Loquinario</td>
                      <td class="border px-2 py-1 text-green-600">✔ Completed</td>
                      <td class="border px-2 py-1">Laptop</td>
                      <td class="border px-2 py-1 text-center">
                        <button class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded">Details</button>
                      </td>
                    </tr>

                    <tr>
                      <td class="border px-2 py-1">4</td>
                      <td class="border px-2 py-1">1234</td>
                      <td class="border px-2 py-1">Russell Evan Loquinario</td>
                      <td class="border px-2 py-1 text-yellow-600">⌛ Pending</td>
                      <td class="border px-2 py-1">White Board</td>
                      <td class="border px-2 py-1 text-center">
                        <button class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded">Details</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Line Chart (Usage Over Time) -->
              <div class="bg-white shadow rounded-lg p-3 mt-3">
                <h2 class="text-lg font-bold mb-2">Equipments Percentage Use</h2>
                <canvas id="lineChart"></canvas>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<style scoped>
/* Custom maroon color for table header */
.bg-maroon {
  background-color: #800000;
}

/* Set maximum height for charts */
#pieChart,
#lineChart {
  max-height: 250px;
}
</style>
