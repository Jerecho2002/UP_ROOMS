<!-- Layouts/RoomTypeLayout.vue -->
<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AddRoomTypeModal from '@/Components/RoomTypeModal/AddRoom.vue';
import EditRoomTypeModal from '@/Components/RoomTypeModal/EditRoom.vue';
import Messagefunction from '@/Components/MessageFunction.vue';
import IconButton from '@/Components/IconButton.vue';

// Sidebar state
const sidebarOpen = ref(true);
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

// Modal states
const isAddModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isViewModalOpen = ref(false);
const selectedRoomType = ref(null);
const isDeleteModalOpen = ref(false);
const roomToDelete = ref(null);

// Toast notification states
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const showError = ref(false);
const showInfo = ref(false);
const deletedRoomName = ref('');
const errorMessage = ref('');
const infoMessage = ref('');

// Room types data
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
  { id: 10, name: 'Drawing Room', slug: 'drawing-room' },
  { id: 11, name: 'Faculty Room', slug: 'faculty-room' },
  { id: 12, name: 'Film Room', slug: 'film-room' },
  { id: 13, name: 'Laboratory', slug: 'laboratory' },
  { id: 14, name: 'Lecture Room', slug: 'lecture-room' },
  { id: 15, name: 'Lounge Room', slug: 'lounge-room' },
  { id: 16, name: 'Meeting Room', slug: 'meeting-room' },
  { id: 17, name: 'Microbiology Laboratory', slug: 'microbiology-laboratory' },
  { id: 18, name: 'Music Room', slug: 'music-room' },
  { id: 19, name: 'Office', slug: 'office' },
  { id: 20, name: 'Physics Laboratory', slug: 'physics-laboratory' },
  { id: 21, name: 'Prayer Room', slug: 'prayer-room' },
  { id: 22, name: 'Science Laboratory', slug: 'science-laboratory' },
  { id: 23, name: 'Seminar Room', slug: 'seminar-room' },
  { id: 24, name: 'Sound Room', slug: 'sound-room' },
  { id: 25, name: 'Special Room', slug: 'special-room' },
  { id: 26, name: 'Speech Laboratory', slug: 'speech-laboratory' },
  { id: 27, name: 'Store Room', slug: 'store-room' },
  { id: 28, name: 'Theatre', slug: 'theatre' },
  { id: 29, name: 'Training Room', slug: 'training-room' },
  { id: 30, name: 'Zoology Laboratory', slug: 'zoology-laboratory' }
]);

// Search functionality
const searchQuery = ref('');

// Filtered room types
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

// === PAGINATION STATE ===
const itemsPerPage = ref(10);
const currentPage = ref(1);

// Paginated room types
const paginatedRoomTypes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredRoomTypes.value.slice(start, end);
});

// Total pages
const totalPages = computed(() => {
  return Math.ceil(filteredRoomTypes.value.length / itemsPerPage.value);
});

// Showing range
const showingRange = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value + 1;
  const end = Math.min(currentPage.value * itemsPerPage.value, filteredRoomTypes.value.length);
  const total = filteredRoomTypes.value.length;
  return { start, end, total };
});

// === PAGINATION METHODS ===
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

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const resetPagination = () => {
  currentPage.value = 1;
};

// Generate page numbers for pagination
const pageNumbers = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;

  if (totalPages.value <= maxVisiblePages) {
    // Show all pages
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    // Show limited pages with ellipsis
    if (currentPage.value <= 3) {
      // Near the beginning
      for (let i = 1; i <= 4; i++) {
        pages.push(i);
      }
      pages.push('...');
      pages.push(totalPages.value);
    } else if (currentPage.value >= totalPages.value - 2) {
      // Near the end
      pages.push(1);
      pages.push('...');
      for (let i = totalPages.value - 3; i <= totalPages.value; i++) {
        pages.push(i);
      }
    } else {
      // In the middle
      pages.push(1);
      pages.push('...');
      for (let i = currentPage.value - 1; i <= currentPage.value + 1; i++) {
        pages.push(i);
      }
      pages.push('...');
      pages.push(totalPages.value);
    }
  }

  return pages;
});

// Watch for search changes to reset pagination
watch(searchQuery, () => {
  resetPagination();
});

// Watch for items per page changes
watch(itemsPerPage, () => {
  resetPagination();
});

// Open add modal
const openAddModal = () => {
  isAddModalOpen.value = true;
};

// Open edit modal
const openEditModal = (room) => {
  selectedRoomType.value = { ...room };
  isEditModalOpen.value = true;
};

// Open view modal
const openViewModal = (room) => {
  selectedRoomType.value = { ...room };
  isViewModalOpen.value = true;
};

