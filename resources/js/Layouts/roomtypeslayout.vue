<script setup>
import { ref, onMounted, computed } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AddRoomTypeModal from '@/Components/RoomTypeModal/AddRoom.vue';
import EditRoomTypeModal from '@/Components/RoomTypeModal/EditRoom.vue';
import Messagefunction from '@/Components/Messagefunction.vue';
import IconButton from '@/Components/IconButton.vue';

// Sidebar state
const sidebarOpen = ref(true);
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

// Modal states
const isAddModalOpen = ref(false);
const isEditModalOpen = ref(false);
const selectedRoomType = ref(null);
const isDeleteModalOpen = ref(false);
const roomToDelete = ref(null);

// Optimized room types data - smaller initial set
const roomTypes = ref([
  { id: 1, name: 'Audio Visual Room', slug: 'audio-visual-room' },
  { id: 2, name: 'Bioassay Laboratory', slug: 'bioassay-laboratory' },
  { id: 3, name: 'Biology and Chemistry Laboratory', slug: 'biology-and-chemistry-laboratory' },
  { id: 4, name: 'Botany Laboratory', slug: 'botany-laboratory' },
  { id: 5, name: 'Chemistry Laboratory', slug: 'chemistry-laboratory' },
  { id: 6, name: 'Computer Laboratory', slug: 'computer-laboratory' },
  { id: 7, name: 'Conference Room', slug: 'conference-room' },
  { id: 8, name: 'Consultation Room', slug: 'consultation-room' },
  { id: 9, name: 'Deans Office', slug: 'deans-office' },
  { id: 10, name: 'Drawing Room', slug: 'drawing-room' }
]);

// Search functionality
const searchQuery = ref('');

// Filtered room types - computed for better performance
const filteredRoomTypes = computed(() => {
  if (!searchQuery.value.trim()) {
    return roomTypes.value;
  }

  const query = searchQuery.value.toLowerCase();
  return roomTypes.value.filter(room =>
    room.name.toLowerCase().includes(query) ||
    room.slug.toLowerCase().includes(query) ||
    room.id.toString().includes(query)
  );
});

// Pagination for better performance
const itemsPerPage = 10;
const currentPage = ref(1);

// Paginated room types
const paginatedRoomTypes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredRoomTypes.value.slice(start, end);
});

// Total pages
const totalPages = computed(() => {
  return Math.ceil(filteredRoomTypes.value.length / itemsPerPage);
});

// Navigation functions
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

// Open add modal
const openAddModal = () => {
  isAddModalOpen.value = true;
};

// Open edit modal
const openEditModal = (room) => {
  selectedRoomType.value = { ...room };
  isEditModalOpen.value = true;
};

// Open delete confirmation
const openDeleteModal = (room) => {
  roomToDelete.value = room;
  isDeleteModalOpen.value = true;
};

// Handle adding new room type
const handleAddRoomType = (newRoom) => {
  // Generate new ID
  const newId = roomTypes.value.length > 0 ? Math.max(...roomTypes.value.map(r => r.id)) + 1 : 1;

  // Add to the beginning of the list
  roomTypes.value.unshift({
    id: newId,
    name: newRoom.name,
    slug: newRoom.slug.toLowerCase().replace(/\s+/g, '-')
  });

  isAddModalOpen.value = false;
  showToast('Room type added successfully!', 'success');
};

// Handle editing room type
const handleEditRoomType = (updatedRoom) => {
  const index = roomTypes.value.findIndex(r => r.id === updatedRoom.id);
  if (index !== -1) {
    roomTypes.value[index] = {
      ...updatedRoom,
      slug: updatedRoom.slug.toLowerCase().replace(/\s+/g, '-')
    };

    isEditModalOpen.value = false;
    showToast('Room type updated successfully!', 'success');
  }
};

// Handle deleting room type
const handleDeleteRoomType = () => {
  if (roomToDelete.value) {
    const index = roomTypes.value.findIndex(r => r.id === roomToDelete.value.id);
    if (index !== -1) {
      roomTypes.value.splice(index, 1);
      isDeleteModalOpen.value = false;
      roomToDelete.value = null;
      showToast('Room type deleted successfully!', 'success');
    }
  }
};

// Toast notification
const toastMessage = ref('');
const toastType = ref('');

const showToast = (message, type = 'success') => {
  toastMessage.value = message;
  toastType.value = type;

  setTimeout(() => {
    toastMessage.value = '';
    toastType.value = '';
  }, 3000);
};

// Handle icon button clicks
const handleIconClick = (action, room) => {
  switch(action) {
    case 'view':
      openEditModal(room);
      break;
    case 'edit':
      openEditModal(room);
      break;
    case 'delete':
      openDeleteModal(room);
      break;
  }
};
</script>

