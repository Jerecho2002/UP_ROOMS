<script setup>
import { ref, computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faPenToSquare, faTrash, faPlus, faSearch, faChevronLeft, faChevronRight } from '@fortawesome/free-solid-svg-icons';

// Assuming these imports are correct for your project structure
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Sidebarsearch from '@/Components/RoomModals/Sidebarsearch.vue';
import EditRoomModal from '@/Components/RoomModals/EditRoomModal.vue';
import AddRoomModal from '@/Components/RoomModals/AddRoomModal.vue';
import ToastContainer from '@/Components/MessageFunction.vue';


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
/* --- State & Layout Visibility (Controller State) --- */
/* ------------------------------------------------------------------- */

const sidebarVisible = ref(true);
const searchSidebarVisible = ref(false);
const addModalVisible = ref(false);
const editModalVisible = ref(false);

const roomToEdit = ref({});
const roomToViewInSidebar = ref(null); // The selected room object for the sidebar

// --- SEARCH STATE ---
const searchQuery = ref('');

// --- PAGINATION STATE ---
const currentPage = ref(1);
const itemsPerPage = ref(10);

// --- TOAST STATE (These states are now passed to ToastContainer) ---
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedRoomName = ref('');


// MOCK DATA (Extended to match Sidebarsearch template)
const roomList = ref([
    { id: 410, room: 'UG 114', building: 'UG Building', college: 'CCAD', capacity: 35, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [
        { name: 'English 3 A', time: '9:00AM - 12:00PM T', college: 'CCAD', isAvailable: false },
        { name: 'FA 12 B', time: '1:30PM - 4:30PM MW', college: 'CCAD', isAvailable: false },
        { name: 'Available Slot', time: '4:30PM - 9:00PM Daily', college: 'N/A', isAvailable: true },
    ]},
    { id: 205, room: 'LH 201', building: 'Lecture Hall', college: 'General', capacity: 150, location: 'Center', roomType: 'Lecture Hall', description: 'Main lecture hall', department: 'All', floorNumber: 2, schedules: [] },
    { id: 101, room: 'A101', building: 'CED', college: 'COE', capacity: 35, location: '1st Floor', roomType: 'Classroom', description: 'Standard classroom', department: 'Engineering', floorNumber: 1, schedules: [] },
    { id: 312, room: 'C312', building: 'Main', college: 'CC', capacity: 20, location: '3rd Floor', roomType: 'Computer Lab', description: 'Multimedia lab', department: 'IT', floorNumber: 3, schedules: [{ name: 'IT Elective 1', time: '10:00AM - 12:00PM Th', college: 'CC', isAvailable: false }] },
    // Add more rooms to test pagination
    { id: 415, room: 'UG 115', building: 'UG Building', college: 'CCAD', capacity: 40, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 416, room: 'UG 116', building: 'UG Building', college: 'CCAD', capacity: 45, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 417, room: 'UG 117', building: 'UG Building', college: 'CCAD', capacity: 50, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 418, room: 'UG 118', building: 'UG Building', college: 'CCAD', capacity: 55, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 419, room: 'UG 119', building: 'UG Building', college: 'CCAD', capacity: 60, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 420, room: 'UG 120', building: 'UG Building', college: 'CCAD', capacity: 65, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 421, room: 'UG 121', building: 'UG Building', college: 'CCAD', capacity: 70, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 422, room: 'UG 122', building: 'UG Building', college: 'CCAD', capacity: 75, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 423, room: 'UG 123', building: 'UG Building', college: 'CCAD', capacity: 80, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
    { id: 424, room: 'UG 124', building: 'UG Building', college: 'CCAD', capacity: 85, location: 'North Wing', roomType: 'Classroom', description: 'General Classroom', department: 'Arts & Design', floorNumber: 1, schedules: [] },
]);

// Layout visibility functions
const toggleSidebar = () => { sidebarVisible.value = !sidebarVisible.value; };
const closeSearchSidebar = () => {
    searchSidebarVisible.value = false;
    roomToViewInSidebar.value = null; // Clear selected room
};
const openAddModal = () => { addModalVisible.value = true; };
const closeAddModal = () => { addModalVisible.value = false; };

const openEditModal = (room) => {
    // Create a copy to prevent direct mutation of the list data
    roomToEdit.value = { ...room };
    editModalVisible.value = true;
};

const closeEditModal = () => {
    editModalVisible.value = false;
    roomToEdit.value = {};
};

/* ------------------------------------------------------------------- */
/* --- Filtering and Selection Logic --- */
/* ------------------------------------------------------------------- */

/**
 * Computed property to filter the room list based on the search query.
 */
const filteredRoomList = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) {
        return roomList.value;
    }
    return roomList.value.filter(room =>
        // Search by ID, Room, Building, College, or Room Type
        String(room.id).includes(query) ||
        room.room.toLowerCase().includes(query) ||
        room.building.toLowerCase().includes(query) ||
        room.college.toLowerCase().includes(query) ||
        room.roomType.toLowerCase().includes(query)
    );
});

