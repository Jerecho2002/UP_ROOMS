<script setup>
import { ref, onMounted, onUnmounted, computed, watchEffect } from "vue";
import { usePage, router } from '@inertiajs/vue3';
import Navbar from "@/Components/Navbar.vue";
import Sidebar from "@/Components/Sidebar.vue";

// ===== LAYOUT LOGIC (UNCHANGED) =====

const sidebarOpen = ref(false);
const sidebarForcedOpen = ref(true);
const windowWidth = ref(window.innerWidth);

const toggleSidebar = () => {
  if (isDesktop.value) {
    sidebarForcedOpen.value = !sidebarForcedOpen.value;
  } else {
    sidebarOpen.value = !sidebarOpen.value;
  }
};

const isDesktop = computed(() => windowWidth.value >= 768);

const updateWindowWidth = () => {
  windowWidth.value = window.innerWidth;
};

// Removed sidebarClasses computed property as it's directly in the template now

watchEffect(() => {
  if (isDesktop.value && sidebarOpen.value) {
    sidebarOpen.value = false;
  }
});

onMounted(() => {
  window.addEventListener('resize', updateWindowWidth);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth);
});

// ===== PAGE DATA (MOCK DATA & INERTIA PROPS) =====

// Define props for data counts
defineProps({
  totalAccounts: { type: Number, default: 120 },
  totalDepartments: { type: Number, default: 15 },
  totalColleges: { type: Number, default: 5 },
  totalRooms: { type: Number, default: 250 },
});

const page = usePage();

/**
 * Computed property to safely access the 'data' array from the Inertia 'rooms' prop.
 * Assumes page.props.rooms is an object containing { data: [] }.
 * Defaults to an empty array if not found.
 */
const rooms = computed(() => page.props.rooms);
const inventoryitems = computed(() => page.props.inventoryitems);


// ===== DETAIL VIEW LOGIC (for the eye icon) =====

// State to control the visibility of the details modal
const isDetailsModalVisible = ref(false);

// State to hold the data of the room/schedule currently being viewed
const currentViewedDetails = ref(null);

/**
 * Handles the click event for the 'View Details' eye icon.
 * Takes the 'room' object (from rooms.data) and formats/displays its data.
 * @param {Object} room - The room object containing user, college, room, and schedule data.
 */
const handleViewDetails = (room) => {
  // store the whole room (or pick only the fields you want)
  currentViewedDetails.value = room || null;
  isDetailsModalVisible.value = true;
};

// Function to close the modal
const closeDetailsModal = () => {
  isDetailsModalVisible.value = false;
  currentViewedDetails.value = null;
};

const goToPage = (url) => {
  if (!url) return;
  router.visit(url, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
  });
};
</script>

