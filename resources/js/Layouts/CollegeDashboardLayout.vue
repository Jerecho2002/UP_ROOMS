<script setup>
import { ref } from 'vue'
// NOTE: Assuming '@/Components/Navbar.vue' and '@/Components/Sidebar.vue' are defined.
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// Import the new dashboard content components
import CollegeCalendar from '@/Components//CollegeModals/CollegeCalendar.vue'
import CollegeStatsAndLinks from '@/Components//CollegeModals/CollegeStatsAndLinks.vue'
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
        <!-- NAVBAR -->
        <!-- Navbar contains toggle button which triggers sidebar visibility toggle -->
        <Navbar @toggleSidebar="toggleSidebar" />

        <!-- MAIN LAYOUT -->
        <div class="flex pt-14 min-h-screen transition-all duration-300">
            
            <!-- SIDEBAR -->
            <!-- Sidebar visibility controlled reactively -->
            <Sidebar v-show="sidebarVisible" />

            <!-- MAIN CONTENT AREA -->
            <main id="mainContent" class="flex-1 p-6 bg-gray-50 transition-all duration-300">

                <!-- PAGE HEADER -->
                <h1 class="text-2xl font-bold text-gray-800 mb-6">College</h1>

                <!-- The Dashboard content is now split into two main components -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    
                    <!-- Left and Center Content (Stats, Leaderboard, Links) -->
                    <CollegeStatsAndLinks class="lg:col-span-2 space-y-6" />

                    <!-- Right Content (Calendar) -->
                    <CollegeCalendar />
                    
                </div>
                
                <!-- Modals Placeholder -->
                <CollegeModals />

            </main>
        </div>
    </div>
</template>
