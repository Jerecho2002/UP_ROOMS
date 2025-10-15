<script setup>
/* Vue core features */
import { ref } from 'vue';

/* Component imports */
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Sidebarsearch from '@/Components/Modals/Sidebarsearch.vue';
import EditRoomModal from '@/Components/Modals/EditRoomModal.vue';
import AddRoomModal from '@/Components/Modals/AddRoomModal.vue';

/* --- Sidebar (Left) --- */
const sidebarVisible = ref(true);
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
};

/* --- Sidebar Search (Right) --- */
const searchSidebarVisible = ref(true);
const toggleSearchSidebar = () => {
  searchSidebarVisible.value = !searchSidebarVisible.value;
};
const closeSearchSidebar = () => {
  searchSidebarVisible.value = false;
};

/* --- Room Data --- */
const roomList = ref([
  {
    id: 1,
    room: 'Room 101',
    building: 'Admin Building',
    college: 'College of Business Admin',
    capacity: 40,
    location: '1st Floor, North',
    roomType: 'Lecture Hall',
  },
  {
    id: 2,
    room: 'Room 202',
    building: 'Science Hall',
    college: 'College of Science',
    capacity: 60,
    location: '2nd Floor, East',
    roomType: 'Classroom',
  },
  {
    id: 3,
    room: 'Room 305',
    building: 'IT Building',
    college: 'College of Computer Studies',
    capacity: 35,
    location: '3rd Floor, West',
    roomType: 'Computer Lab',
  },
  {
    id: 4,
    room: 'Room 401',
    building: 'Main Building',
    college: 'College of Engineering',
    capacity: 80,
    location: '4th Floor, South',
    roomType: 'Lecture Hall',
  },
  {
    id: 5,
    room: 'Conf Rm 2',
    building: 'Admin Building',
    college: 'College of Social Sciences',
    capacity: 20,
    location: '2nd Floor, South',
    roomType: 'Conference Room',
  },
  {
    id: 6,
    room: 'Lab 102',
    building: 'Science Hall',
    college: 'College of Science',
    capacity: 25,
    location: 'Ground Floor, East',
    roomType: 'Science Lab',
  },
  {
    id: 7,
    room: 'Study Rm A',
    building: 'Library',
    college: 'College of Arts & Letters',
    capacity: 12,
    location: 'Ground Floor, West',
    roomType: 'Study Area',
  },
]);

/* --- DELETE ROOM --- */
const handleDeleteRoom = (id) => {
  if (confirm(`Are you sure you want to delete Room ID ${id}?`)) {
    const initialLength = roomList.value.length;
    roomList.value = roomList.value.filter(room => room.id !== id);
    if (roomList.value.length < initialLength) {
      console.log(`Room with ID ${id} deleted successfully.`);
    } else {
      console.log(`Room with ID ${id} not found.`);
    }
  }
};

/* --- ADD ROOM MODAL --- */
const addModalVisible = ref(false);

const openAddModal = () => {
  addModalVisible.value = true;
};
const closeAddModal = () => {
  addModalVisible.value = false;
};

const handleAddRoom = (newRoomData) => {
  const newId = roomList.value.length > 0
    ? Math.max(...roomList.value.map(r => r.id)) + 1
    : 1;

  const newRoom = {
    id: newId,
    ...newRoomData,
    capacity: Number(newRoomData.capacity) || 0
  };

  roomList.value.push(newRoom);
  console.log(`New room ${newRoom.room} added successfully with ID ${newId}.`);
  closeAddModal();
};

/* --- EDIT ROOM MODAL --- */
const editModalVisible = ref(false);
const roomToEdit = ref({});

const openEditModal = (room) => {
  roomToEdit.value = { ...room }; // Clone room to avoid reactive mutation
  editModalVisible.value = true;
};

const closeEditModal = () => {
  editModalVisible.value = false;
  roomToEdit.value = {};
};

const handleRoomUpdate = (updatedRoom) => {
  const index = roomList.value.findIndex(r => r.id === updatedRoom.id);
  if (index !== -1) {
    roomList.value[index] = updatedRoom;
    console.log(`Room ${updatedRoom.room} updated successfully.`);
  }
};
</script>

<!-- TEMPLATE -->
<template>
  <div class="flex pt-14 min-h-screen transition-all duration-300">

    <!-- LEFT SIDEBAR -->
    <aside
      :class="{
        'w-64': sidebarVisible,
        'w-0 overflow-hidden hidden sm:block': !sidebarVisible
      }"
      class="transition-all duration-300 flex-shrink-0 fixed h-full z-40"
    >
      <Sidebar v-show="sidebarVisible" class="h-full" />
    </aside>

    <!-- MAIN CONTENT -->
    <div :class="{ 'ml-64': sidebarVisible }" class="flex flex-col flex-1 overflow-hidden transition-all duration-300">
      <!-- NAVBAR -->
      <Navbar @toggle-sidebar="toggleSidebar" @show-search="toggleSearchSidebar" />

      <!-- MAIN BODY -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
        <div class="space-y-6">

          <!-- HEADER & BREADCRUMB -->
          <div class="flex items-baseline justify-between">
            <h1 class="text-xl font-bold text-gray-800">Rooms Dashboard</h1>
            <div class="text-sm text-gray-500">
              <span>UPCEBU &gt;</span>
              <a href="#" @click.prevent="toggleSearchSidebar"
                 class="text-green-600 hover:text-green-700 font-semibold cursor-pointer">Rooms</a>
            </div>
          </div>

          <!-- SEARCH + ADD BUTTON -->
          <div class="flex justify-between items-center">
            <!-- SEARCH INPUT -->
            <div class="relative w-96">
              <input
                type="text"
                placeholder="SEARCH"
                class="pl-12 pr-4 py-2 w-full rounded-lg border-2 border-gray-300 focus:outline-none focus:border-green-500 transition duration-150"
                style="background-color: #f0fdf4;"
              />
              <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                   fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>

            <!-- ADD BUTTON -->
            <div class="flex space-x-4">
              <button
                @click="openAddModal"
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition duration-150"
              >
                ADD ROOMS
              </button>
            </div>
          </div>

          <!-- ROOM LIST TABLE -->
          <div class="bg-white p-4 rounded-lg shadow-xl overflow-x-auto">
            <h2 class="text-lg font-semibold mb-4">Room List</h2>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Building</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">College</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Type</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="room in roomList" :key="room.id">
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.id }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.room }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.building }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.college }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.capacity }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.location }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">{{ room.roomType }}</td>
                  <td class="px-6 py-4 text-sm font-medium text-center">
                    <div class="flex justify-center space-x-2">
                      <!-- EDIT -->
                      <button @click="openEditModal(room)" class="text-green-600 hover:text-green-900">
                        ✏️
                      </button>
                      <!-- DELETE -->
                      <button @click="handleDeleteRoom(room.id)" class="text-red-600 hover:text-red-900">
                        🗑️
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

    <!-- RIGHT SIDEBAR: Search -->
    <div :class="{
      'translate-x-0': searchSidebarVisible,
      'translate-x-full': !searchSidebarVisible
    }"
         class="fixed right-0 top-0 h-full w-80 bg-white shadow-2xl transition-transform duration-300 z-50">
      <Sidebarsearch v-show="searchSidebarVisible" @close-search="closeSearchSidebar" />
    </div>

    <!-- ADD ROOM MODAL -->
    <AddRoomModal
      :isVisible="addModalVisible"
      @close="closeAddModal"
      @save="handleAddRoom"
    />

    <!-- EDIT ROOM MODAL -->
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

<!-- STYLES -->
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
