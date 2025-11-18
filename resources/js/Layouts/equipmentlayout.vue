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
    if (pieChartInstance) pieChartInstance.destroy()
    if (lineChartInstance) lineChartInstance.destroy()

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
            plugins: { legend: { display: false } }
        }
    })

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
  <div class="flex pt-14 h-screen">
    <Sidebar :sidebarOpen="sidebarOpen" />

    <div class="flex-1 flex flex-col h-full overflow-hidden">
      <Navbar @toggleSidebar="toggleSidebar" />

      <main class="flex-1 bg-gray-100 p-3 h-full overflow-y-auto">

        <!-- GRID LAYOUT EXACTLY LIKE YOUR SCREENSHOT -->
        <div class="grid grid-cols-1 gap-3">

          <!-- LEFT SIDE SMALL WIDGETS -->
        

          <!-- RIGHT SIDE FULL WIDTH TABLE + PIE + LINE -->
          <div class="col-span-12 md:col-span-9 space-y-3">

            <!-- TABLE FULL WIDTH -->
            <EquipmentTable />

            <!-- PIE CHART BELOW TABLE -->
            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-lg font-bold text-gray-700">Equipment Usage Percentage</h2>

              <canvas id="pieChart" ref="pieChartRef"></canvas>

              <div class="mt-2 text-xs">
                <p class="text-green-600 font-medium">■ Student: 100%</p>
                <p class="text-orange-500 font-medium">■ Room: 80%</p>
                <p class="text-blue-500 font-medium">■ Building: 40%</p>
              </div>
            </div>

            <!-- LINE CHART LAST -->
            <div class="bg-white shadow rounded-lg p-3">
              <h2 class="text-lg font-bold text-gray-700">Accountability (Monthly)</h2>
              <canvas id="lineChart" ref="lineChartRef"></canvas>
            </div>

          </div>
        </div>

        <EquipmentModals />
      </main>
    </div>
  </div>
</template>

<style scoped>
#pieChart,
#lineChart {
  max-height: 250px;
}
</style>
