<script setup>
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// ----------------------------------------------
// Sidebar toggle state and method
// ----------------------------------------------

// Controls visibility of the Sidebar component
const sidebarVisible = ref(true)

// Toggles sidebar visibility when called
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}

// ----------------------------------------------
// Props definition to receive data from Laravel backend
// ----------------------------------------------

// Define expected prop 'buildings' which is an array of building objects
const props = defineProps({
  buildings: {
    type: Array,
    required: true,
    default: () => []
  }
})

// Extract 'buildings' from props for easier use in template
const buildings = props.buildings
</script>

<template>
  <div class="bg-gray-100 font-sans antialiased">
    <!-- NAVBAR -->
    <!-- Navbar emits an event 'toggleSidebar' when the toggle button is clicked -->
    <Navbar @toggleSidebar="toggleSidebar" />

    <!-- MAIN LAYOUT -->
    <div class="flex pt-14 min-h-screen transition-all duration-300">
      
      <!-- SIDEBAR -->
      <!-- Sidebar visibility controlled by 'sidebarVisible' reactive state -->
      <Sidebar v-show="sidebarVisible" />

      <!-- PAGE CONTENT -->
      <main id="mainContent" class="flex-1 px-6 py-6 bg-gray-50 transition-all duration-300">
        
        <!-- Page Title & Search Section -->
        <div class="flex items-center justify-between mb-6">
          
          <!-- Title and Breadcrumb -->
          <div>
            <h1 class="text-xl font-bold text-gray-800">Building</h1>
            <div class="text-xs text-gray-500">UPCEBU › Buildings</div>
          </div>

          <!-- Search Input and Add Button -->
          <div class="flex items-center space-x-2">
            
            <!-- Search input (functionality to be added) -->
            <input
              type="text"
              placeholder="SEARCH"
              class="border border-gray-300 rounded-full px-4 py-2 w-72 focus:outline-none focus:ring-2 focus:ring-sky-400"
            />

            <!-- Add button (action to be added, e.g., open form modal) -->
            <button
              class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md shadow"
            >
              Add
            </button>
          </div>
        </div>

        <!-- Building Table -->
        <div class="bg-white rounded shadow overflow-hidden">
          
          <!-- Make table horizontally scrollable on small screens -->
          <div class="overflow-x-auto">
            
            <table class="min-w-full text-sm text-gray-800">
              <thead class="bg-[#7A0C23] text-white">
                <tr>
                  <!-- Table Headers -->
                  <th class="px-6 py-3 text-left">Name</th>
                  <th class="px-6 py-3 text-left">Address</th>
                  <th class="px-6 py-3 text-center">Total Space</th>
                  <th class="px-6 py-3 text-center">Lift</th>
                  <th class="px-6 py-3 text-center">Parking</th>
                  <th class="px-6 py-3 text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Loop over buildings array to display each building -->
                <tr
                  v-for="b in buildings"
                  :key="b.id"
                  class="odd:bg-white even:bg-gray-50 hover:bg-gray-100"
                >
                  <td class="px-6 py-3">{{ b.name }}</td>
                  <td class="px-6 py-3">{{ b.address }}</td>
                  <td class="px-6 py-3 text-center">{{ b.total_space }}</td>
                  <td class="px-6 py-3 text-center">{{ b.lift }}</td>
                  <td class="px-6 py-3 text-center">
                    <!-- Display 'Yes' or 'No' based on boolean parking value -->
                    {{ b.parking ? 'Yes' : 'No' }}
                  </td>
                  <td class="px-6 py-3 text-center">
                    <!-- Placeholder for future action buttons (edit/delete) -->
                    ...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </main>
    </div>
  </div>
</template>
