<script setup>
import { ref, onMounted, onUnmounted, computed, watchEffect } from "vue";
import { usePage, router } from '@inertiajs/vue3';
import Navbar from "@/Components/Navbar.vue";
import Sidebar from "@/Components/Sidebar.vue";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faEye,
    faPenToSquare,
    faTrash,
    faPlus,
    faSearch,
    faChevronLeft,
    faChevronRight
} from '@fortawesome/free-solid-svg-icons';

// --- ICON MAPPING ---
// Maps imported FontAwesome icons to a local object for template use.
const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    plus: faPlus,
    search: faSearch,
    prev: faChevronLeft, // For pagination
    next: faChevronRight // For pagination
};

// --- LAYOUT & SIDEBAR LOGIC ---

// State for mobile sidebar open/closed status.
const sidebarOpen = ref(false);
// State for desktop sidebar: true = forced open (visible), false = forced closed (hidden).
const sidebarForcedOpen = ref(true);
// Tracks window width for responsiveness.
const windowWidth = ref(window.innerWidth);

/**
 * Computed property to determine if the screen is desktop size (>= 768px).
 */
const isDesktop = computed(() => windowWidth.value >= 768);

/**
 * Toggles the sidebar state based on the screen size (mobile vs. desktop).
 */
const toggleSidebar = () => {
    if (isDesktop.value) {
        sidebarForcedOpen.value = !sidebarForcedOpen.value;
    } else {
        sidebarOpen.value = !sidebarOpen.value;
    }
};

/**
 * Updates the window width on resize event.
 */
const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
};

/**
 * Ensures the mobile sidebar is closed when transitioning to desktop view.
 */
watchEffect(() => {
    if (isDesktop.value && sidebarOpen.value) {
        sidebarOpen.value = false;
    }
});

// Setup and teardown of event listeners.
onMounted(() => {
    window.addEventListener('resize', updateWindowWidth);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateWindowWidth);
});

// --- INERTIA PROPS & DATA ACCESS ---

// Define dashboard count props passed from the controller.
defineProps({
    totalAccounts: { type: Number, default: 0 },
    totalDepartments: { type: Number, default: 0 },
    totalColleges: { type: Number, default: 0 },
    totalRooms: { type: Number, default: 0 },
    inventoryitems: { type: Object, required: true },
});

const page = usePage();

/**
 * Computed property to safely access the paginated 'rooms' data.
 */
const rooms = computed(() => page.props.rooms);
// const inventoryitems = computed(() => page.props.inventoryitems); // Already defined as a prop

// --- TABLE ACTION HANDLERS ---

// State to control the visibility of the details modal.
const isDetailsModalVisible = ref(false);
// State to hold the data of the room/schedule currently being viewed.
const currentViewedDetails = ref(null);

/**
 * Handles the click event for the 'View Details' (eye) icon.
 * Populates the modal with data and makes it visible.
 * @param {Object} room - The room object containing details.
 */
const handleViewDetails = (room) => {
    currentViewedDetails.value = room || null;
    isDetailsModalVisible.value = true;
};

/**
 * Handles the click event for the 'Edit Room' (pen) icon.
 * (Placeholder - This function should navigate to an edit form or open an edit modal).
 * @param {Object} room - The room object to edit.
 */
const handleEditRoom = (room) => {
    console.log("Editing room:", room.id);
    // Example: router.get(route('rooms.edit', room.id));
};

/**
 * Handles the click event for the 'Delete Room' (trash) icon.
 * (Placeholder - This function should prompt for confirmation and then delete).
 * @param {number} id - The ID of the room to delete.
 */
const handleDeleteRoom = (id) => {
    if (confirm("Are you sure you want to delete this room record?")) {
        console.log("Deleting room with ID:", id);
        // Example: router.delete(route('rooms.destroy', id));
    }
};

/**
 * Function to close the details modal and clear the viewed data.
 */
const closeDetailsModal = () => {
    isDetailsModalVisible.value = false;
    currentViewedDetails.value = null;
};

