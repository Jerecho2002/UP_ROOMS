<script setup>
import { ref, computed, onMounted, onUnmounted, watch, defineEmits } from 'vue'
import EquipmentModal from '@/Components/EquipmentModals/EquipmentModal.vue'

// --- Props & Emits ---
const emit = defineEmits(['chart-data-update'])

// --- Real-time Date & Time ---
const currentDate = ref('')
const currentTime = ref('')
let timerInterval = null

const updateDateTime = () => {
    const now = new Date()
    currentDate.value = now.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    })
    currentTime.value = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    })
}

// --- Equipment Data ---
const usageList = ref([
    {
        id: 1,
        room: '1234',
        name: 'Russell Evan Loquinario',
        building: 'Admin Block A',
        college: 'CAS',
        equipmentUsed: [
            { inventory_id: 'LAP-001', name: 'Laptop (i7)', cfic: 'CFIC-A1', property_id: 'PID-1001' },
            { inventory_id: 'PRO-005', name: 'Projector', cfic: 'CFIC-B2', property_id: 'PID-1005' },
        ]
    },
    {
        id: 2,
        room: '5678',
        name: 'Maria Garcia Santos',
        building: 'Science & Tech Annex',
        college: 'CCPS',
        equipmentUsed: [
            { inventory_id: 'CMP-12A', name: 'Desktop PC (i5)', cfic: 'CFIC-D4', property_id: 'PID-1012' },
            { inventory_id: 'PRT-003', name: '3D Printer', cfic: 'CFIC-E5', property_id: 'PID-1013' },
        ]
    },
    {
        id: 3,
        room: '9012',
        name: 'John Michael Reyes',
        building: 'Library Hub',
        college: 'GSO',
        equipmentUsed: [
            { inventory_id: 'TV-002', name: 'Smart TV 65"', cfic: 'CFIC-F7', property_id: 'PID-1020' },
            { inventory_id: 'SPK-008', name: 'Speaker System', cfic: 'CFIC-G8', property_id: 'PID-1021' },
            { inventory_id: 'MIC-009', name: 'Wireless Microphone', cfic: 'CFIC-H9', property_id: 'PID-1022' },
        ]
    },
    {
        id: 4,
        room: '3456',
        name: 'Sarah Lee Tan',
        building: 'Admin Block A',
        college: 'CAS',
        equipmentUsed: [
            { inventory_id: 'LAP-002', name: 'Laptop (i5)', cfic: 'CFIC-A2', property_id: 'PID-1002' },
            { inventory_id: 'WBD-011', name: 'White Board', cfic: 'CFIC-C4', property_id: 'PID-1011' },
            { inventory_id: 'TAB-015', name: 'Tablet', cfic: 'CFIC-J1', property_id: 'PID-1015' },
        ]
    },
    {
        id: 5,
        room: '7890',
        name: 'Carlos Miguel Cruz',
        building: 'Engineering Wing',
        college: 'COE',
        equipmentUsed: [
            { inventory_id: 'DRN-020', name: '3D Scanner', cfic: 'CFIC-K2', property_id: 'PID-1023' },
            { inventory_id: 'CAM-025', name: 'Document Camera', cfic: 'CFIC-L3', property_id: 'PID-1025' },
        ]
    },
    {
        id: 6,
        room: '1122',
        name: 'Anna Marie Lopez',
        building: 'Science & Tech Annex',
        college: 'CCPS',
        equipmentUsed: [
            { inventory_id: 'MIC-010', name: 'Conference Microphone', cfic: 'CFIC-M4', property_id: 'PID-1030' },
        ]
    },
    {
        id: 7,
        room: '3344',
        name: 'Robert James Wilson',
        building: 'Library Hub',
        college: 'GSO',
        equipmentUsed: [
            { inventory_id: 'LAP-003', name: 'Laptop (i9)', cfic: 'CFIC-O6', property_id: 'PID-1032' },
            { inventory_id: 'PRO-006', name: 'HD Projector', cfic: 'CFIC-P7', property_id: 'PID-1033' },
        ]
    }
])

// --- Search State ---
const searchTerm = ref('')

// --- Filtered List ---
const filteredUsageList = computed(() => {
    const term = searchTerm.value.trim().toLowerCase()
    if (!term) return usageList.value

    return usageList.value.filter(item =>
        String(item.room).toLowerCase().includes(term) ||
        item.name.toLowerCase().includes(term) ||
        item.building.toLowerCase().includes(term) ||
        item.college.toLowerCase().includes(term)
    )
})

// --- Statistics (Based on Filtered List for dynamic charts) ---
const personStats = computed(() => {
    const stats = {}
    filteredUsageList.value.forEach(room => {
        if (!stats[room.name]) {
            stats[room.name] = { name: room.name, equipmentCount: 0 }
        }
        stats[room.name].equipmentCount += room.equipmentUsed.length
    })
    return Object.values(stats).sort((a, b) => b.equipmentCount - a.equipmentCount)
})

