<script setup>
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import CollegeTableComponent from '@/Components/CollegeModals/CollegeStatsAndLinks.vue'

const sidebarVisible = ref(true)

const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}
</script>

<template>
    <div class="bg-gray-100 font-sans min-h-screen">

        <!-- NAVBAR -->
        <Navbar @toggleSidebar="toggleSidebar" />

        <div class="flex pt-14 min-h-screen w-full">

            <!-- SIDEBAR ALWAYS ATTACHED | NO SPACING -->
            <Sidebar 
                v-show="sidebarVisible"
                class="fixed top-14 left-0 h-[calc(100%-3.5rem)] w-56"
            />

            <!-- MAIN CONTENT — NO LEFT MARGIN -->
            <main 
                id="mainContent"
                class="flex-1 p-6 bg-gray-50 transition-all duration-300 w-full"
                :style="sidebarVisible ? 'margin-left: 14rem;' : 'margin-left: 0;'"
            >

                <h1 class="text-2xl font-normal text-[#7A0C23] mb-2">College</h1>
              

                <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 mb-6">

                    <!-- Search -->
                 

                    
                    <!-- TABLE -->
                    <CollegeTableComponent />
                </div>

            </main>

        </div>
    </div>
</template>
