<script setup>
import { ref, computed, onMounted, onUnmounted, watch, defineEmits } from 'vue'
import axios from 'axios'
import EquipmentModal from '@/Components/EquipmentModals/EquipmentModal.vue'
import IconButton from '@/Components/IconButton.vue'

// --- Props & Emits ---
const emit = defineEmits(['chart-data-update'])

// --- API Base URL ---
const baseUrl = '/api/equipment'

// --- Pagination State ---
const currentPage = ref(1)
const itemsPerPage = ref(5)

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
const usageList = ref([])
const loading = ref(false)
const error = ref(null)

// --- Search State ---
const searchTerm = ref('')
const statusFilter = ref('')
const collegeFilter = ref('')
const departmentFilter = ref('')
const buildingFilter = ref('')
const assignedUserFilter = ref('')

// --- Statistics ---
const statistics = ref({
    total: 0,
    available: 0,
    in_use: 0,
    maintenance: 0,
    damaged: 0,
    retired: 0,
    total_value: 0,
    average_value: 0,
    person_stats: [],
    building_stats: []
})

// --- Fetch Equipment Usage Data ---
const fetchEquipmentUsage = async () => {
    try {
        loading.value = true
        const response = await axios.get(`${baseUrl}/usage`, {
            params: { search: searchTerm.value }
        })

        if (response.data.success) {
            usageList.value = response.data.usage_list.map((item, index) => ({
                id: item.id || index + 1,
                room: item.room || 'N/A',
                name: item.name,
                building: item.building,
                college: item.college,
                equipmentUsed: item.equipmentUsed.map(eq => ({
                    inventory_id: eq.inventory_id,
                    property_id: eq.property_id,
                    name: eq.name,
                    cfic: eq.cfic || 'N/A',
                    status: eq.status,
                    description: eq.description
                }))
            }))
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch equipment usage data'
        console.error('Error fetching equipment usage:', err)
    } finally {
        loading.value = false
    }
}

// --- Fetch Equipment Statistics ---
const fetchEquipmentStats = async () => {
    try {
        const response = await axios.get(`${baseUrl}/stats`)

        if (response.data.success) {
            statistics.value = response.data.data

            // Emit chart data to parent component
            emit('chart-data-update', {
                pieData: {
                    labels: statistics.value.person_stats.map(p => p.name),
                    datasets: [{
                        data: statistics.value.person_stats.map(p => p.equipmentCount),
                        backgroundColor: [
                            '#4CAF50', '#FF9800', '#2196F3', '#9C27B0',
                            '#FF5722', '#795548', '#607D8B', '#3F51B5',
                            '#009688', '#E91E63'
                        ]
                    }]
                },
                barData: {
                    labels: statistics.value.building_stats.map(b => b.building),
                    datasets: [{
                        label: 'Equipment Count',
                        data: statistics.value.building_stats.map(b => b.equipmentCount),
                        backgroundColor: '#7A0C23'
                    }]
                }
            })
        }
    } catch (err) {
        console.error('Error fetching equipment stats:', err)
    }
}

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

// --- Paginated Data ---
const paginatedUsageList = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredUsageList.value.slice(start, end)
})

// --- Pagination Computed ---
const totalPages = computed(() => {
    return Math.ceil(filteredUsageList.value.length / itemsPerPage.value)
})

const showingRange = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1
    const end = Math.min(currentPage.value * itemsPerPage.value, filteredUsageList.value.length)
    const total = filteredUsageList.value.length
    return { start, end, total }
})

// --- Pagination Methods ---
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
    }
}

const resetPagination = () => {
    currentPage.value = 1
}

// --- Statistics Computed (Based on Filtered List) ---
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

const computedStats = computed(() => {
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

// --- Initialize Data ---
const initializeData = async () => {
    await Promise.all([
        fetchEquipmentUsage(),
        fetchEquipmentStats()
    ])
}

// --- Lifecycle & Watchers ---
onMounted(() => {
    updateDateTime()
    timerInterval = setInterval(updateDateTime, 1000)
    initializeData()
})

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval)
})

// Watch the filtered list—if the search or the data changes, update the parent charts
watch(filteredUsageList, () => {
    // Update charts with filtered data
    emit('chart-data-update', {
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
    })

    // Reset pagination when search changes
    resetPagination()
}, { deep: true })

// Watch items per page change
watch(itemsPerPage, () => {
    resetPagination()
})

// Watch search term and fetch new data
watch(searchTerm, () => {
    fetchEquipmentUsage()
})
</script>

