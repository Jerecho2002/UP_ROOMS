<script setup>
import { ref, computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'; // <--- NEW/UNCOMMENTED: Required for template
import { faEye, faPenToSquare, faTrash, faPlus, faSearch } from '@fortawesome/free-solid-svg-icons';

// Assuming these imports are correct for your project structure
import Navbar from '@/Components/Navbar.vue'; 
import Sidebar from '@/Components/Sidebar.vue'; 
import Sidebarsearch from '@/Components/RoomModals/Sidebarsearch.vue'; 
import EditRoomModal from '@/Components/RoomModals/EditRoomModal.vue';
import AddRoomModal from '@/Components/RoomModals/AddRoomModal.vue';

/* ------------------------------------------------------------------- */
/* --- Icon Mapping --- */
/* ------------------------------------------------------------------- */
const icons = {
    eye: faEye,
    edit: faPenToSquare, // Mapped to faPenToSquare
    delete: faTrash, // Mapped to faTrash
    plus: faPlus,
    search: faSearch,
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
 * Handles clicking a table row to view details in the sidebar, or explicitly
 * via the new View button.
 * @param {Object} room - The room object clicked.
 */
const selectRoomForSidebar = (room) => {
    roomToViewInSidebar.value = room;
    searchSidebarVisible.value = true;
};

/* ------------------------------------------------------------------- */
/* --- Data Management (Controller Actions) --- */
/* ------------------------------------------------------------------- */

// Handlers for template actions (NEW/FIXED)
const handleViewDetails = (room) => {
    selectRoomForSidebar(room);
};

const handleEditRoom = (room) => {
    openEditModal(room);
};

const handleDeleteRoom = (id) => {
    // Note: The script logic correctly handles an ID input
    if (confirm(`Are you sure you want to delete Room ID ${id}?`)) {
        const index = roomList.value.findIndex(room => room.id === id);
        if (index !== -1) {
            roomList.value.splice(index, 1);
            // If the deleted room was the one in the sidebar, close the sidebar
            if (roomToViewInSidebar.value && roomToViewInSidebar.value.id === id) {
                closeSearchSidebar();
            }
            console.log(`Room ID ${id} deleted.`);
        }
    }
};

const handleAddRoom = (newRoomData) => {
    // Generate a new ID based on the highest existing ID
    const newId = roomList.value.length > 0
        ? Math.max(...roomList.value.map(r => r.id)) + 1
        : 1;

    const newRoom = {
        id: newId, 
        ...newRoomData,
        // Ensure capacity is stored as a number
        capacity: Number(newRoomData.capacity),
        schedules: newRoomData.schedules || [], 
        description: newRoomData.description || 'N/A', 
        department: newRoomData.department || 'N/A',
        floorNumber: newRoomData.floorNumber || 'N/A',
    };

    roomList.value.push(newRoom); 
    console.log(`New room ${newRoom.room} added with ${newRoom.schedules.length} schedules.`);
    
    closeAddModal(); 
};

const handleRoomUpdate = (updatedRoom) => {
    const index = roomList.value.findIndex(room => room.id === updatedRoom.id);
    if (index !== -1) {
        // Replace the old room object with the updated one
        roomList.value[index] = updatedRoom;
        
        // Update the sidebar view immediately if it is displaying this room
        if (roomToViewInSidebar.value && roomToViewInSidebar.value.id === updatedRoom.id) {
             roomToViewInSidebar.value = { ...updatedRoom };
        }
        console.log(`Room ID ${updatedRoom.id} updated.`);
    }
    closeEditModal();
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
                 <h3 class="text-2xl font-bold text-[#7A0C23]">Rooms Dashboard</h3>
                 <div class="absolute right-6 top-6 z-20"> 
                      <div class="text-sm text-gray-500 whitespace-nowrap ">
                          <span>UPCEBU > Room</span>
                      </div>
                 </div>
                 
                 <div class="space-y-4 mt-8">


            

                    <div class="flex justify-between items-center pt-4">
                          <div class="relative w-full max-w-sm">
                              <input 
                                   type="text" 
                                   placeholder="SEARCH" 
                                   v-model="searchQuery"
                                   class="pl-12 pr-4 py-2 w-full rounded-lg border-2 border-gray-300 focus:outline-none focus:border-green-500 transition duration-150 bg-white" 
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
        
    </button>
</div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-xl overflow-x-auto">
                          <h2 class="text-xl font-bold mb-4 text-gray-800">Room List ({{ filteredRoomList.length }})</h2>
                          <table class="min-w-full divide-y divide-gray-200">
                               <thead class="bg-gray-50">
                                   <tr>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Room</th>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Building</th>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">College</th>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Capacity</th>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                                       <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Room Type</th>
                                       <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                                   </tr>
                               </thead>
                               <tbody class="bg-white divide-y divide-gray-200">
                                   <tr v-if="filteredRoomList.length === 0">
                                       <td colspan="8" class="px-6 py-4 text-center text-gray-500">No rooms found matching "{{ searchQuery }}".</td>
                                   </tr>
                                   <tr v-for="room in filteredRoomList" :key="room.id" class="hover:bg-gray-50 transition duration-100">
                                       <td class="px-6 py-4 text-sm font-mono text-gray-500 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.id }}</td>
                                       <td class="px-6 py-4 text-sm font-medium text-gray-900 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.room }}</td>
                                       <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.building }}</td>
                                       <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.college }}</td>
                                       <td class="px-6 py-4 text-sm text-gray-800 text-center font-bold cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.capacity }}</td>
                                       <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.location }}</td>
                                       <td class="px-6 py-4 text-sm text-gray-800 cursor-pointer" @click="selectRoomForSidebar(room)">{{ room.roomType }}</td>
                                       <td class="px-6 py-4 text-sm font-medium text-center">
                                           <div class="flex justify-center space-x-3">
                                               <!-- FIX: Use defined handlers and icons object -->
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
</style>