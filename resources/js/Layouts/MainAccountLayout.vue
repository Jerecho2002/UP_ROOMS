<script setup>
import { ref, onMounted, onUnmounted, computed, watchEffect } from "vue";
import Navbar from "@/Components/Navbar.vue"; // Navbar component import
import Sidebar from "@/Components/Sidebar.vue"; // Sidebar component import

// ===== LAYOUT LOGIC =====

// Sidebar open state for mobile overlay menu (default closed)
const sidebarOpen = ref(false);

// Sidebar forced open state for desktop layout (default visible)
const sidebarForcedOpen = ref(true);

// Reactive state tracking window width for responsiveness
const windowWidth = ref(window.innerWidth);

/**
 * Toggles sidebar visibility.
 * - On desktop: toggles sidebar forced open/close state (collapsible sidebar)
 * - On mobile: toggles overlay sidebar open/close state
 */
const toggleSidebar = () => {
  if (isDesktop.value) {
    sidebarForcedOpen.value = !sidebarForcedOpen.value;
  } else {
    sidebarOpen.value = !sidebarOpen.value;
  }
};

/**
 * Computed property to check if current device width is desktop (>= 768px).
 */
const isDesktop = computed(() => windowWidth.value >= 768);

/**
 * Updates windowWidth reactive variable on resize.
 * Keeps track of window size for responsive sidebar behavior.
 */
const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth;
};

/**
 * Computed classes applied to the sidebar element for responsive behavior:
 * - Mobile: fixed, overlay with translate-x to show/hide sidebar
 * - Desktop: static positioning, width changes for collapse/expand
 */
const sidebarClasses = computed(() => {
  if (!isDesktop.value) {
    return [
      'fixed top-0 left-0 pt-14',
      sidebarOpen.value ? 'translate-x-0 shadow-xl' : '-translate-x-full',
    ];
  } else {
    return [
      'static pt-0',
      sidebarForcedOpen.value ? 'w-64' : 'w-0 overflow-hidden',
    ];
  }
});

/**
 * Watch effect to reset mobile sidebar state when switching to desktop.
 * Ensures mobile sidebar overlay is hidden on desktop.
 */
watchEffect(() => {
  if (isDesktop.value && sidebarOpen.value) {
    sidebarOpen.value = false;
  }
});

// Add window resize listener on mount
onMounted(() => {
  window.addEventListener('resize', updateWindowWidth);
});

// Remove window resize listener on unmount (cleanup)
onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth);
});

// ===== PAGE DATA (MOCK DATA FOR DEFAULT SLOT CONTENT) =====