// Open delete confirmation
const openDeleteModal = (room) => {
  roomToDelete.value = room;
  isDeleteModalOpen.value = true;
};

// Handle adding new room type
const handleAddRoomType = (newRoom) => {
  try {
    if (!newRoom.name || !newRoom.slug) {
      throw new Error('Room name and slug are required');
    }

    const newId = roomTypes.value.length > 0 ? Math.max(...roomTypes.value.map(r => r.id)) + 1 : 1;

    roomTypes.value.unshift({
      id: newId,
      name: newRoom.name,
      slug: newRoom.slug.toLowerCase().replace(/\s+/g, '-')
    });

    isAddModalOpen.value = false;
    showCreateSuccess.value = true;
    resetPagination(); // Reset to page 1 to show new item
  } catch (error) {
    errorMessage.value = error.message || 'Failed to create room type';
    showError.value = true;
  }
};

// Handle editing room type
const handleEditRoomType = (updatedRoom) => {
  try {
    if (!updatedRoom.name || !updatedRoom.slug) {
      throw new Error('Room name and slug are required');
    }

    const index = roomTypes.value.findIndex(r => r.id === updatedRoom.id);
    if (index !== -1) {
      roomTypes.value[index] = {
        ...updatedRoom,
        slug: updatedRoom.slug.toLowerCase().replace(/\s+/g, '-')
      };

      isEditModalOpen.value = false;
      showEditSuccess.value = true;
    } else {
      throw new Error('Room not found');
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to update room type';
    showError.value = true;
  }
};

// Handle deleting room type
const handleDeleteRoomType = () => {
  try {
    if (roomToDelete.value) {
      const index = roomTypes.value.findIndex(r => r.id === roomToDelete.value.id);
      if (index !== -1) {
        deletedRoomName.value = roomToDelete.value.name;
        roomTypes.value.splice(index, 1);

        isDeleteModalOpen.value = false;
        roomToDelete.value = null;
        showDeleteSuccess.value = true;

        // Reset pagination if needed
        if (paginatedRoomTypes.value.length === 0 && currentPage.value > 1) {
          prevPage();
        }
      } else {
        throw new Error('Room not found');
      }
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to delete room type';
    showError.value = true;
    isDeleteModalOpen.value = false;
    roomToDelete.value = null;
  }
};

// Close toast functions
const closeCreateToast = () => showCreateSuccess.value = false;
const closeEditToast = () => showEditSuccess.value = false;
const closeDeleteToast = () => {
  showDeleteSuccess.value = false;
  deletedRoomName.value = '';
};
const closeErrorToast = () => {
  showError.value = false;
  errorMessage.value = '';
};
const closeInfoToast = () => {
  showInfo.value = false;
  infoMessage.value = '';
};

// Handle icon button clicks
const handleIconClick = (action, room) => {
  switch(action) {
    case 'view':
      openViewModal(room);
      break;
    case 'edit':
      openEditModal(room);
      break;
    case 'delete':
      openDeleteModal(room);
      break;
  }
};

// Handle edit from view modal
const handleEditFromView = () => {
  if (selectedRoomType.value) {
    isViewModalOpen.value = false;
    openEditModal(selectedRoomType.value);
  }
};

// Format date
const formatDate = () => {
  return new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Close view modal when clicking outside
const closeViewModalOutside = (event) => {
  if (event.target.classList.contains('bg-black')) {
    isViewModalOpen.value = false;
  }
};

// Close view modal with Escape key
const handleEscapeKey = (event) => {
  if (event.key === 'Escape' && isViewModalOpen.value) {
    isViewModalOpen.value = false;
  }
};

// Add event listener for Escape key
onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey);
});
</script>

<template>
  <div class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">
    <!-- Toast Notifications -->
    <Messagefunction
      :show-create-success="showCreateSuccess"
      :show-edit-success="showEditSuccess"
      :show-delete-success="showDeleteSuccess"
      :deleted-room-name="deletedRoomName"
      :show-error="showError"
      :error-message="errorMessage"
      :show-info="showInfo"
      :info-message="infoMessage"
      @close-create="closeCreateToast"
      @close-edit="closeEditToast"
      @close-delete="closeDeleteToast"
      @close-error="closeErrorToast"
      @close-info="closeInfoToast"
    />

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
            <h1 class="text-1xl md:text-2xl font-bold text-[#7A0C23] mb-1">Room Types Management</h1>
           <div class="absolute right-6 top-6 z-20">
                    <div class="text-sm text-gray-500 whitespace-nowrap ">

        </div>

