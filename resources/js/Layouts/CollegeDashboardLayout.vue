<script setup>
import { ref } from 'vue'
// NOTE: Assuming '@/Components/Navbar.vue' and '@/Components/Sidebar.vue' are defined.
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// Import the new dashboard content components
import CollegeCalendar from '@/Components//CollegeModals/CollegeCalendar.vue'
import CollegeStatsAndLinks from '@/Components//CollegeModals/CollegeStatsAndLinks.vue' // Contains Stats Cards, User Table, and Quick Links
import CollegeModals from '@/Components//CollegeModals/CollegeModals.vue' // Placeholder

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

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <CollegeStatsAndLinks />
                    </div>

                    <div class="space-y-6">
                         <div class="bg-white shadow rounded-lg p-4">
                            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">📅 Class Schedule</h2>
                            <CollegeCalendar />
                         </div>
                    </div>
                </div>

                <CollegeModals />

            </main>
        </div>
    </div>
</template>