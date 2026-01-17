<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import EquipmentTable from '@/Components/EquipmentModals/EquipmentTable.vue'
import Chart from 'chart.js/auto'

// Sidebar state
const sidebarOpen = ref(true)
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

// Chart references and instances
const pieChartRef = ref(null)
const barChartRef = ref(null)

let pieChartInstance = null
let barChartInstance = null

// Chart data from EquipmentTable
const chartData = ref({
    pieData: { labels: [], datasets: [] },
    barData: { labels: [], datasets: [] }
})

// Handle chart data updates from EquipmentTable
const handleChartDataUpdate = (data) => {
    chartData.value = data
    updateCharts()
}

// Update charts with new data
const updateCharts = () => {
    if (pieChartInstance && chartData.value.pieData.labels.length > 0) {
        pieChartInstance.data = chartData.value.pieData
        pieChartInstance.update()
    }

    if (barChartInstance && chartData.value.barData.labels.length > 0) {
        barChartInstance.data = chartData.value.barData
        barChartInstance.update()
    }
}

// Initialize charts
const initializeCharts = () => {
    // Destroy previous instances if they exist
    if (pieChartInstance) pieChartInstance.destroy()
    if (barChartInstance) barChartInstance.destroy()

    // --- Pie Chart: Equipment Distribution by Person ---
    pieChartInstance = new Chart(pieChartRef.value, {
        type: 'pie',
        data: chartData.value.pieData.labels.length > 0 ? chartData.value.pieData : {
            labels: ['Loading...'],
            datasets: [{ data: [100], backgroundColor: ['#CCCCCC'] }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: { size: 10 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw} equipment items`
                        }
                    }
                }
            }
        }
    })

    // --- Bar Chart: Equipment Count by Building ---
    barChartInstance = new Chart(barChartRef.value, {
        type: 'bar',
        data: chartData.value.barData.labels.length > 0 ? chartData.value.barData : {
            labels: ['Loading...'],
            datasets: [{
                label: 'Equipment Count',
                data: [0],
                backgroundColor: '#7A0C23',
                borderColor: '#7A0C23',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    })
}

// Lifecycle hooks
onMounted(() => {
    setTimeout(initializeCharts, 100)
})

onBeforeUnmount(() => {
    if (pieChartInstance) pieChartInstance.destroy()
    if (barChartInstance) barChartInstance.destroy()
})

// Watch for chart data updates
watch(chartData, updateCharts, { deep: true })
</script>

<template>
    <Head title="Equipment Management" />

    <div class="flex pt-14 h-screen">
        <Sidebar :sidebarOpen="sidebarOpen" />

        <div class="flex-1 flex flex-col">
            <Navbar @toggleSidebar="toggleSidebar" />

            <main class="flex-1 bg-gray-200 p-4 overflow-y-auto">
                <div class="space-y-4">
                    <!-- Main Equipment Table -->
                    <div>
                        <EquipmentTable @chart-data-update="handleChartDataUpdate" />
                    </div>

                    <!-- Charts Section - Side by Side -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Pie Chart Container -->
                        <div class="bg-white shadow-lg rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-bold text-gray-800">Equipment Distribution by Person</h2>
                                <div class="flex items-center text-sm text-gray-500">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-1"></div>
                                    <span>Total: {{ chartData.pieData.datasets[0]?.data?.reduce((a, b) => a + b, 0) || 0 }} items</span>
                                </div>
                            </div>
                            <div class="h-[320px] relative">
                                <canvas id="pieChart" ref="pieChartRef" class="w-full h-full"></canvas>
                            </div>
                        </div>

                        <!-- Bar Chart Container -->
                        <div class="bg-white shadow-lg rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-bold text-gray-800">Equipment Count by Building</h2>
                                <div class="flex items-center text-sm text-gray-500">
                                    <div class="w-3 h-3 bg-[#7A0C23] rounded-full mr-1"></div>
                                    <span>Buildings: {{ chartData.barData.labels?.length || 0 }}</span>
                                </div>
                            </div>
                            <div class="h-[320px] relative">
                                <canvas id="barChart" ref="barChartRef" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
