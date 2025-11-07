<script setup>
// -------------------------------------------
// Import components and dependencies
// -------------------------------------------
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import EquipmentTable from '@/Components/EquipmentModals/Equipmenttable.vue' // Imports the updated table
import EquipmentModals from '@/Components/EquipmentModals/EquipmentModal.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Chart from 'chart.js/auto'

// -------------------------------------------
// Sidebar: Controls sidebar open/close toggle
// -------------------------------------------
const sidebarOpen = ref(true)
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

// -------------------------------------------
// Chart refs and cleanup
// -------------------------------------------
const pieChartRef = ref(null)
const lineChartRef = ref(null)

let pieChartInstance = null
let lineChartInstance = null

onMounted(() => {
    // Destroy existing charts if they exist (prevents overlap)
    if (pieChartInstance) pieChartInstance.destroy()
    if (lineChartInstance) lineChartInstance.destroy()

    // PIE CHART - Equipment usage percentage
    pieChartInstance = new Chart(pieChartRef.value, {
        type: 'pie',
        data: {
            labels: ['Student', 'Room', 'Building'],
            datasets: [{
                data: [100, 80, 40],
                backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'],
                spacing: 2
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: ${ctx.parsed}%`
                    }
                }
            }
        }
    })

    // LINE CHART - Equipment usage trend over the year
    lineChartInstance = new Chart(lineChartRef.value, {
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
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#333'
                    }
                },
                x: {
                    ticks: {
                        color: '#333'
                    }
                }
            }
        }
    })
})

// Cleanup charts when component unmounts
onBeforeUnmount(() => {
    if (pieChartInstance) pieChartInstance.destroy()
    if (lineChartInstance) lineChartInstance.destroy()
})
</script>

<template>
  <div class="flex pt-14 h-screen transition-all duration-300">
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex-1 flex flex-col h-full overflow-hidden">
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="flex-1 bg-gray-100 p-3 h-full overflow-y-auto transition-all duration-300">
        <div class="grid grid-cols-12 gap-3 mb-3">
          <div class="col-span-12 md:col-span-3 space-y-3">
            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-sm font-bold mb-1 text-gray-700">Total Population</h2>
              <p class="text-xs text-gray-500">Student: <span class="font-semibold">100,000</span></p>
              <p class="text-xs text-gray-500">Room: <span class="font-semibold">50,000</span></p>
              <p class="text-xs text-gray-500">Building: <span class="font-semibold">14,000</span></p>
            </div>

            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-sm font-bold mb-1 text-gray-700">Usage Summary</h2>
              <p class="text-xs text-green-600">Student: <span class="font-semibold">100%</span></p>
              <p class="text-xs text-orange-500">Room: <span class="font-semibold">80%</span></p>
              <p class="text-xs text-blue-500">Building: <span class="font-semibold">40%</span></p>
            </div>

            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-sm font-bold mb-1 text-gray-700">Equipment Usage Percentage</h2>
              <canvas ref="pieChartRef" id="pieChart"></canvas>
              <div class="mt-2 text-xs">
                <p class="text-green-600 font-medium">■ Student: 100%</p>
                <p class="text-orange-500 font-medium">■ Room: 80%</p>
                <p class="text-blue-500 font-medium">■ Building: 40%</p>
              </div>
            </div>
          </div>

          <div class="col-span-12 md:col-span-9 space-y-3">
            <EquipmentTable />

            <div class="bg-white shadow rounded-lg p-3 mt-3">
              <h2 class="text-lg font-bold mb-2 text-gray-700">Equipment Usage Trend (Monthly)</h2>
              <canvas ref="lineChartRef" id="lineChart"></canvas>
            </div>
          </div>
        </div>

        <EquipmentModals />
      </main>
    </div>
  </div>
</template>

<style scoped>
.bg-maroon {
    background-color: #800000;
}
#pieChart,
#lineChart {
    max-height: 250px;
}
</style>