// Define props for data counts and table data with default mock data
defineProps({
  totalAccounts: { type: Number, default: 120 },
  totalDepartments: { type: Number, default: 15 },
  totalColleges: { type: Number, default: 5 },
  totalRooms: { type: Number, default: 250 },
  tableData: {
    type: Array,
    default: () => [
      { id: 1, name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
      { id: 2, name: 'MARIA SANTOS', school: 'UNIVERSITY OF CEBU', age: 22, address: 'LAPU-LAPU CITY', room: 'ROOM 203', start: 'SEPTEMBER 01, 2025', end: 'JUNE 30, 2026' },
      { id: 3, name: 'KEN TANG', school: 'USC', age: 34, address: 'TALISAY CITY', room: 'ROOM 310', start: 'AUGUST 15, 2025', end: 'APRIL 10, 2026' },
      { id: 4, name: 'LEE CHEN', school: 'CEBU TECH', age: 25, address: 'MANDAUE CITY', room: 'ROOM 112', start: 'JULY 20, 2025', end: 'MAY 15, 2026' },
      { id: 5, name: 'RUSSELL EVAN', school: 'Velez College', age: 31, address: 'CEBU CITY', room: 'ROOM 201', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
    ],
  },
});
</script>

<template>
 <div class="flex pt-14 min-h-screen transition-all duration-300">
  

  
    <Navbar 
      @toggleSidebar="toggleSidebar" 
      :is-mobile-open="sidebarOpen"
      :is-desktop-open="sidebarForcedOpen"
      :is-desktop="isDesktop" 
    />
    
    <Sidebar :class="sidebarClasses" :sidebar-open="sidebarForcedOpen" />

    <transition name="fade">
      <div
        v-if="sidebarOpen && !isDesktop"
        class="fixed inset-0 bg-black bg-opacity-50 z-30"
        @click="sidebarOpen = false"
      ></div>
    </transition>

    <div class="flex-1 flex flex-col min-w-0">
      
      <main id="mainContent" class="flex-1 px-6 py-6 bg-gray-50 transition-all duration-300">
        
        <slot>
          <div class="text-xs text-gray-500 mb-4 flex justify-between items-center">
            <span class="hidden md:block">Dashboard</span>
            <span>UPCEBU &gt; Dashboard</span>
          </div>

          <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-cyan-500 text-white p-3 font-semibold">Total Accounts</div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">{{ totalAccounts }}</p>
              </div>
            </div>

            <div class="rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-purple-600 text-white p-3 font-semibold">Total Department</div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">{{ totalDepartments }}</p>
              </div>
            </div>

            <div class="rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-orange-500 text-white p-3 font-semibold">Total Colleges</div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">{{ totalColleges }}</p>
              </div>
            </div>

            <div class="rounded-xl text-center shadow-lg overflow-hidden">
              <div class="bg-red-500 text-white p-3 font-semibold">Total Rooms</div>
              <div class="bg-white p-3">
                <p class="text-3xl font-bold text-gray-800">{{ totalRooms }}</p>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto bg-white rounded-lg shadow-xl mb-6">
            <table class="min-w-full text-sm text-center border-collapse">
              <thead class="bg-[#800020] text-white">
                <tr>
                  <th class="px-4 py-3 font-semibold text-left">NAME</th>
                  <th class="px-4 py-3 font-semibold text-left hidden sm:table-cell">SCHOOL</th>
                  <th class="px-4 py-3 font-semibold hidden md:table-cell">AGE</th>
                  <th class="px-4 py-3 font-semibold hidden md:table-cell">ADDRESS</th>
                  <th class="px-4 py-3 font-semibold hidden md:table-cell">ROOM</th>
                  <th class="px-4 py-3 font-semibold hidden lg:table-cell">START</th>
                  <th class="px-4 py-3 font-semibold hidden lg:table-cell">END</th>
                  <th class="px-4 py-3 font-semibold">ACTION</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200">
                <tr v-if="tableData.length === 0">
                  <td colspan="8" class="px-4 py-6 text-gray-500 italic">No records found.</td>
                </tr>

                <tr
                  v-for="item in tableData"
                  :key="item.id"
                  class="odd:bg-white even:bg-gray-50 hover:bg-gray-100"
                >
                  <td class="px-4 py-3 text-left">{{ item.name }}</td>
                  <td class="px-4 py-3 text-left hidden sm:table-cell">{{ item.school }}</td>
                  <td class="px-4 py-3 hidden md:table-cell">{{ item.age }}</td>
                  <td class="px-4 py-3 hidden md:table-cell">{{ item.address }}</td>
                  <td class="px-4 py-3 hidden md:table-cell">{{ item.room }}</td>
                  <td class="px-4 py-3 hidden lg:table-cell">{{ item.start }}</td>
                  <td class="px-4 py-3 hidden lg:table-cell">{{ item.end }}</td>
                  <td class="px-4 py-3 space-x-2">
                    <a href="#" class="text-green-500 hover:text-green-700" title="Edit">✏️</a>
                    <a href="#" class="text-red-500 hover:text-red-700" title="Delete">🗑️</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </slot>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Fade transition for sidebar overlay */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Transition for the desktop sidebar width change. 
  The Sidebar component itself must apply the transition to the width property 
  to get a smooth collapse/expand effect.
*/
</style>