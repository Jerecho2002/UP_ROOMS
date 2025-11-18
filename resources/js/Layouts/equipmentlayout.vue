<script setup>
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import EquipmentTable from '@/Components/EquipmentModals/EquipmentTable.vue'
import EquipmentModals from '@/Components/EquipmentModals/EquipmentModal.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Chart from 'chart.js/auto'

const sidebarOpen = ref(true)
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

const pieChartRef = ref(null)
const lineChartRef = ref(null)

let pieChartInstance = null
let lineChartInstance = null

onMounted(() => {
    // Destroy previous instances if they exist
    if (pieChartInstance) pieChartInstance.destroy()
    if (lineChartInstance) lineChartInstance.destroy()

    // --- Pie Chart Initialization ---
    // Note: The Chart.js options already hide the built-in legend (plugins: { legend: { display: false } })
    pieChartInstance = new Chart(pieChartRef.value, {
        type: 'pie',
        data: {
            labels: ['Student', 'Room', 'Building'],
            datasets: [{
                data: [100, 80, 40],
                backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allows chart to respect the max-height
            plugins: { legend: { display: false } }
        }
    })

    // --- Line Chart Initialization ---
    lineChartInstance = new Chart(lineChartRef.value, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Usage',
                data: [20,40,35,50,70,60,65,55,45,50,40,35],
                borderColor: '#800000',
                backgroundColor: 'rgba(128,0,0,0.25)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            aspectRatio: 3,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true },
                x: {}
            }
        }
    })
})

onBeforeUnmount(() => {
    if (pieChartInstance) pieChartInstance.destroy()
    if (lineChartInstance) lineChartInstance.destroy()
})
</script>

<template>
  <div class="flex pt-14 h-full">
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex-1 flex flex-col h-screen">
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="flex-1 bg-gray-50 p-3 h-full ">

        <div class="grid grid-cols-1 gap-3">

          <div class="col-span-12">
            <EquipmentTable />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 col-span-12">

           <div class="bg-white shadow rounded-lg p-3 flex flex-col justify-start items-center h-[500px]">
    <h2 class="text-lg font-bold text-gray-700 w-full text-center mb-4">Equipment Usage Percentage</h2>

    <div class="flex justify-center items-center h-[350px]"> 
        <canvas id="pieChart" ref="pieChartRef" class="w-full "></canvas>
    </div>

<div class="mt-4 pt-2 border-t border-gray-100 text-sm flex flex-row items-center justify-center space-x-6 w-full">
    <p class="text-green-600 font-medium">■ Student: 100%</p>
    <p class="text-orange-500 font-medium">■ Room: 80%</p>
    <p class="text-blue-500 font-medium">■ Building: 40%</p>
</div>
</div>
            
<div class="bg-white shadow rounded-lg p-3 flex flex-col justify-center items-center h-[500px]">
             <h2 class="text-lg font-bold text-gray-700 h-full text-left ">Accountability (Monthly)</h2>
              <canvas id="lineChart" ref="lineChartRef" class="w-full mb-[150px]"></canvas>
            </div>
          </div>
        </div>

        <EquipmentModals />
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Scoped styles are no longer needed for chart dimensions as they are handled by Tailwind classes inline */
</style>