<template>
    <div class="space-y-4 p-4">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#7A0C23]"></div>
            <p class="mt-2 text-gray-600">Loading equipment data...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-600 font-semibold">Error: {{ error }}</p>
            <button @click="initializeData" class="mt-2 px-4 py-2 bg-[#7A0C23] text-white rounded-lg hover:bg-[#5a091a] transition-colors">
                Retry
            </button>
        </div>

        <!-- Main Content -->
        <div v-else>
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-[#7A0C23]">Equipment Management</h1>
                        <p class="text-gray-600 mt-1">Track and manage all equipment assets</p>
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
                                placeholder="Search by name, room, building..."
                                class="w-full pl-10 pr-4 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none transition-all"
                            />
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <IconButton
                                    icon="search"
                                    size="sm"
                                    color="gray"
                                    class="pointer-events-none"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="p-3 bg-[#7A0C23] rounded-lg border border-red-100">
                        <p class="text-xs text-white font-bold uppercase">Total Items</p>
                        <p class="text-xl font-bold text-white">{{ statistics.total || 0 }}</p>
                    </div>

                    <div class="p-3 bg-[#7A0C23] rounded-lg border border-blue-100">
                        <p class="text-xs text-white font-bold uppercase">Available</p>
                        <p class="text-xl font-bold text-white">{{ statistics.available || 0 }}</p>
                    </div>

                    <div class="p-3 bg-[#7A0C23] rounded-lg border border-green-100">
                        <p class="text-xs text-white font-bold uppercase">In Use</p>
                        <p class="text-xl font-bold text-white">{{ statistics.in_use || 0 }}</p>
                    </div>

                    <div class="p-3 bg-[#7A0C23] rounded-lg border border-yellow-100">
                        <p class="text-xs text-white font-bold uppercase">Total Value</p>
                        <p class="text-xl font-bold text-white">
                            ₱{{ (statistics.total_value || 0).toLocaleString() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-yellow-400">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#7A0C23] text-white sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Accountable Person</th>
                                <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Location</th>
                                <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">College</th>
                                <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider">Items</th>
                                <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-yellow-400">
                            <tr v-for="item in paginatedUsageList" :key="item.id" class="hover:bg-gray-300 transition-colors">
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
                                    <IconButton
                                        @click="handleViewDetails(item)"
                                        icon="eye"
                                        title="View Details"
                                        size="sm"
                                        color="green"
                                        outlined
                                        class="hover:scale-105 transition-transform"
                                    >
                                        View Details
                                    </IconButton>
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

                <!-- Pagination Controls -->
                <div v-if="filteredUsageList.length > 0" class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                        <!-- Showing range -->
                        <div class="text-sm text-gray-600">
                            Showing {{ showingRange.start }} to {{ showingRange.end }} of {{ showingRange.total }} entries
                        </div>

                        <!-- Items per page selector -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Show:</span>
                            <select
                                v-model="itemsPerPage"
                                class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                            >
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-sm text-gray-600">per page</span>
                        </div>

                        <!-- Page navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous button -->
                            <IconButton
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                icon="chevronLeft"
                                title="Previous Page"
                                size="sm"
                                color="gray"
                                outlined
                                :class="[
                                    'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150',
                                    currentPage === 1
                                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Previous
                            </IconButton>

                            <!-- Page numbers -->
                            <div class="flex items-center space-x-1">
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    @click="goToPage(page)"
                                    :class="[
                                        'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px] transition-colors duration-150',
                                        currentPage === page
                                            ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ page }}
                                </button>
                            </div>

                            <!-- Next button -->
                            <IconButton
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                icon="chevronRight"
                                title="Next Page"
                                size="sm"
                                color="gray"
                                outlined
                                :class="[
                                    'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150',
                                    currentPage === totalPages
                                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Next
                            </IconButton>
                        </div>

                        <!-- Page indicator -->
                        <div class="text-sm text-gray-600">
                            Page {{ currentPage }} of {{ totalPages }}
                        </div>
                    </div>

                    <!-- Results summary -->
                    <div class="mt-4 pt-3 border-t border-gray-300 text-center">
                        <p class="text-sm text-gray-500">
                            Filtered Results: <span class="font-semibold text-[#7A0C23]">{{ filteredUsageList.length }}</span>
                            | Total Records: <span class="font-semibold text-[#7A0C23]">{{ usageList.length }}</span>
                        </p>
                    </div>
                </div>

                <!-- Empty state when no data -->
                <div v-if="usageList.length === 0" class="px-6 py-12 text-center text-gray-400 bg-white">
                    <p class="text-lg font-bold">No equipment records available</p>
                    <p class="text-sm mt-2">Start by adding equipment records to the system.</p>
                </div>
            </div>

            <EquipmentModal
                :is-visible="isDetailsModalVisible"
                :selected-usage="selectedUserUsage"
                @close="closeDetailsModal"
            />
        </div>
    </div>
</template>

<style scoped>
thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Custom styles for pagination */
button:not(:disabled):hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease;
}

/* Ensure pagination controls are properly spaced */
.space-x-1 > * + * {
    margin-left: 0.25rem;
}

.space-x-2 > * + * {
    margin-left: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .flex-col.md\:flex-row {
        gap: 1rem;
    }

    .space-x-2 {
        justify-content: center;
    }
}
</style>
