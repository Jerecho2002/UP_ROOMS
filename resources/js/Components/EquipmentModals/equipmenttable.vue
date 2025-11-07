<script setup>
import { ref, computed } from 'vue'

// --- Simulated User Equipment Usage Data (Matching Screenshot Headers) ---
const usageList = ref([
    { 
        id: 1, 
        room: '1234', 
        name: 'Russell Evan Loquinario', 
        building: 'Admin Block A', 
        college: 'CAS', 
        capacity: 50, // Not strictly needed here, but kept for context
        location: '1st Floor', // Not strictly needed here, but kept for context
        roomType: 'Lecture Hall', // Not strictly needed here, but kept for context
        status: 'Completed', 
        equipmentUsed: [
            { inventory_id: 'LAP-001', name: 'Laptop (i7)', cfic: 'CFIC-A1', property_id: 'PID-1001', status: 'Returned' },
            { inventory_id: 'PRO-005', name: 'Projector', cfic: 'CFIC-B2', property_id: 'PID-1005', status: 'Returned' },
        ]
    },
    { 
        id: 2, 
        room: '1234', 
        name: 'Russell Evan Loquinario', 
        building: 'Science & Tech Annex', 
        college: 'CCPS', 
        status: 'Cancel', 
        equipmentUsed: [
            { inventory_id: 'CMP-12A', name: 'Desktop PC (i5)', cfic: 'CFIC-D4', property_id: 'PID-1012', status: 'Cancelled' },
        ]
    },
    { 
        id: 3, 
        room: '1234', 
        name: 'Russell Evan Loquinario', 
        building: 'Library Hub', 
        college: 'GSO', 
        status: 'Completed', 
        equipmentUsed: [
            { inventory_id: 'TV-002', name: 'Smart TV 65"', cfic: 'CFIC-F7', property_id: 'PID-1020', status: 'Returned' },
        ]
    },
    { 
        id: 4, 
        room: '1234', 
        name: 'Russell Evan Loquinario', 
        building: 'Admin Block A', 
        college: 'CAS', 
        status: 'Pending', 
        equipmentUsed: [
            { inventory_id: 'LAP-002', name: 'Laptop (i5)', cfic: 'CFIC-A2', property_id: 'PID-1002', status: 'Out' },
            { inventory_id: 'WBD-011', name: 'White Board', cfic: 'CFIC-C4', property_id: 'PID-1011', status: 'Out' },
        ]
    },
]);

// --- Filtering/Search State ---
const searchTerm = ref('');
const filterStatus = ref('All'); 
const currentStatusFilter = ref(null); // Used to filter by 'Student', 'Room', 'Building' status

const filteredUsageList = computed(() => {
    let list = usageList.value;
    const lowerSearch = searchTerm.value.toLowerCase();

    if (lowerSearch) {
        list = list.filter(item => 
            String(item.room).includes(lowerSearch) ||
            item.name.toLowerCase().includes(lowerSearch) ||
            item.building.toLowerCase().includes(lowerSearch) ||
            item.college.toLowerCase().includes(lowerSearch)
        );
    }
    
    // Add logic for 'Checked' button filter if needed (using currentStatusFilter as the model)
    // if (currentStatusFilter.value && item.status !== currentStatusFilter.value) { ... }
    
    return list;
});

// --- Modal State for Equipment Details ---
const isDetailsModalVisible = ref(false);
const selectedUserUsage = ref(null);

const handleViewDetails = (usage) => {
    selectedUserUsage.value = usage;
    isDetailsModalVisible.value = true;
};

const closeDetailsModal = () => {
    isDetailsModalVisible.value = false;
    selectedUserUsage.value = null;
};
</script>