/**
 * Computed property for paginated rooms
 */
const paginatedRooms = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredRoomList.value.slice(start, end);
});

/**
 * Computed property for total pages
 */
const totalPages = computed(() => {
    return Math.ceil(filteredRoomList.value.length / itemsPerPage.value);
});

/**
 * Computed property for showing range
 */
const showingRange = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(currentPage.value * itemsPerPage.value, filteredRoomList.value.length);
    const total = filteredRoomList.value.length;
    return { start, end, total };
});

/**
 * Navigate to next page
 */
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

/**
 * Navigate to previous page
 */
const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

/**
 * Go to specific page
 */
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// Reset to page 1 when search changes
const resetPagination = () => {
    currentPage.value = 1;
};

/**
 * Handles clicking a table row to view details in the sidebar.
 * @param {Object} room - The room object clicked.
 */
const selectRoomForSidebar = (room) => {
    roomToViewInSidebar.value = room;
    searchSidebarVisible.value = true;
};

/* ------------------------------------------------------------------- */
/* --- Data Management (Controller Actions) --- */
/* ------------------------------------------------------------------- */

const handleViewDetails = (room) => {
    selectRoomForSidebar(room);
};


const handleEditRoom = (room) => {
    openEditModal(room);
};

// **DELETE ROOM FUNCTIONALITY (Active & with Message)**
const handleDeleteRoom = (id) => {
    // Find the room to get its name before deletion
    const roomToDelete = roomList.value.find(room => room.id === id);

    if (roomToDelete && confirm(`Are you sure you want to delete Room ${roomToDelete.room} (ID ${id})?`)) {
        const index = roomList.value.findIndex(room => room.id === id);

        if (index !== -1) {
            // Store the name for the toast message
            deletedRoomName.value = roomToDelete.room;

            // Delete the room
            roomList.value.splice(index, 1);

            // If the deleted room was the one in the sidebar, close the sidebar
            if (roomToViewInSidebar.value && roomToViewInSidebar.value.id === id) {
                closeSearchSidebar();
            }

            // Show delete success toast (active)
            showDeleteSuccess.value = true;
            setTimeout(() => {
                showDeleteSuccess.value = false;
            }, 2000); // 2 seconds

            console.log(`Room ID ${id} deleted.`);

            // Reset pagination if needed
            resetPagination();
        }
    }
};

// **CREATE ROOM FUNCTIONALITY (Active & with Message)**
const handleAddRoom = (newRoomData) => {
    const newId = roomList.value.length > 0
        ? Math.max(...roomList.value.map(r => r.id)) + 1
        : 1;

    // Standardize data format
    const newRoom = {
        id: newId,
        ...newRoomData,
        capacity: Number(newRoomData.capacity),
        // Ensure required fields exist
        schedules: newRoomData.schedules || [],
        description: newRoomData.description || 'N/A',
        department: newRoomData.department || 'N/A',
        floorNumber: newRoomData.floorNumber || 'N/A',
    };

    roomList.value.push(newRoom);

    // Show success toast (active)
    showCreateSuccess.value = true;
    setTimeout(() => {
        showCreateSuccess.value = false;
    }, 2000);

    // finally close modal
    closeAddModal();

    // Reset pagination to show new room
    resetPagination();
};

/**
 * Handles the update of a room when the EditModal emits the 'save' event.
 * **EDIT ROOM FUNCTIONALITY (Active & with Message)**
 * @param {Object} updatedRoomData - The data from the form, including the room's ID.
 */
