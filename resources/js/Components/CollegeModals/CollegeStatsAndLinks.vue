<script setup>
// This component is purely structural and does not need complex setup logic.
</script>

<template>
    <!-- This div acts as the container for the 2/3rds content column -->
    <div class="space-y-6"> 

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-4 text-center border-b-4 border-sky-400">
                <p class="text-gray-500 text-sm">Class</p>
                <h2 class="text-2xl font-bold mt-1 text-sky-600">0</h2>
            </div>
            <div class="bg-white shadow rounded-lg p-4 text-center border-b-4 border-green-500">
                <p class="text-gray-500 text-sm">Students</p>
                <h2 class="text-2xl font-bold mt-1 text-green-600">0</h2>
            </div>
            <div class="bg-white shadow rounded-lg p-4 text-center border-b-4 border-yellow-500">
                <p class="text-gray-500 text-sm">Teacher</p>
                <h2 class="text-2xl font-bold mt-1 text-yellow-600">0</h2>
            </div>
            <div class="bg-white shadow rounded-lg p-4 text-center border-b-4 border-red-500">
                <p class="text-gray-500 text-sm">New Messages</p>
                <h2 class="text-2xl font-bold mt-1 text-red-600">0</h2>
            </div>
        </div>

        <!-- Leader Board Section -->
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 border-b pb-2 mb-2">Leader Board</h2>
            <div class="h-24 flex items-center justify-center text-gray-400 text-sm">No Data Available</div>
        </div>

        <!-- News & Events Section -->
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 border-b pb-2 mb-2">News & Events</h2>
            <div class="h-24 flex items-center justify-center text-gray-400 text-sm">No Data Available</div>
        </div>

        <!-- Quick Links Buttons -->
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">Quick Links</h2>
            <div class="grid grid-cols-2 gap-4">
                <!-- Buttons for navigation or actions -->
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">News Board</button>
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">Event</button>
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">Class Schedule</button>
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">Mail / SMS</button>
            </div>
        </div>
    </div>
</template>
