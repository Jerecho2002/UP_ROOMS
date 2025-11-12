<script setup>
import { ref } from 'vue'
// NOTE: Assuming '@/Components/Navbar.vue' and '@/Components/Sidebar.vue' are defined.
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// Import the dashboard content components
import CollegeCalendar from '@/Components/CollegeModals/CollegeCalendar.vue'
import CollegeStatsAndLinks from '@/Components/CollegeModals/CollegeStatsAndLinks.vue'
import CollegeModals from '@/Components/CollegeModals/CollegeModals.vue' // Placeholder

// ----------------------------------------------
// Sidebar toggle state and method
// ----------------------------------------------
const sidebarVisible = ref(true) // Controls sidebar visibility

// Toggle sidebar visibility on toggle event
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}
</script>

<template>
    <div class="bg-gray-100 font-sans">
        <Navbar @toggleSidebar="toggleSidebar" />

        <div class="flex pt-14 min-h-screen transition-all duration-300">

            <Sidebar v-show="sidebarVisible" />

            <main id="mainContent" class="flex-1 p-6 bg-gray-50 transition-all duration-300">
                <h1 class="text-xl font-normal text-[#7A0C23] mb-6">College</h1>

                <div class="grid grid-cols-4 gap-4 mb-6">
                    <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-2 px-4 rounded shadow transition duration-150 font-medium text-base">News Board</button>
                    <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-2 px-4 rounded shadow transition duration-150 font-medium text-base">Class Schedule</button>
                    <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-2 px-4 rounded shadow transition duration-150 font-medium text-base">Event</button>
                    <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-2 px-4 rounded shadow transition duration-150 font-medium text-base">Mail / SMS</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <CollegeStatsAndLinks />
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white shadow rounded-lg p-4">
                            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">Calendar</h2>
                            <CollegeCalendar />
                        </div>
                    </div>
                </div>

                <CollegeModals />

            </main>
        </div>
    </div>
</template>