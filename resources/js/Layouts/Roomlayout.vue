<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
    faEye, faPenToSquare, faTrash, faPlus,
    faSearch, faChevronLeft, faChevronRight
} from '@fortawesome/free-solid-svg-icons';

import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Sidebarsearch from '@/Components/RoomModals/Sidebarsearch.vue';
import EditRoomModal from '@/Components/RoomModals/EditRoomModal.vue';
import AddRoomModal from '@/Components/RoomModals/AddRoomModal.vue';
import ToastContainer from '@/Components/MessageFunction.vue';

const { props } = usePage();

/* ------------------------------------------------------------------- */
/* --- Icon Mapping --- */
/* ------------------------------------------------------------------- */
const icons = {
    eye: faEye,
    edit: faPenToSquare,
    delete: faTrash,
    plus: faPlus,
    search: faSearch,
    chevronLeft: faChevronLeft,
    chevronRight: faChevronRight,
};

/* ------------------------------------------------------------------- */
/* --- State & Layout Visibility --- */
/* ------------------------------------------------------------------- */
const sidebarVisible = ref(true);
const searchSidebarVisible = ref(false);
const addModalVisible = ref(false);
const editModalVisible = ref(false);

const roomToEdit = ref({});
const roomToViewInSidebar = ref(null);

// --- SEARCH & PAGINATION STATE ---
const searchQuery = ref(props.filters?.search || '');
const currentPage = ref(props.rooms.current_page || 1);
const itemsPerPage = ref(props.filters?.perPage || 10);

// --- TOAST STATE ---
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedRoomName = ref('');

// --- DATA FROM BACKEND ---
const roomList = ref(props.rooms.data || []);
const pagination = ref(props.rooms);

// Watch for changes in props
watch(() => props.rooms, (newRooms) => {
    roomList.value = newRooms.data;
    pagination.value = newRooms;
    currentPage.value = newRooms.current_page;
}, { deep: true });

/* ------------------------------------------------------------------- */
/* --- Computed Properties --- */
/* ------------------------------------------------------------------- */
const totalRoomsDisplay = computed(() => pagination.value.total || 0);
const availableRoomsCount = computed(() => props.stats?.available || 0);
const occupiedRoomsCount = computed(() => props.stats?.occupied || 0);
const uniqueRoomTypesCount = computed(() => {
    const types = new Set(roomList.value.map(room => room.room_type?.type_name || room.room_type_id));
    return types.size;
});

/* ------------------------------------------------------------------- */
/* --- Functions --- */
/* ------------------------------------------------------------------- */
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value;
};

const closeSearchSidebar = () => {
    searchSidebarVisible.value = false;
    roomToViewInSidebar.value = null;
};

const openAddModal = () => {
    addModalVisible.value = true;
};
const closeAddModal = () => {
    addModalVisible.value = false;
};

const openEditModal = (room) => {
    roomToEdit.value = { ...room };
    editModalVisible.value = true;
};

const closeEditModal = () => {
    editModalVisible.value = false;
    roomToEdit.value = {};
};

const selectRoomForSidebar = (room) => {
    roomToViewInSidebar.value = room;
    searchSidebarVisible.value = true;
};

