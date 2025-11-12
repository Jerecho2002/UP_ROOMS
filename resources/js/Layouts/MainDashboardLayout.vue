<script setup>
import { ref, onMounted, onUnmounted, computed, watchEffect } from "vue";
import { usePage, router } from '@inertiajs/vue3';
import Navbar from "@/Components/Navbar.vue";
import Sidebar from "@/Components/Sidebar.vue";

// 1. --- Font Awesome Imports & Setup (Required for icons to display) ---
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash, faPlus, faSearch } from '@fortawesome/free-solid-svg-icons'; // Added faSearch

// Mapping icons for use in the template
const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    plus: faPlus,
    search: faSearch,
};
// ----------------------------------------


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
    totalColleges: { type : Number, default: 5 },
    totalRooms: { type: Number, default: 250 },
});

const page = usePage();

/**
 * Computed property to safely access the 'data' array from the Inertia 'rooms' prop.
 * Assumes page.props.rooms is an object containing { data: [] }.
 * Defaults to an empty array if not found.
 */
const rooms = computed(() => page.props.rooms);
const products = computed(() => page.props.products);


// ===== SEARCH & FILTER LOGIC (NEW) =====
const searchTerm = ref('');

/**
 * Filters the rooms based on the search term (case-insensitive across relevant fields).
 */
const filteredRooms = computed(() => {
    if (!searchTerm.value) {
        return rooms.value;
    }

    const lowerCaseSearch = searchTerm.value.toLowerCase();

    return rooms.value.filter(room => {
        const username = room.user_account?.username?.toLowerCase() || '';
        const collegeName = room.college?.college_name?.toLowerCase() || '';
        const location = room.location?.toLowerCase() || '';
        const roomName = room.room_name?.toLowerCase() || '';

        return username.includes(lowerCaseSearch) ||
               collegeName.includes(lowerCaseSearch) ||
               location.includes(lowerCaseSearch) ||
               roomName.includes(lowerCaseSearch);
    });
});


// ===== DETAIL VIEW & ACTION LOGIC (UPDATED) =====

// State to control the visibility of the primary details modal
const isDetailsModalVisible = ref(false);

// State to control the visibility of the edit modal
const isEditModalVisible = ref(false);

// State to control the visibility of the delete confirmation modal
const isDeleteModalVisible = ref(false);

// State to hold the data of the room/schedule currently being viewed/edited/deleted
const currentViewedDetails = ref(null);

/**
 * Handles the click event for the 'View Details' eye icon.
 * @param {Object} room - The room object.
 */
const handleViewDetails = (room) => {
    currentViewedDetails.value = room || null;
    isDetailsModalVisible.value = true;
    isEditModalVisible.value = false;
    isDeleteModalVisible.value = false;
};

/**
 * Handles the click event for the 'Edit Room' icon.
 * @param {Object} room - The room object.
 */
const handleEditRoom = (room) => {
    currentViewedDetails.value = room || null;
    isEditModalVisible.value = true;
    isDetailsModalVisible.value = false;
    isDeleteModalVisible.value = false;
    // In a real app, you'd load form data here
    console.log('Opening Edit Modal for:', room.room_name);
};

/**
 * Handles the click event for the 'Delete Room' icon.
 * @param {Object} room - The room object.
 */
const handleDeleteRoom = (room) => {
    currentViewedDetails.value = room || null;
    isDeleteModalVisible.value = true;
    isDetailsModalVisible.value = false;
    isEditModalVisible.value = false;
    console.log('Opening Delete Confirmation for:', room.room_name);
};

// Functions to close modals
const closeDetailsModal = () => {
    isDetailsModalVisible.value = false;
    currentViewedDetails.value = null;
};

const closeEditModal = () => {
    isEditModalVisible.value = false;
    currentViewedDetails.value = null;
};

const closeDeleteModal = () => {
    isDeleteModalVisible.value = false;
    currentViewedDetails.value = null;
};