const handleRoomUpdate = (updatedRoomData) => {
    // Find the index of the room to update
    const index = roomList.value.findIndex(r => r.id === updatedRoomData.id);

    if (index !== -1) {
        // Standardize data format before saving
        const dataToSave = {
            ...updatedRoomData,
            capacity: Number(updatedRoomData.capacity),
            // Ensure required fields exist, though EditModal usually passes full data
            schedules: updatedRoomData.schedules || [],
            description: updatedRoomData.description || 'N/A',
            department: updatedRoomData.department || 'N/A',
            floorNumber: updatedRoomData.floorNumber || 'N/A',
        };

        // Update the item in the reactive array
        roomList.value[index] = dataToSave;

        // If the updated room is currently in the sidebar, update the sidebar's data
        if (roomToViewInSidebar.value && roomToViewInSidebar.value.id === updatedRoomData.id) {
             roomToViewInSidebar.value = dataToSave;
        }

        // Show success toast for editing (active)
        showEditSuccess.value = true;
        setTimeout(() => {
            showEditSuccess.value = false;
        }, 2000);

        // Close the modal
        closeEditModal();

    } else {
        console.error(`Room with ID ${updatedRoomData.id} not found for update.`);
        closeEditModal();
    }
};


/* ------------------------------------------------------------------- */
/* --- Dashboard Card Calculations (Computed Properties) --- */
/* ------------------------------------------------------------------- */

const totalRoomsDisplay = computed(() => roomList.value.length);

const availableRoomsCount = computed(() => {
    // A room is considered 'Available/Vacant' if ALL its schedule slots are marked as 'isAvailable: true'
    // OR if it has NO schedules at all (fully free).
    return roomList.value.filter(room =>
        room.schedules.length === 0 ||
        room.schedules.every(s => s.isAvailable)
    ).length;
});

const occupiedRoomsCount = computed(() => {
    // A room is considered 'Occupied' if it has AT LEAST ONE schedule slot marked as 'isAvailable: false'
    // This correctly captures rooms that are in use for any period.
    return roomList.value.filter(room =>
        room.schedules.some(s => !s.isAvailable)
    ).length;
});

const uniqueRoomTypesCount = computed(() => {
    const types = new Set(roomList.value.map(room => room.roomType));
    // Display the count of unique room types
    return types.size;
});
</script>