/* ------------------------------------------------------------------- */
/* --- Data Operations --- */
/* ------------------------------------------------------------------- */
const handleSearch = () => {
    router.get(route('rooms.index'), {
        search: searchQuery.value,
        perPage: itemsPerPage.value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};

const handlePerPageChange = () => {
    router.get(route('rooms.index'), {
        search: searchQuery.value,
        perPage: itemsPerPage.value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};

const handlePageChange = (page) => {
    router.get(route('rooms.index'), {
        search: searchQuery.value,
        perPage: itemsPerPage.value,
        page: page
    }, {
        preserveState: true,
        replace: true
    });
};

const handleAddRoom = (newRoomData) => {
    router.post(route('rooms.store'), newRoomData, {
        onSuccess: () => {
            showCreateSuccess.value = true;
            setTimeout(() => {
                showCreateSuccess.value = false;
                closeAddModal();
            }, 2000);
        },
        onError: (errors) => {
            alert('Error creating room. Please check the form.');
        }
    });
};

const handleRoomUpdate = (updatedRoomData) => {
    router.put(route('rooms.update', updatedRoomData.id), updatedRoomData, {
        onSuccess: () => {
            showEditSuccess.value = true;
            setTimeout(() => {
                showEditSuccess.value = false;
                closeEditModal();
            }, 2000);
        },
        onError: (errors) => {
            alert('Error updating room. Please check the form.');
        }
    });
};

const handleDeleteRoom = (id) => {
    if (confirm('Are you sure you want to delete this room?')) {
        router.delete(route('rooms.destroy', id), {
            onSuccess: () => {
                const room = roomList.value.find(r => r.id === id);
                deletedRoomName.value = room?.room_name || '';
                showDeleteSuccess.value = true;
                setTimeout(() => {
                    showDeleteSuccess.value = false;
                }, 2000);

                if (roomToViewInSidebar.value?.id === id) {
                    closeSearchSidebar();
                }
            }
        });
    }
};

const handleViewDetails = (room) => {
    selectRoomForSidebar(room);
};

const handleEditRoom = (room) => {
    openEditModal(room);
};
</script>

<template>
    <div class="flex pt-14 min-h-screen transition-all duration-300 bg-gray-200">
        <ToastContainer
            :showCreateSuccess="showCreateSuccess"
            :showEditSuccess="showEditSuccess"
            :showDeleteSuccess="showDeleteSuccess"
            :deletedRoomName="deletedRoomName"
        />

        <!-- Sidebar -->
        <aside
            :class="{
                'w-64': sidebarVisible,
                'w-0 overflow-hidden hidden sm:block': !sidebarVisible
            }"
            class="transition-all duration-300 flex-shrink-0 fixed h-full z-40"
        >
            <Sidebar v-show="sidebarVisible" class="h-full bg-white shadow-xl" />
        </aside>

        <!-- Main Content -->
        <div :class="{ 'sm:ml-64': sidebarVisible }" class="flex flex-col flex-1 overflow-hidden transition-all duration-300">
            <!-- Navbar -->
            <Navbar @toggle-sidebar="toggleSidebar" />

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 relative">
                <!-- Page Header -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#7A0C23]">Rooms Management</h3>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span>UPCEBU > ROOMS</span>
                    </div>
                </div>

                <!-- Search and Add Button Section -->
                <div class="flex justify-between items-center mb-6">
                    <!-- Search Input -->
                    <div class="relative w-full max-w-sm">
                        <input
                            type="text"
                            placeholder="SEARCH"
                            v-model="searchQuery"
                            @input="handleSearch"
                            class="pl-12 pr-4 py-2 w-full rounded-lg border-2 border-yellow-300 focus:outline-none focus:border-[#7A0C23] transition duration-150 bg-white"
                        />
                        <FontAwesomeIcon
                            :icon="icons.search"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                        />
                    </div>

                    <!-- Add Button -->
                    <div class="flex items-center space-x-2">
                        <button
                            @click="openAddModal"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transition duration-150 transform hover:scale-[1.02]"
                        >
                            <span class="hidden sm:inline">ADD ROOMS</span>
                            <FontAwesomeIcon :icon="icons.plus" class="w-4 h-4 sm:hidden" />
                        </button>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="bg-white p-6 rounded-xl shadow-xl overflow-x-auto">
                    <!-- Table Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Room List ({{ pagination.total }})</h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Items per page:</span>
                            <select
                                v-model="itemsPerPage"
                                @change="handlePerPageChange"
                                class="text-sm border border-gray-300 rounded px-2 py-1"
                            >
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <table class="min-w-full divide-y divide-yellow-600">
                        <thead class="bg-[#7A0C23]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Building</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">College</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-yellow-600">
                            <!-- No Results Row -->
                            <tr v-if="roomList.length === 0">
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No rooms found.
                                </td>
                            </tr>

                            <!-- Room Rows -->
                            <tr
                                v-for="room in roomList"
                                :key="room.id"
                                class="hover:bg-gray-200 transition duration-100"
                            >
                                <!-- ID -->
                                <td
                                    class="px-6 py-4 text-sm font-mono text-gray-500 cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    {{ room.id }}
                                </td>

                                <!-- Room Name and Code -->
                                <td
                                    class="px-6 py-4 text-sm font-medium text-gray-900 cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    {{ room.room_name }}
                                    <span class="text-xs text-gray-500 block">{{ room.room_code }}</span>
                                </td>

                                <!-- Building -->
                                <td
                                    class="px-6 py-4 text-sm text-gray-800 cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    {{ room.building?.building_name || 'N/A' }}
                                </td>

                                <!-- College -->
                                <td
                                    class="px-6 py-4 text-sm text-gray-800 cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    {{ room.college?.college_name || 'N/A' }}
                                </td>

                                <!-- Capacity -->
                                <td
                                    class="px-6 py-4 text-sm text-gray-800 text-center font-bold cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    {{ room.capacity }}
                                </td>

                                <!-- Location -->
                                <td
                                    class="px-6 py-4 text-sm text-gray-800 cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    {{ room.location || 'N/A' }}
                                </td>

                                <!-- Status -->
                                <td
                                    class="px-6 py-4 text-sm cursor-pointer"
                                    @click="selectRoomForSidebar(room)"
                                >
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800': room.status === 'available',
                                            'bg-red-100 text-red-800': room.status === 'occupied',
                                            'bg-yellow-100 text-yellow-800': room.status === 'maintenance',
                                            'bg-gray-100 text-gray-800': room.status === 'closed'
                                        }"
                                        class="px-2 py-1 rounded-full text-xs font-medium"
                                    >
                                        {{ room.status }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-sm font-medium text-center">
                                    <div class="flex justify-center space-x-3">
                                        <!-- View Button -->
                                        <button
                                            @click="handleViewDetails(room)"
                                            title="View Details"
                                            class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition"
                                        >
                                            <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                        </button>

                                        <!-- Edit Button -->
                                        <button
                                            @click="handleEditRoom(room)"
                                            title="Edit Room"
                                            class="text-green-600 hover:text-green-800 transform hover:scale-110 transition"
                                        >
                                            <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                        </button>

                                        <!-- Delete Button -->
                                        <button
                                            @click="handleDeleteRoom(room.id)"
                                            title="Delete Room"
                                            class="text-red-600 hover:text-red-800 transform hover:scale-110 transition"
                                        >
                                            <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div
                        v-if="pagination.last_page > 1"
                        class="flex flex-col sm:flex-row items-center justify-between pt-4 border-t border-gray-200 mt-4"
                    >
                        <!-- Showing Entries -->
                        <div class="text-sm text-gray-600 mb-2 sm:mb-0">
                            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
                        </div>

                        <!-- Page Navigation -->
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            <button
                                @click="handlePageChange(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                :class="[
                                    'flex items-center px-3 py-1.5 rounded border text-sm',
                                    pagination.current_page === 1
                                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                        : 'bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                <FontAwesomeIcon :icon="icons.chevronLeft" class="h-3 w-3 mr-1" />
                                Previous
                            </button>

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                <button
                                    v-for="page in pagination.links.slice(1, -1)"
                                    :key="page.label"
                                    @click="handlePageChange(page.label)"
                                    :class="[
                                        'px-3 py-1.5 rounded border text-sm',
                                        page.active
                                            ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    ]"
                                    v-html="page.label"
                                ></button>
                            </div>

                            <!-- Next Button -->
                            <button
                                @click="handlePageChange(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page"
                                :class="[
                                    'flex items-center px-3 py-1.5 rounded border text-sm',
                                    pagination.current_page === pagination.last_page
                                        ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                        : 'bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                                ]"
                            >
                                Next
                                <FontAwesomeIcon :icon="icons.chevronRight" class="h-3 w-3 ml-1" />
                            </button>
                        </div>

                        <!-- Page Info -->
                        <div class="text-sm text-gray-600 mt-2 sm:mt-0">
                            Page {{ pagination.current_page }} of {{ pagination.last_page }}
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Sidebar Search Component -->
        <div
            :class="{
                'translate-x-0': searchSidebarVisible,
                'translate-x-full': !searchSidebarVisible
            }"
            class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl transition-transform duration-300 z-50"
        >
            <Sidebarsearch
                v-if="searchSidebarVisible"
                :room-data="roomToViewInSidebar"
                :all-rooms="roomList"
                @select-room="selectRoomForSidebar"
                @close-search="closeSearchSidebar"
            />
        </div>

        <!-- Add Room Modal -->
        <AddRoomModal
            :isVisible="addModalVisible"
            @close="closeAddModal"
            @save="handleAddRoom"
            :buildings="props.buildings"
            :colleges="props.colleges"
            :departments="props.departments"
            :room-types="props.room_types"
            :users="props.users"
        />

        <!-- Edit Room Modal -->
        <EditRoomModal
            :isVisible="editModalVisible"
            :roomData="roomToEdit"
            @close="closeEditModal"
            @save="handleRoomUpdate"
            :buildings="props.buildings"
            :colleges="props.colleges"
            :departments="props.departments"
            :room-types="props.room_types"
            :users="props.users"
        />
    </div>
</template>

<style scoped>
/* Custom Styles */
.bg-maroon-dark {
    background-color: #7B0025;
}

.border-maroon-light {
    border-color: #9C3857;
}

.hover\:bg-maroon-light,
.bg-maroon-light {
    background-color: #9C3857;
}

.flex-col .relative.z-30 {
    position: sticky;
    top: 0;
}

/* Button hover effects */
button:not(:disabled):hover {
    transform: translateY(-1px);
    transition: transform 0.2s;
}

/* Table row hover effect */
tr:hover {
    background-color: #f3f4f6;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .w-400 {
        width: 100%;
    }

    .space-x-3 > * + * {
        margin-left: 0.5rem;
    }
}

/* Scrollbar styling for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Smooth transitions */
.transition {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.duration-100 {
    transition-duration: 100ms;
}

.duration-150 {
    transition-duration: 150ms;
}

.duration-300 {
    transition-duration: 300ms;
}

/* Transform utilities */
.transform {
    transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

/* Cursor pointer for clickable elements */
.cursor-pointer {
    cursor: pointer;
}

/* Focus outlines for accessibility */
input:focus,
button:focus,
select:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}
</style>