// Placeholder action handlers for demonstration
const confirmEditAction = () => {
    console.log('Confirmed Edit for:', currentViewedDetails.value.room_name);
    // TODO: Implement actual Inertia form submission for update
    closeEditModal();
};

const confirmDeleteAction = () => {
    console.log('Confirmed Delete for:', currentViewedDetails.value.room_name);
    // TODO: Implement actual Inertia form submission for delete
    closeDeleteModal();
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
  <pre>{{ products }}</pre>
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

                    <div class="mb-6 flex">
                        <div class="relative w-full max-w-sm">
                            <input type="text" v-model="searchTerm" placeholder="Search by Name, School, Location, or Room..."
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-[#800020] focus:border-[#800020]">
                            <FontAwesomeIcon :icon="icons.search"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                        </div>
                    </div>

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

                            <tbody class="divide-y divide-gray-200">
                                <tr v-if="filteredRooms.length === 0">
                                    <td colspan="5" class="px-4 py-6 text-gray-500 italic">No room records found.</td>
                                </tr>
                                <tr v-for="room in filteredRooms" :key="room.id" class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                    <td class="px-4 py-3 text-left">{{ room.user_account?.username || 'N/A' }}</td>
                                    <td class="px-4 py-3 text-left hidden sm:table-cell">{{ room.college?.college_name || 'N/A' }}</td>
                                    <td class="px-4 py-3 hidden md:table-cell">{{ room.location ?? "N/A" }}</td>
                                    <td class="px-4 py-3 hidden md:table-cell">{{ room.room_name || 'N/A' }}</td>
                                    <td class="px-4 py-3 space-x-2 whitespace-nowrap">
                                        <button @click="handleViewDetails(room)" title="View Details"
                                            class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                            <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                        </button>
                                        <button @click="handleEditRoom(room)" title="Edit Room"
                                            class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                            <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                        </button>
                                        <button @click="handleDeleteRoom(room)" title="Delete Room"
                                            class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                            <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </slot>
            </main>
        </div>

        <transition name="fade">
            <div v-if="isDetailsModalVisible"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                @click.self="closeDetailsModal"> 
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

                            <div v-else class="max-h-64 overflow-y-auto border p-2 rounded custom-scrollbar">
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

        <transition name="fade">
            <div v-if="isEditModalVisible"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                @click.self="closeEditModal"> 
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6 relative">
                    <h3 class="text-xl font-bold text-green-700 mb-4 border-b pb-2">Edit Room/Schedule</h3>

                    <div v-if="currentViewedDetails" class="space-y-3">
                        <p class="text-gray-700">
                            You are editing the details for **{{ currentViewedDetails.room_name }}** (College: {{ currentViewedDetails.college?.college_name || 'N/A' }}).
                        </p>
                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200 text-sm">
                            <p class="font-semibold text-yellow-800">TODO: Insert actual Edit Form here.</p>
                            <p class="text-yellow-700">For demonstration, this acts as a confirmation.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="closeEditModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                            Cancel
                        </button>
                        <button @click="confirmEditAction"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="isDeleteModalVisible"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                @click.self="closeDeleteModal"> 
                <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 relative">
                    <h3 class="text-xl font-bold text-red-700 mb-4 border-b pb-2">Confirm Deletion</h3>

                    <div v-if="currentViewedDetails" class="space-y-4">
                        <p class="text-lg text-gray-700">
                            Are you sure you want to delete the record for **{{ currentViewedDetails.room_name }}**?
                        </p>
                        <p class="text-sm text-red-600 bg-red-50 p-3 rounded border border-red-200">
                            This action cannot be undone.
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="closeDeleteModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition duration-150">
                            Cancel
                        </button>
                        <button @click="confirmDeleteAction"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150">
                            Delete
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

/* Optional: Custom scrollbar styling for a cleaner look in Webkit browsers (Chrome, Safari) */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 10px;
    border: 2px solid #f9f9f9;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f9f9f9;
}
</style>