<template>
    <div class="flex pt-14 min-h-screen transition-all duration-300 bg-gray-200">

        <ToastContainer
            :showCreateSuccess="showCreateSuccess"
            :showEditSuccess="showEditSuccess"
            :showDeleteSuccess="showDeleteSuccess"
            :deletedRoomName="deletedRoomName"
        />

        <aside
            :class="{
                'w-64': sidebarVisible,
                'w-0 overflow-hidden hidden sm:block': !sidebarVisible
            }"
            class="transition-all duration-300 flex-shrink-0 fixed h-full z-40"
        >
            <Sidebar v-show="sidebarVisible" class="h-full bg-white shadow-xl" />
        </aside>

        <div :class="{ 'sm:ml-64': sidebarVisible }" class="flex flex-col flex-1 overflow-hidden transition-all duration-300">

            <Navbar @toggle-sidebar="toggleSidebar" />

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 relative">
                <h3 class="mt-2 text-2xl font-bold text-[#7A0C23]">Rooms Management</h3>
                <div class="absolute right-6 top-6 z-20">
                    <div class="mr-4 mt-4 text-sm text-gray-500 whitespace-nowrap ">
                        <span>UPCEBU > ROOMS</span>
                    </div>
                </div>

                <div class="space-y-4 mt-4">
                    <div class="flex justify-between items-center pt-4">
                        <div class="relative w-full max-w-sm">
                            <input
                                type="text"
                                placeholder="SEARCH"
                                v-model="searchQuery"
                                @input="resetPagination"
                                class="pl-12 pr-4 py-2 w-400 rounded-lg border-2 border-yellow-300 focus:outline-none focus:border-[#7A0C23] transition duration-150 bg-white"
                            />
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <div class="flex items-center space-x-2">

                            <button
                                @click="searchSidebarVisible = true"
                                class=" text-gray-500 mr-3 "
                            >
                            </button>

                            <button @click="openAddModal" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transition duration-150 transform hover:scale-[1.02]">
                                <span class="hidden sm:inline">ADD ROOMS</span>
                                <FontAwesomeIcon :icon="icons.plus" class="w-4 h-4 sm:hidden" />
                            </button>
                        </div>
                    </div>

                    <div class=" bg-white p-6 rounded-xl shadow-xl overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Room List ({{ filteredRoomList.length }})</h2>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Items per page:</span>
                                <select v-model="itemsPerPage" @change="resetPagination" class="text-sm border border-gray-300 rounded px-2 py-1">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                        <table class="min-w-full divide-y divide-yellow-600 ">
                            <thead class="bg-[#7A0C23] ">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Room</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Building</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">College</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Capacity</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase">Room Type</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-white uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-yellow-600">
                                <tr v-if="filteredRoomList.length === 0">
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No rooms found matching "{{ searchQuery }}".</td>
                                </tr>
                                <tr v-for="room in paginatedRooms" :key="room.id" class="hover:bg-gray-200 transition duration-100">
                                    <td class="px-6 py-4 text-sm font-mono text-gray-500 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.room }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.building }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.college }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 text-center font-bold cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.capacity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.location }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.roomType }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-center">
                                        <div class="flex justify-center space-x-3">
                                            <button @click="handleViewDetails(room)" title="View Details"
                                                class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                                <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                            </button>
                                            <button @click="handleEditRoom(room)" title="Edit Room"
                                                class="text-green-600 hover:text-green-800 transform hover:scale-110 transition">
                                                <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                            </button>
                                            <button @click="handleDeleteRoom(room.id)" title="Delete Room"
                                                class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                                <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination Controls -->
                        <div v-if="filteredRoomList.length > itemsPerPage" class="flex flex-col sm:flex-row items-center justify-between pt-4 border-t border-gray-200 mt-4">
                            <div class="text-sm text-gray-600 mb-2 sm:mb-0">
                                Showing {{ showingRange.start }} to {{ showingRange.end }} of {{ showingRange.total }} entries
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Previous Button -->
                                <button
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    :class="[
                                        'flex items-center px-3 py-1.5 rounded border text-sm',
                                        currentPage === 1
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
                                        v-for="page in totalPages"
                                        :key="page"
                                        @click="goToPage(page)"
                                        :class="[
                                            'px-3 py-1.5 rounded border text-sm',
                                            currentPage === page
                                                ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                        ]"
                                    >
                                        {{ page }}
                                    </button>
                                </div>

                                <!-- Next Button -->
                                <button
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
                                    :class="[
                                        'flex items-center px-3 py-1.5 rounded border text-sm',
                                        currentPage === totalPages
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                                    ]"
                                >
                                    Next
                                    <FontAwesomeIcon :icon="icons.chevronRight" class="h-3 w-3 ml-1" />
                                </button>
                            </div>

                            <div class="text-sm text-gray-600 mt-2 sm:mt-0">
                                Page {{ currentPage }} of {{ totalPages }}
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </div>

        <div :class="{
            'translate-x-0': searchSidebarVisible,
            'translate-x-full': !searchSidebarVisible
        }"
            class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl transition-transform duration-300 z-50">
            <Sidebarsearch
                v-if="searchSidebarVisible"
                :room-data="roomToViewInSidebar"
                :all-rooms="roomList"
                @select-room="selectRoomForSidebar"
                @close-search="closeSearchSidebar"
            />
        </div>

        <AddRoomModal
            :isVisible="addModalVisible"
            @close="closeAddModal"
            @save="handleAddRoom"
        />

        <EditRoomModal
            :isVisible="editModalVisible"
            :roomData="roomToEdit"
            @close="closeEditModal"
            @save="handleRoomUpdate"
            @reset="console.log('Resetting form...')"
            @upload="console.log('Opening file dialog...')"
        />
    </div>
</template>

<style scoped>
/* Custom theme colors */
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

/* Sticky Navbar */
.flex-col .relative.z-30 {
    position: sticky;
    top: 0;
}

/* Pagination button hover effects */
button:not(:disabled):hover {
    transform: translateY(-1px);
    transition: transform 0.2s;
}
</style>
