<script setup>
import { ref } from 'vue'
// NOTE: Assuming '@/Components/Navbar.vue' and '@/Components/Sidebar.vue' are defined.
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

    <!-- MAIN LAYOUT Container -->
    <div class="flex w-full min-h-screen transition-all duration-300">
      
      <!-- SIDEBAR -->
      <!-- Sidebar visibility controlled by 'sidebarVisible' reactive state -->
      <!-- NOTE: We assume Sidebar has a fixed position and a width (e.g., w-64) -->
      <Sidebar v-show="sidebarVisible" class="fixed top-14 left-0 h-full z-20 w-64" />

      <!-- PAGE CONTENT -->
      <!-- The main content dynamically changes its margin (ml-64) to shift when the sidebar is visible, 
           and uses the transition class for a smooth effect. -->
      <main
        id="mainContent"
        :class="{'lg:ml-64': sidebarVisible}"
        class="flex-1 mt-14 px-6 py-6 bg-gray-50 transition-all duration-300 ease-in-out"
      >
        
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
              class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md shadow transition duration-150"
            >
              Add
            </button>
          </div>
        </div>

        <!-- Building Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          
          <!-- Make table horizontally scrollable on small screens -->
          <div class="overflow-x-auto">
            
            <table class="min-w-full text-sm text-gray-800">
              <thead class="bg-[#7A0C23] text-white">
                <tr>
                  <!-- Table Headers -->
                  <th class="px-6 py-3 text-left font-semibold">Name</th>
                  <th class="px-6 py-3 text-left font-semibold">Address</th>
                  <th class="px-6 py-3 text-center font-semibold">Total Space</th>
                  <th class="px-6 py-3 text-center font-semibold">Lift</th>
                  <th class="px-6 py-3 text-center font-semibold">Parking</th>
                  <th class="px-6 py-3 text-center font-semibold">Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Loop over buildings array to display each building -->
                <tr
                  v-for="b in buildings"
                  :key="b.id"
                  class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 border-b border-gray-200"
                >
                  <td class="px-6 py-3 font-medium">{{ b.name }}</td>
                  <td class="px-6 py-3 text-gray-600">{{ b.address }}</td>
                  <td class="px-6 py-3 text-center">{{ b.total_space }}</td>
                  <td class="px-6 py-3 text-center">{{ b.lift }}</td>
                  <td class="px-6 py-3 text-center">
                    <!-- Display 'Yes' or 'No' based on boolean parking value -->
                    <span :class="b.parking ? 'text-green-600 font-semibold' : 'text-red-500'">
                      {{ b.parking ? 'Yes' : 'No' }}
                    </span>
                  </td>
                  <td class="px-6 py-3 text-center">
                    <!-- Placeholder for future action buttons (edit/delete) -->
                    <div class="flex justify-center space-x-2">
                        <button class="text-blue-500 hover:text-blue-700 text-lg">
                            <!-- Edit Icon (using a simple SVG as a placeholder) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-7.586 11l-2 2h4l9.293-9.293-2.828-2.828L5 14.004v-2.004H3v4h4l-2 2H3v-2z" />
                            </svg>
                        </button>
                        <button class="text-red-500 hover:text-red-700 text-lg">
                            <!-- Delete Icon (using a simple SVG as a placeholder) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 11a1 1 0 10-2 0v5a1 1 0 102 0v-5zm6-1a1 1 0 00-1 1v5a1 1 0 102 0v-5a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                  </td>
                </tr>
                <!-- Fallback for no data -->
                <tr v-if="buildings.length === 0">
                    <td colspan="6" class="text-center py-6 text-gray-500">No building data available.</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </main>
    </div>
  </div>
</template>