<template>
  <!-- <div v-for="inv in inventoryitems.suppliers">
    <pre>{{ inv.address }}</pre>
  </div> -->
  
  <div class="relative min-h-screen">
    <Navbar @toggleSidebar="toggleSidebar" :is-mobile-open="sidebarOpen" :is-desktop-open="sidebarForcedOpen"
      :is-desktop="isDesktop" />

    <Sidebar
      :class="['fixed top-14 left-0 h-[calc(100vh-3.5rem)] z-20 transition-all duration-300 bg-white shadow-lg',
        isDesktop ? (sidebarForcedOpen ? 'w-64' : 'w-0 overflow-hidden') : (sidebarOpen ? 'w-64 translate-x-0' : '-translate-x-full w-64')]"
      :sidebar-open="sidebarForcedOpen" />

    <transition name="fade">
      <div v-if="sidebarOpen && !isDesktop" class="fixed inset-0 bg-black bg-opacity-50 z-30"
        @click="sidebarOpen = false"></div>
    </transition>

    <div :class="['min-h-screen transition-all duration-300',
      isDesktop ? (sidebarForcedOpen ? 'ml-64' : 'ml-0') : 'ml-0']">
      <main id="mainContent" class="flex-1 px-6 py-6 bg-gray-50 pt-20">
        <slot>
          <div class=" text-gray-500 mb-4 flex justify-between items-center">
            <h1 class="hidden md:block text-[#7A0C23]">Dashboard</h1>
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
                  <th class="px-4 py-3 font-semibold hidden md:table-cell">LOCATION</th>
                  <th class="px-4 py-3 font-semibold hidden md:table-cell">ROOM</th>
                  <th class="px-4 py-3 font-semibold">ACTION</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200">
                <tr v-if="rooms.length === 0">
                  <td colspan="5" class="px-4 py-6 text-gray-500 italic">No room records found.</td>
                </tr>
                <tr v-for="room in rooms.data" :key="room.id" class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                  <td class="px-4 py-3 text-left">{{ room.user_account?.username || 'N/A' }}</td>
                  <td class="px-4 py-3 text-left hidden sm:table-cell">{{ room.college?.college_name || 'N/A' }}</td>
                  <td class="px-4 py-3 hidden md:table-cell">{{ room.location ?? "N/A" }}</td>
                  <td class="px-4 py-3 hidden md:table-cell">{{ room.room_name || 'N/A' }}</td>
                  <td class="px-4 py-3 space-x-2">
                    <button @click="handleViewDetails(room)" class="text-green-500 hover:text-green-700"
                      title="View">👁️</button>
                    <a href="#" class="text-green-500 hover:text-green-700" title="Edit">✏️</a>
                    <a href="#" class="text-red-500 hover:text-red-700" title="Delete">🗑️</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </slot>
        <!-- Pagination -->
        <div class="mt-2 flex justify-end">
          <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 
                bg-gray-100 border border-gray-300 rounded-md px-3 py-1">
            <p class="text-xs sm:text-sm border-r border-gray-300 px-3">
              {{ rooms.from }}-{{ rooms.to }} of
              {{ rooms.total }}
            </p>
            <div>
              <span v-for="link in rooms.links" :key="link.label">
                <span v-if="link.url" @click="goToPage(link.url)" class="cursor-pointer p-1 text-xs sm:text-sm" :class="{
                  'text-gray-600 hover:underline': link.url,
                  'text-blue-600 font-bold': link.active
                }">
                  <!-- Render label or icon -->
                  <i v-if="link.label.includes('Previous')" class="fa-solid fa-chevron-left"></i>
                  <i v-else-if="link.label.includes('Next')" class="fa-solid fa-chevron-right"></i>
                  <span class="px-1" v-else>{{ link.label }}</span>
                </span>
              </span>
            </div>
          </div>
        </div>
      </main>
    </div>

    <transition name="fade">
      <div v-if="isDetailsModalVisible"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6 relative">
          <h3 class="text-xl font-bold text-[#800020] mb-4 border-b pb-2">Room/Schedule Details</h3>

          <div v-if="currentViewedDetails" class="space-y-3">
            <div class="flex justify-between items-center text-gray-700">
              <span class="font-medium text-gray-900">Room:</span>
              <span>{{ currentViewedDetails.room_name }}</span>
            </div>

            <div class="flex justify-between items-center text-gray-700">
              <span class="font-medium text-gray-900">Location:</span>
              <span>{{ currentViewedDetails.location || 'N/A' }}</span>
            </div>

            <div class="flex justify-between items-center text-gray-700">
              <span class="font-medium text-gray-900">College:</span>
              <span>{{ currentViewedDetails.college?.college_name || currentViewedDetails.college || 'N/A' }}</span>
            </div>

            <div class="pt-2">
              <div class="font-medium text-gray-900 mb-2">Schedules:</div>

              <div v-if="(currentViewedDetails.schedules || []).length === 0" class="text-sm text-gray-500">
                — No schedules —
              </div>

              <div v-else>
                <div v-for="sched in currentViewedDetails.schedules" :key="sched.id"
                  class="mb-3 p-3 border rounded bg-gray-50">
                  <div class="text-sm"><strong>CFIC ID:</strong> {{ sched.cfic_id || 'N/A' }}</div>
                  <div class="text-sm"><strong>Course:</strong> {{ sched.course_name || 'N/A' }}</div>
                  <div class="text-sm"><strong>Day:</strong> {{ sched.day || 'N/A' }}</div>
                  <div class="text-sm"><strong>Time:</strong> {{ sched.start_time || 'N/A' }} — {{ sched.end_time ||
                    'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button @click="closeDetailsModal"
              class="px-4 py-2 bg-[#800020] text-white rounded-lg hover:bg-red-800 transition duration-150">
              Close
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
/* Fade transition for sidebar overlay and details modal */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>