/**
 * Handles navigation for pagination links using Inertia.
 * @param {string} url - The URL to visit.
 */
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
    <div class="relative min-h-screen">
        <Navbar @toggleSidebar="toggleSidebar" :is-mobile-open="sidebarOpen" :is-desktop-open="sidebarForcedOpen"
            :is-desktop="isDesktop" />

        <Sidebar
            :class="['fixed top-14 left-0 h-[calc(100vh-3.5rem)] z-20 transition-all duration-300 bg-white shadow-lg',
                // Desktop Sidebar positioning
                isDesktop ? (sidebarForcedOpen ? 'w-64' : 'w-0 overflow-hidden') :
                // Mobile Sidebar positioning
                (sidebarOpen ? 'w-64 translate-x-0' : '-translate-x-full w-64')]"
            :sidebar-open="sidebarForcedOpen" />

        <transition name="fade">
            <div v-if="sidebarOpen && !isDesktop" class="fixed inset-0 bg-black bg-opacity-50 z-30"
                @click="sidebarOpen = false"></div>
        </transition>

        <div :class="['min-h-screen transition-all duration-300',
            // Content margin based on sidebar state
            isDesktop ? (sidebarForcedOpen ? 'ml-64' : 'ml-0') : 'ml-0']">

            <main id="mainContent" class="flex-1 px-6 py-6 bg-gray-200 pt-20">
                <slot>
                    <div class=" text-gray-500 mb-4 flex justify-between items-center">
                        <h1 class="hidden md:block text-[#7A0C23] font-bold text-2xl">Dashboard</h1>
                        <span>UPCEBU &gt; Dashboard</span>
                    </div>

                    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-xl text-center shadow-lg overflow-hidden">
                            <div class="bg-yellow-500 text-white p-3 font-semibold">Total Accounts</div>
                            <div class="bg-white p-3">
                                <p class="text-3xl font-bold text-gray-800">{{ totalAccounts }}</p>
                            </div>
                        </div>

                        <div class="rounded-xl text-center shadow-lg overflow-hidden">
                            <div class="bg-green-600 text-white p-3 font-semibold">Total Department</div>
                            <div class="bg-white p-3">
                                <p class="text-3xl font-bold text-gray-800">{{ totalDepartments }}</p>
                            </div>
                        </div>

                        <div class="rounded-xl text-center shadow-lg overflow-hidden">
                            <div class="bg-[#800020] text-white p-3 font-semibold">Total Colleges</div>
                            <div class="bg-white p-3">
                                <p class="text-3xl font-bold text-gray-800">{{ totalColleges }}</p>
                            </div>
                        </div>

                        <div class="rounded-xl text-center shadow-lg overflow-hidden">
                            <div class="bg-blue-500 text-white p-3 font-semibold">Total Rooms</div>
                            <div class="bg-white p-3">
                                <p class="text-3xl font-bold text-gray-800">{{ totalRooms }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-lg shadow-xl mb-6">
                        <table class="min-w-full text-sm text-center border-collapse">
                            <thead class="bg-[#800020] text-white">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-left">SUBJECT</th>
                                    <th class="px-4 py-3 font-semibold text-left hidden sm:table-cell">COLLEGE</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell">TIME</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell">DATE</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell">FACULTY</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell">ROOM</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell">BUILDING</th>
                                    <th class="px-4 py-3 font-semibold">ACTION</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-yellow-600">
                                <tr v-if="!rooms || rooms.data.length === 0">
                                    <td colspan="8" class="px-4 py-6 text-gray-500 italic">No room records found.</td>
                                </tr>

                                <tr v-for="room in rooms.data" :key="room.id" class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                    <td class="px-4 py-3 text-left font-medium">{{ room.room_name || 'N/A' }}</td>
                                    <td class="px-4 py-3 text-left hidden sm:table-cell">{{ room.college?.college_name || 'N/A' }}</td>

                                    <td class="px-4 py-3 hidden md:table-cell">{{ room.user_account?.username || 'N/A' }}</td>
                                    <td class="px-4 py-3 hidden md:table-cell">{{ room.location ?? "N/A" }}</td>


                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button @click="handleViewDetails(room)" title="View Details"
                                                class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition p-1">
                                                <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                            </button>
                                            <button @click="handleEditRoom(room)" title="Edit Room"
                                                class="text-green-600 hover:text-green-800 transform hover:scale-110 transition p-1">
                                                <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                            </button>
                                            <button @click="handleDeleteRoom(room.id)" title="Delete Room"
                                                class="text-red-600 hover:text-red-800 transform hover:scale-110 transition p-1">
                                                <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2 flex justify-end" v-if="rooms && rooms.links">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4
                                    bg-gray-100 border border-gray-300 rounded-md px-3 py-1">
                            <p class="text-xs sm:text-sm border-r border-gray-300 px-3" v-if="rooms.from">
                                {{ rooms.from }}-{{ rooms.to }} of {{ rooms.total }}
                            </p>
                            <div class="flex space-x-1">
                                <span v-for="link in rooms.links" :key="link.label">
                                    <button
                                        v-if="link.url"
                                        @click="goToPage(link.url)"
                                        class="p-1 text-xs sm:text-sm rounded transition"
                                        :class="{
                                            'text-gray-600 hover:bg-gray-200': link.url && !link.active,
                                            'bg-blue-600 text-white font-bold hover:bg-blue-700': link.active,
                                            'text-gray-400 cursor-not-allowed': !link.url
                                        }">
                                        <FontAwesomeIcon v-if="link.label.includes('Previous')" :icon="icons.prev" class="h-3 w-3" />
                                        <FontAwesomeIcon v-else-if="link.label.includes('Next')" :icon="icons.next" class="h-3 w-3" />
                                        <span class="px-1" v-else v-html="link.label"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </slot>
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
                                — No schedules found for this room —
                            </div>

                            <div v-else class="max-h-60 overflow-y-auto pr-2">
                                <div v-for="sched in currentViewedDetails.schedules" :key="sched.id"
                                    class="mb-3 p-3 border rounded bg-gray-50">
                                    <div class="text-sm"><strong>CFIC ID:</strong> {{ sched.cfic_id || 'N/A' }}</div>
                                    <div class="text-sm"><strong>Course:</strong> {{ sched.course_name || 'N/A' }}</div>
                                    <div class="text-sm"><strong>Day:</strong> {{ sched.day || 'N/A' }}</div>
                                    <div class="text-sm"><strong>Time:</strong> {{ sched.start_time || 'N/A' }} — {{ sched.end_time || 'N/A' }}</div>
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