<div class="mt-8 absolute right-6 top-6 z-20">
                    <div class="text-sm text-gray-500 whitespace-nowrap ">
                        <span>UPCEBU > ROOM TYPES</span>
                    </div>
                </div>
     </div>

                </div>




          <!-- Controls -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-4">
            <!-- Search Box -->
            <div class="relative w-full md:w-80">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <IconButton
                  icon="search"
                  title="Search room types"
                  size="sm"
                  disabled
                />
              </div>
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Search by ID, name, or slug..."
                class="pl-10 pr-4 py-2 w-full border border-yellow-400 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none bg-white shadow-sm"
              />
            </div>

            <!-- Add Button -->
            <button
              @click="openAddModal"
              class="flex items-center px-4 py-2 bg-green-700 text-white rounded-lg bg-green-800 transition-colors duration-200 font-medium shadow-sm w-full md:w-auto mt-2 md:mt-0"
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
          <div class="bg-white rounded-lg shadow border border-yellow-400 overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-yellow-600">
                <thead class="bg-[#7A0C23]">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                      ID
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                      Name
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                      Slug
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-yellow-400">
                  <tr v-for="room in paginatedRoomTypes" :key="room.id" class="hover:bg-gray-200 transition-colors duration-150">
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
                        <!-- View Button -->
                        <IconButton
                          icon="eye"
                          title="View Room Type Details"
                          size="sm"
                          @click="handleIconClick('view', room)"
                          class="p-1.5 rounded hover:bg-blue-50 transition-colors"
                        />

                        <!-- Edit Button -->
                        <IconButton
                          icon="edit"
                          title="Edit Room Type"
                          size="sm"
                          @click="handleIconClick('edit', room)"
                          class="p-1.5 rounded hover:bg-green-50 transition-colors"
                        />

                        <!-- Delete Button -->
                        <IconButton
                          icon="delete"
                          title="Delete Room Type"
                          size="sm"
                          @click="handleIconClick('delete', room)"
                          class="p-1.5 rounded hover:bg-red-50 transition-colors"
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

            <!-- Enhanced Pagination Controls -->
            <div v-if="filteredRoomTypes.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
              <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">

                <!-- Showing range -->
                <div class="text-sm text-gray-600">
                  Showing {{ showingRange.start }} to {{ showingRange.end }} of {{ showingRange.total }} entries
                </div>

                <!-- Items per page selector -->
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-600">Show:</span>
                  <select
                    v-model="itemsPerPage"
                    class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
                  >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                  </select>
                  <span class="text-sm text-gray-600">per page</span>
                </div>

                <!-- Page navigation -->
                <div class="flex items-center space-x-2">
                  <!-- Previous button -->
                  <IconButton
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    icon="chevronLeft"
                    title="Previous Page"
                    size="sm"
                    color="gray"
                    outlined
                    :class="[
                      'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150',
                      currentPage === 1
                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                    ]"
                  >
                    Previous
                  </IconButton>

                  <!-- Page numbers -->
                  <div class="flex items-center space-x-1">
                    <button
                      v-for="(page, index) in pageNumbers"
                      :key="index"
                      @click="typeof page === 'number' ? goToPage(page) : null"
                      :disabled="page === '...'"
                      :class="[
                        'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px] transition-colors duration-150',
                        page === '...'
                          ? 'bg-white text-gray-400 border-gray-300 cursor-default'
                          : currentPage === page
                            ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                      ]"
                    >
                      {{ page }}
                    </button>
                  </div>

                  <!-- Next button -->
                  <IconButton
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    icon="chevronRight"
                    title="Next Page"
                    size="sm"
                    color="gray"
                    outlined
                    :class="[
                      'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150',
                      currentPage === totalPages
                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                    ]"
                  >
                    Next
                  </IconButton>
                </div>

                <!-- Page indicator -->
                <div class="text-sm text-gray-600">
                  Page {{ currentPage }} of {{ totalPages }}
                </div>
              </div>

              <!-- Results summary -->
              <div class="mt-4 pt-3 border-t border-gray-300 text-center">
                <p class="text-sm text-gray-500">
                  Filtered Results: <span class="font-semibold text-[#7A0C23]">{{ filteredRoomTypes.length }}</span>
                  | Total Room Types: <span class="font-semibold text-[#7A0C23]">{{ roomTypes.length }}</span>
                </p>
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

    <!-- VIEW MODAL - Shows all information (Read-only) -->
    <div v-if="isViewModalOpen" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4" @click="closeViewModalOutside">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 flex items-center justify-between">
          <div class="flex items-center">
            <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
              <IconButton
                icon="eye"
                title="View Room"
                size="md"
                disabled
              />
            </div>
            <div>
              <h2 class="text-xl font-semibold text-gray-900">Room Type Details</h2>
              <p class="text-sm text-gray-600">Read-only information display</p>
            </div>
          </div>

          <!-- Close Button -->
          <button
            @click="isViewModalOpen = false"
            class="text-gray-400 hover:text-gray-600 transition-colors duration-150"
            title="Close"
          >
            <IconButton
              icon="times"
              title="Close"
              size="sm"
            />
          </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]" v-if="selectedRoomType">
          <!-- Room ID Section -->
          <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center mb-2">
              <IconButton
                icon="info"
                title="Room ID"
                size="sm"
                disabled
                class="mr-2"
              />
              <h3 class="text-lg font-medium text-gray-900">Room ID</h3>
            </div>
            <div class="flex items-center">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                #{{ selectedRoomType.id }}
              </span>
              <span class="ml-3 text-sm text-gray-500">Unique identifier for this room type</span>
            </div>
          </div>

          <!-- Room Information Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Room Name -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <div class="flex items-center mb-3">
                <IconButton
                  icon="edit"
                  title="Room Name"
                  size="sm"
                  disabled
                  class="mr-2"
                />
                <h4 class="text-md font-medium text-gray-900">Room Name</h4>
              </div>
              <div class="p-3 bg-gray-50 rounded border border-gray-200">
                <p class="text-lg font-semibold text-gray-800">{{ selectedRoomType.name }}</p>
                <p class="text-sm text-gray-500 mt-1">The display name of the room type</p>
              </div>
            </div>

            <!-- Room Slug -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <div class="flex items-center mb-3">
                <IconButton
                  icon="list"
                  title="Room Slug"
                  size="sm"
                  disabled
                  class="mr-2"
                />
                <h4 class="text-md font-medium text-gray-900">Room Slug</h4>
              </div>
              <div class="p-3 bg-gray-50 rounded border border-gray-200">
                <code class="text-lg font-mono text-gray-800 bg-white px-2 py-1 rounded">{{ selectedRoomType.slug }}</code>
                <p class="text-sm text-gray-500 mt-1">URL-friendly identifier</p>
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center mb-3">
              <IconButton
                icon="info"
                title="Additional Information"
                size="sm"
                disabled
                class="mr-2"
              />
              <h4 class="text-md font-medium text-gray-900">Additional Information</h4>
            </div>
            <div class="space-y-3">
              <!-- Created Date -->
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                  <p class="text-sm font-medium text-gray-700">Created Date</p>
                  <p class="text-xs text-gray-500">When this room type was added</p>
                </div>
                <div class="text-sm text-gray-600">
                  {{ formatDate() }}
                </div>
              </div>

              <!-- Last Modified -->
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                  <p class="text-sm font-medium text-gray-700">Last Modified</p>
                  <p class="text-xs text-gray-500">When this room type was last updated</p>
                </div>
                <div class="text-sm text-gray-600">
                  {{ formatDate() }}
                </div>
              </div>

              <!-- Status -->
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <div>
                  <p class="text-sm font-medium text-gray-700">Status</p>
                  <p class="text-xs text-gray-500">Current status of the room type</p>
                </div>
                <div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <IconButton
                      icon="check"
                      title="Active"
                      size="xs"
                      disabled
                      class="mr-1"
                    />
                    Active
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Info Box -->
          <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
              <div class="flex-shrink-0">
                <IconButton
                  icon="info"
                  title="Information"
                  size="sm"
                  disabled
                />
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">View-Only Mode</h3>
                <div class="mt-2 text-sm text-blue-700">
                  <p>This is a read-only view. All fields are displayed for reference only and cannot be modified here.</p>
                  <p class="mt-1">To make changes, please use the "Edit" button on the main page.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-else class="p-8 text-center">
          <IconButton
            icon="warning"
            title="Loading"
            size="lg"
            disabled
            class="mx-auto mb-3 opacity-50"
          />
          <p class="text-gray-500">Loading room information...</p>
        </div>



      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-5">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
            <IconButton
              icon="delete"
              title="Delete Warning"
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
                size="sm"
                disabled
              />
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                Delete <span class="font-semibold">"{{ roomToDelete?.name }}"</span>?
                <span class="block text-yellow-600 text-xs mt-1">This action cannot be undone.</span>
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

/* Smooth transitions */
.transition-colors {
  transition: background-color 0.2s ease, color 0.2s ease;
}

/* Custom styles for pagination */
button:not(:disabled):hover {
  transform: translateY(-1px);
  transition: transform 0.2s ease;
}

/* Ensure pagination controls are properly spaced */
.space-x-1 > * + * {
  margin-left: 0.25rem;
}

.space-x-2 > * + * {
  margin-left: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .flex-col.md\:flex-row {
    gap: 1rem;
  }

  .space-x-2 {
    justify-content: center;
  }
}
</style>
