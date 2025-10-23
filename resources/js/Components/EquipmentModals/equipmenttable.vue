<script setup>
// This component focuses on the equipment status table and filter controls.
</script>

<template>
    <div class="space-y-3">
        <!-- Top Header with Filters -->
        <div class="bg-white shadow rounded-lg p-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-700">Equipment Availability</h2>

                <!-- Filter Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Student Type Filter -->
                    <div class="flex items-center rounded-lg bg-purple-600 p-2 text-white cursor-pointer hover:bg-purple-700 transition">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zM8 11a2 2 0 100-4 2 2 0 000 4zM12 11a2 2 0 100-4 2 2 0 000 4zM14 13a6 6 0 00-12 0v2h12v-2z"></path></svg>
                        <span class="font-semibold text-sm">Student</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <!-- Date Filter -->
                    <div class="flex items-center rounded-lg bg-orange-500 p-2 text-white cursor-pointer hover:bg-orange-600 transition">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 2h8v12H6V4zm2 2h4v2H8V6zm0 4h4v2H8v-2zm0 4h4v2H8v-2z"></path></svg>
                        <span class="font-semibold text-sm">Oct 5, 2025</span>
                    </div>

                    <!-- Time Filter -->
                    <div class="flex items-center rounded-lg bg-red-500 p-2 text-white cursor-pointer hover:bg-red-600 transition">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8V7h2v3h-2zm0 2v2h2v-2h-2z"></path></svg>
                        <span class="font-semibold text-sm">10:50 PM</span>
                    </div>

                    <!-- Check Status Button -->
                    <button class="rounded-lg bg-cyan-400 font-bold p-2 text-gray-800 text-sm hover:bg-cyan-500 transition shadow">Checked</button>

                    <!-- Search Button -->
                    <button class="flex items-center bg-gray-200 p-2 rounded-lg text-gray-700 hover:bg-gray-300 transition shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span class="ml-1 text-sm font-semibold">SEARCH</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- User Table with Equipment Status -->
        <div class="bg-white shadow rounded-lg p-3 overflow-x-auto">
            <h2 class="text-lg font-bold mb-2 text-gray-700">User Using Equipment</h2>

            <div class="max-h-[300px] overflow-y-auto border border-gray-200 rounded-lg">
                <table class="table-auto w-full border-collapse text-sm">
                    <thead class="bg-maroon text-white sticky top-0 shadow-md">
                        <tr>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">No.</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">ID #</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">User</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">Status</th>
                            <th class="px-3 py-2 border-b-2 border-r border-maroon-dark">Burrowed Item</th>
                            <th class="px-3 py-2 border-b-2">Information</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-700 divide-y divide-gray-100">
                        <!-- Static rows for demonstration (using v-for for better structure) -->
                        <tr v-for="i in 10" :key="i" :class="{'bg-white': i % 2 !== 0, 'bg-gray-50': i % 2 === 0}">
                            <td class="border-r px-3 py-2 text-center">{{ i }}</td>
                            <td class="border-r px-3 py-2 text-center">123{{ i }}</td>
                            <td class="border-r px-3 py-2 font-medium">Russell Evan Loquinario</td>
                            <td class="border-r px-3 py-2 text-center">
                                <span v-if="i % 3 === 1" class="text-green-600 font-semibold">✔ Completed</span>
                                <span v-else-if="i % 3 === 2" class="text-red-600 font-semibold">✘ Cancelled</span>
                                <span v-else class="text-yellow-600 font-semibold">⌛ Pending</span>
                            </td>
                            <td class="border-r px-3 py-2 text-center">{{ ['Chair', 'Computer', 'Laptop', 'White Board', 'Projector'][i % 5] }}</td>
                            <td class="px-3 py-2 text-center">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-2 py-1 rounded shadow-md transition">Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