<template>
    <div class="space-y-3">
        <div class="bg-white shadow rounded-lg p-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-[#7A0C23]">Equipment Availability</h2>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center rounded-lg bg-purple-600 p-2 text-white cursor-pointer hover:bg-purple-700 transition">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zM8 11a2 2 0 100-4 2 2 0 000 4zM12 11a2 2 0 100-4 2 2 0 000 4zM14 13a6 6 0 00-12 0v2h12v-2z"></path></svg>
                        <span class="font-semibold text-sm">Student</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <div class="flex items-center rounded-lg bg-orange-500 p-2 text-white cursor-pointer hover:bg-orange-600 transition">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 2h8v12H6V4zm2 2h4v2H8V6zm0 4h4v2H8v-2zm0 4h4v2H8v-2z"></path></svg>
                        <span class="font-semibold text-sm">Oct 5, 2025</span>
                    </div>

                    <div class="flex items-center rounded-lg bg-red-500 p-2 text-white cursor-pointer hover:bg-red-600 transition">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8V7h2v3h-2zm0 2v2h2v-2h-2z"></path></svg>
                        <span class="font-semibold text-sm">10:50 PM</span>
                    </div>

                    <div class="relative bg-gray-200 rounded-lg flex items-center shadow-inner h-9">
                        <input 
                            type="text" 
                            v-model="searchTerm"
                            placeholder="Search" 
                            class="w-40 bg-transparent border-none focus:ring-0 focus:outline-none text-gray-700 placeholder-gray-500 pl-3 pr-8 text-sm"
                        />
                        <button class="absolute right-0 top-0 bottom-0 flex items-center p-2 text-gray-700 hover:text-gray-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>

                    <button class="rounded-lg bg-cyan-400 font-bold p-2 text-gray-800 text-sm hover:bg-cyan-500 transition shadow">Checked</button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-3 overflow-x-auto">
            <h2 class="text-lg font-bold mb-2 text-gray-700">User Using Equipment</h2>

            <div class="max-h-[300px] overflow-y-auto border border-gray-200 rounded-lg">
                <table class="table-auto w-full border-collapse text-sm">
                    <thead class="bg-maroon text-white sticky top-0 shadow-md">
                        <tr>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">Room</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">Name</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">Building</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">College</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark text-center">Status</th>
                            <th class="px-3 py-2 border-b-2 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700 divide-y divide-gray-100">
                        <tr v-for="(item, index) in filteredUsageList" :key="item.id" :class="{'bg-white': index % 2 === 0, 'bg-gray-50': index % 2 !== 0}">
                            <td class="border-r px-3 py-2 text-center">{{ item.room }}</td>
                            <td class="border-r px-3 py-2 font-medium">{{ item.name }}</td>
                            <td class="border-r px-3 py-2 text-center">{{ item.building }}</td>
                            <td class="border-r px-3 py-2 text-center">{{ item.college }}</td>
                            <td class="border-r px-3 py-2 text-center">
                                <span v-if="item.status === 'Completed'" class="text-green-600 font-semibold">✔ Completed</span>
                                <span v-else-if="item.status === 'Cancel'" class="text-red-600 font-semibold">✘ Cancel</span>
                                <span v-else class="text-yellow-600 font-semibold">⚪ Pending</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button 
                                    @click="handleViewDetails(item)"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-2 py-1 rounded shadow-md transition"
                                >
                                    Details
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredUsageList.length === 0">
                            <td colspan="6" class="text-center py-4 text-gray-500">No user data matches your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <transition name="fade">
            <div v-if="isDetailsModalVisible" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl p-6 relative">
                    
                    <h3 class="text-xl font-bold text-[#800020] mb-2 pb-2 border-b">
                        Equipment Used by: {{ selectedUserUsage?.name }} 
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">Room: {{ selectedUserUsage?.room }} | College: {{ selectedUserUsage?.college }}</p>

                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600">Inventory ID</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Property ID</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Name</th>
                                    <th class="px-4 py-2 text-left text-gray-600">CFIC</th>
                                    <th class="px-4 py-2 text-center text-gray-600">Item Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in selectedUserUsage?.equipmentUsed" :key="item.property_id" class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ item.inventory_id }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-700">{{ item.property_id }}</td>
                                    <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ item.cfic }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span :class="{'bg-green-100 text-green-700': item.status === 'Returned', 'bg-red-100 text-red-700': item.status === 'Out' || item.status === 'Cancelled'}"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button 
                            @click="closeDetailsModal"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-150"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.bg-maroon {
    background-color: #800000;
}
.bg-maroon-dark {
    background-color: #7A0C23;
}
/* Fade transition for the details modal */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>