<template>
  <div class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">
    <!-- Toast Notification -->
    <div v-if="toastMessage"
         :class="[
           'fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300',
           toastType === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
           'bg-red-100 border-l-4 border-red-500 text-red-700'
         ]">
      <div class="flex items-center">
        <IconButton
          v-if="toastType === 'success'"
          icon="check"
          title="Success"
          color="green"
          size="sm"
          class="mr-2"
          disabled
        />
        <IconButton
          v-else
          icon="times"
          title="Error"
          color="red"
          size="sm"
          class="mr-2"
          disabled
        />
        {{ toastMessage }}
      </div>
    </div>

    <Navbar @toggle-sidebar="toggleSidebar" />

    <div class="flex flex-1 pt-14 overflow-hidden">
      <!-- Sidebar -->
      <div
        class="fixed top-14 left-0 h-[calc(100vh-3.5rem)] bg-white shadow-xl z-30 transition-all duration-300"
        :class="sidebarOpen ? 'w-64' : 'w-20'"
      >
        <Sidebar :sidebar-open="sidebarOpen" />
      </div>

      <!-- Main Content -->
      <main
        id="mainContent"
        class="flex-1 bg-gray-50 transition-all duration-300 overflow-y-auto"
        :class="sidebarOpen ? 'ml-64' : 'ml-20'"
      >
        <div class="p-4 md:p-6">
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-[#7A0C23] mb-1">Room Type Management</h1>
            <p class="text-sm text-gray-600">Manage and organize different types of rooms in your facility</p>
          </div>

          <!-- Controls -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-4">
            <!-- Search Box -->
            <div class="relative w-full md:w-80">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <IconButton
                  icon="search"
                  title="Search"
                  color="gray"
                  size="sm"
                  disabled
                />
              </div>
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search by ID, name, or slug..."
                class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none bg-white shadow-sm"
              />
            </div>

            <!-- Add Button -->
            <button
              @click="openAddModal"
              class="flex items-center px-4 py-2 bg-[#7A0C23] text-white rounded-lg hover:bg-red-800 transition-colors duration-200 font-medium shadow-sm w-full md:w-auto mt-2 md:mt-0"
            >
              <IconButton
                icon="plus"
                title="Add New Room Type"
                color="white"
                size="sm"
                class="mr-2"
              />
              Add New Room Type
            </button>
          </div>

          <!-- Room Types Table -->
          <div class="bg-white rounded-lg shadow border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      ID
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Name
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Slug
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="room in paginatedRoomTypes" :key="room.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        #{{ room.id }}
                      </span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm font-medium text-gray-900">{{ room.name }}</div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm text-gray-500 font-mono">{{ room.slug }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <IconButton
                          icon="eye"
                          title="View Room Type Details"
                          color="blue"
                          size="sm"
                          @click="handleIconClick('view', room)"
                          class="p-1.5  rounded bg-white hover:bg-gray-50"
                        />

                        <IconButton
                          icon="edit"
                          title="Edit Room Type"
                          color="green"
                          size="sm"
                          @click="handleIconClick('edit', room)"
                          class="p-1.5 rounded bg-green-50 hover:bg-blue-100"
                        />

                        <IconButton
                          icon="delete"
                          title="Delete Room Type"
                          color="red"
                          size="sm"
                          @click="handleIconClick('delete', room)"
                          class="p-1.5  rounded bg-red-50 hover:bg-red-100"
                        />
                      </div>
                    </td>
                  </tr>

                  <!-- Empty State -->
                  <tr v-if="filteredRoomTypes.length === 0">
                    <td colspan="4" class="px-4 py-8 text-center">
                      <div class="text-gray-500">
                        <IconButton
                          icon="search"
                          title="No Results"
                          color="gray"
                          size="lg"
                          disabled
                          class="mx-auto mb-3 opacity-50"
                        />
                        <p class="text-base font-medium mb-1">No room types found</p>
                        <p class="text-sm mb-4">Try adjusting your search or add a new room type</p>
                        <button
                          @click="openAddModal"
                          class="inline-flex items-center px-4 py-2 bg-[#7A0C23] text-white rounded-lg hover:bg-red-800 transition-colors duration-200"
                        >
                          <IconButton
                            icon="plus"
                            title="Add New"
                            color="white"
                            size="sm"
                            class="mr-2"
                          />
                          Add New Room Type
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="filteredRoomTypes.length > itemsPerPage" class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-between items-center">
              <div class="text-sm text-gray-600 mb-2 sm:mb-0">
                Showing {{ Math.min((currentPage - 1) * itemsPerPage + 1, filteredRoomTypes.length) }} to
                {{ Math.min(currentPage * itemsPerPage, filteredRoomTypes.length) }} of
                {{ filteredRoomTypes.length }} results
              </div>
              <div class="flex items-center space-x-1">
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  :class="[
                    'px-3 py-1 rounded border',
                    currentPage === 1
                      ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                      : 'bg-white text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  Previous
                </button>
                <span class="px-3 py-1 text-sm text-gray-700">
                  Page {{ currentPage }} of {{ totalPages }}
                </span>
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  :class="[
                    'px-3 py-1 rounded border',
                    currentPage === totalPages
                      ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                      : 'bg-white text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add Room Type Modal -->
    <AddRoomTypeModal
      :is-open="isAddModalOpen"
      @close="isAddModalOpen = false"
      @save="handleAddRoomType"
    />

    <!-- Edit Room Type Modal -->
    <EditRoomTypeModal
      :is-open="isEditModalOpen"
      :room-type="selectedRoomType"
      @close="isEditModalOpen = false"
      @save="handleEditRoomType"
    />

    <!-- Delete Confirmation Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-5">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
            <IconButton
              icon="delete"
              title="Delete Warning"
              color="red"
              size="md"
              disabled
            />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">Delete Room Type</h3>
          </div>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-5">
          <div class="flex">
            <div class="flex-shrink-0">
              <IconButton
                icon="warning"
                title="Warning"
                color="yellow"
                size="sm"
                disabled
              />
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                Delete <span class="font-semibold">"{{ roomToDelete?.name }}"</span>?
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            @click="isDeleteModalOpen = false; roomToDelete = null;"
            class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-150"
          >
            <IconButton
              icon="times"
              title="Cancel"
              color="gray"
              size="sm"
              class="mr-2"
            />
            Cancel
          </button>
          <button
            @click="handleDeleteRoomType"
            class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-150"
          >
            <IconButton
              icon="delete"
              title="Delete"
              color="white"
              size="sm"
              class="mr-2"
            />
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Minimal custom scrollbar for better performance */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>