const buildingStats = computed(() => {
    const stats = {}
    filteredUsageList.value.forEach(room => {
        if (!stats[room.building]) {
            stats[room.building] = { building: room.building, equipmentCount: 0 }
        }
        stats[room.building].equipmentCount += room.equipmentUsed.length
    })
    return Object.values(stats).sort((a, b) => b.equipmentCount - a.equipmentCount)
})

const statistics = computed(() => {
    const totalEquipment = filteredUsageList.value.reduce((acc, curr) => acc + curr.equipmentUsed.length, 0)
    const totalRooms = filteredUsageList.value.length
    const buildings = new Set(filteredUsageList.value.map(r => r.building))

    return {
        totalEquipment,
        totalRooms,
        totalBuildings: buildings.size,
        avgEquipmentPerRoom: totalRooms > 0 ? (totalEquipment / totalRooms).toFixed(1) : 0
    }
})

// --- Modal State ---
const isDetailsModalVisible = ref(false)
const selectedUserUsage = ref(null)

const handleViewDetails = (usage) => {
    selectedUserUsage.value = usage
    isDetailsModalVisible.value = true
}

const closeDetailsModal = () => {
    isDetailsModalVisible.value = false
    selectedUserUsage.value = null
}

// --- Chart Data Exports ---
const getChartData = () => {
    return {
        pieData: {
            labels: personStats.value.map(p => p.name),
            datasets: [{
                data: personStats.value.map(p => p.equipmentCount),
                backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#9C27B0', '#FF5722', '#795548', '#607D8B']
            }]
        },
        barData: {
            labels: buildingStats.value.map(b => b.building),
            datasets: [{
                label: 'Equipment Count',
                data: buildingStats.value.map(b => b.equipmentCount),
                backgroundColor: '#7A0C23'
            }]
        }
    }
}

// --- Lifecycle & Watchers ---
onMounted(() => {
    updateDateTime()
    timerInterval = setInterval(updateDateTime, 1000)
    // Initial emit
    emit('chart-data-update', getChartData())
})

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval)
})

// Watch the filtered list—if the search or the data changes, update the parent charts
watch(filteredUsageList, () => {
    emit('chart-data-update', getChartData())
}, { deep: true })

</script>

<template>
    <div class="space-y-4 p-4">
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-[#7A0C23]">Equipment Management</h1>

                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex gap-2">
    <div class="flex items-center px-4 py-2 bg-yellow-400 border rounded-lg text-sm font-semibold text-white">
        📅 {{ currentDate }}
    </div>
    <div class="flex items-center px-4 py-2 bg-green-400 border rounded-lg text-sm font-semibold text-white">
        {{ currentTime }}
    </div>
</div>


                    <div class="relative w-full lg:w-72">
                        <input
                            type="text"
                            v-model="searchTerm"
                            placeholder="Search records..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition-all"
                        />
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

    <div class="p-3 bg-[#7A0C23] rounded-lg border border-red-100">
        <p class="text-xs text-white font-bold uppercase">Total Items</p>
        <p class="text-xl font-bold text-white">{{ statistics.totalEquipment }}</p>
    </div>

    <div class="p-3 bg-[#7A0C23] rounded-lg border border-blue-100">
        <p class="text-xs text-white font-bold uppercase">Active Rooms</p>
        <p class="text-xl font-bold text-white">{{ statistics.totalRooms }}</p>
    </div>

    <div class="p-3 bg-[#7A0C23] rounded-lg border border-green-100">
        <p class="text-xs text-white font-bold uppercase">Buildings</p>
        <p class="text-xl font-bold text-white">{{ statistics.totalBuildings }}</p>
    </div>

    <div class="p-3 bg-[#7A0C23] rounded-lg border border-yellow-100">
        <p class="text-xs text-white font-bold uppercase">Density</p>
        <p class="text-xl font-bold text-white">
            {{ statistics.avgEquipmentPerRoom }}
            <span class="text-sm font-normal text-white">/room</span>
        </p>
    </div>
</div>

        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[#7A0C23] text-white">
                        <tr>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Accountable Person</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">College</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in filteredUsageList" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ item.name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-green-700">Room {{ item.room }}</div>
                                <div class="text-xs text-gray-500">{{ item.building }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded">
                                    {{ item.college }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 flex items-center justify-center bg-green-100 text-green-700 rounded-full font-bold text-xs">
                                        {{ item.equipmentUsed.length }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium">Equipments</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    @click="handleViewDetails(item)"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 transition-all shadow-sm"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredUsageList.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <p class="text-lg font-bold">No results found</p>
                                <p class="text-sm">Try searching for a different name or room number.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <EquipmentModal
            :is-visible="isDetailsModalVisible"
            :selected-usage="selectedUserUsage"
            @close="closeDetailsModal"
        />
    </div>
</template>

<style scoped>
thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
