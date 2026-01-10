
<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import AddRoomTypeModal from '@/Components/RoomTypeModal/AddRoom.vue';
import EditRoomTypeModal from '@/Components/RoomTypeModal/EditRoom.vue';
import Messagefunction from '@/Components/MessageFunction.vue';
import IconButton from '@/Components/IconButton.vue';

// Props from Laravel/Inertia
const props = defineProps({
  room_types: {
    type: Object,
    default: () => ({ data: [], links: {}, meta: {} })
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

// Sidebar state
const sidebarOpen = ref(true);
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

// Data states
const roomTypes = ref(props.room_types.data || []);
const totalCount = ref(props.room_types.meta?.total || 0);
const filteredCount = ref(totalCount.value);
const loading = ref(false);
const searchQuery = ref('');
const searchTimeout = ref(null);

// Modal states
const isAddModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isViewModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const selectedRoomType = ref(null);
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

// Pagination state
const itemsPerPage = ref(20);
const currentPage = ref(1);
const totalPages = ref(props.room_types.meta?.last_page || 1);

// Fetch room types from API
const fetchRoomTypes = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: itemsPerPage.value,
      search: searchQuery.value
    });

    const response = await fetch(`/api/room-types?${params}`);
    const data = await response.json();

    roomTypes.value = data.data;
    totalCount.value = data.meta.total;
    filteredCount.value = data.meta.total;
    totalPages.value = data.meta.last_page;
  } catch (error) {
    console.error('Error fetching room types:', error);
    errorMessage.value = 'Failed to load room types';
    showError.value = true;
  } finally {
    loading.value = false;
  }
};

// Search room types with debounce
const searchRoomTypes = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    currentPage.value = 1;
    fetchRoomTypes();
  }, 300);
};

// Open modals
const openAddModal = () => {
  isAddModalOpen.value = true;
};

const openEditModal = (room) => {
  selectedRoomType.value = { ...room };
  isEditModalOpen.value = true;
};

const openViewModal = async (room) => {
  try {
    const response = await fetch(`/api/room-types/${room.id}`);
    const data = await response.json();
    selectedRoomType.value = data.room_type;
    isViewModalOpen.value = true;
  } catch (error) {
    console.error('Error fetching room type details:', error);
    errorMessage.value = 'Failed to load room type details';
    showError.value = true;
  }
};

const openDeleteModal = (room) => {
  roomToDelete.value = room;
  isDeleteModalOpen.value = true;
};

// Handle CRUD operations
const handleAddRoomType = async (roomData) => {
  try {
    const response = await fetch('/api/room-types', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(roomData)
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to create room type');
    }

    isAddModalOpen.value = false;
    showCreateSuccess.value = true;
    fetchRoomTypes(); // Refresh the list
  } catch (error) {
    errorMessage.value = error.message;
    showError.value = true;
  }
};

const handleEditRoomType = async (roomData) => {
  try {
    const response = await fetch(`/api/room-types/${roomData.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(roomData)
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to update room type');
    }

    isEditModalOpen.value = false;
    showEditSuccess.value = true;
    fetchRoomTypes(); // Refresh the list
  } catch (error) {
    errorMessage.value = error.message;
    showError.value = true;
  }
};

const handleDeleteRoomType = async () => {
  try {
    const response = await fetch(`/api/room-types/${roomToDelete.value.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to delete room type');
    }

    isDeleteModalOpen.value = false;
    deletedRoomName.value = roomToDelete.value.room_type_name;
    roomToDelete.value = null;
    showDeleteSuccess.value = true;
    fetchRoomTypes(); // Refresh the list
  } catch (error) {
    errorMessage.value = error.message;
    showError.value = true;
    isDeleteModalOpen.value = false;
    roomToDelete.value = null;
  }
};

// Pagination methods
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    fetchRoomTypes();
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    fetchRoomTypes();
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    fetchRoomTypes();
  }
};

// Generate page numbers for pagination
const pageNumbers = computed(() => {
  const pages = [];
  const maxVisiblePages = 5;

  if (totalPages.value <= maxVisiblePages) {
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    if (currentPage.value <= 3) {
      for (let i = 1; i <= 4; i++) {
        pages.push(i);
      }
      pages.push('...');
      pages.push(totalPages.value);
    } else if (currentPage.value >= totalPages.value - 2) {
      pages.push(1);
      pages.push('...');
      for (let i = totalPages.value - 3; i <= totalPages.value; i++) {
        pages.push(i);
      }
    } else {
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

// Paginated room types (client-side for filtered results)
const paginatedRoomTypes = computed(() => {
  if (searchQuery.value) {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredRoomTypes.value.slice(start, end);
  }
  return roomTypes.value;
});

// Filtered room types (client-side filtering for search)
const filteredRoomTypes = computed(() => {
  if (!searchQuery.value.trim()) {
    return roomTypes.value;
  }

  const query = searchQuery.value.toLowerCase();
  return roomTypes.value.filter(room =>
    room.room_type_name.toLowerCase().includes(query) ||
    room.slug.toLowerCase().includes(query) ||
    (room.description && room.description.toLowerCase().includes(query)) ||
    room.id.toString().includes(query)
  );
});

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

// Handle edit from view modal
const handleEditFromView = () => {
  if (selectedRoomType.value) {
    isViewModalOpen.value = false;
    openEditModal(selectedRoomType.value);
  }
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
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

// Watch for pagination changes
watch(currentPage, fetchRoomTypes);
watch(itemsPerPage, () => {
  currentPage.value = 1;
  fetchRoomTypes();
});

// Initialize on mount
onMounted(() => {
  // Initial data is already loaded from Inertia props
  // We can optionally fetch fresh data
  fetchRoomTypes();
});
</script>

<style scoped>
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

.transition-colors {
  transition: background-color 0.2s ease, color 0.2s ease;
}

button:not(:disabled):hover {
  transform: translateY(-1px);
  transition: transform 0.2s ease;
}
</style>



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
            <div class="text-sm text-gray-500 whitespace-nowrap mt-8 absolute right-6 top-6 z-20">
              <span>UPCEBU > ROOM TYPES</span>
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
                @input="searchRoomTypes"
                placeholder="Search by name, description, or slug..."
                class="pl-10 pr-4 py-2 w-full border border-yellow-400 rounded-lg focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent outline-none bg-white shadow-sm"
              />
            </div>

            <!-- Add Button -->
            <button
              @click="openAddModal"
              class="flex items-center px-4 py-2 bg-green-700 text-white rounded-lg bg-green-800 hover:bg-green-900 transition-colors duration-200 font-medium shadow-sm w-full md:w-auto mt-2 md:mt-0"
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
                      Default Capacity
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                      Rooms Count
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
                      <div class="text-sm font-medium text-gray-900">{{ room.room_type_name }}</div>
                      <div v-if="room.description" class="text-xs text-gray-500 truncate max-w-xs">{{ room.description }}</div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-sm text-gray-500 font-mono">{{ room.slug }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ room.default_capacity }}</div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ room.rooms_count || 0 }}
                      </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                      <div class="flex items-center space-x-2">
                        <!-- View Button -->
                        <IconButton
                          icon="eye"
                          title="View Room Type Details"
                          size="sm"
                          @click="openViewModal(room)"
                          class="p-1.5 rounded hover:bg-blue-50 transition-colors"
                        />

                        <!-- Edit Button -->
                        <IconButton
                          icon="edit"
                          title="Edit Room Type"
                          size="sm"
                          @click="openEditModal(room)"
                          class="p-1.5 rounded hover:bg-green-50 transition-colors"
                        />

                        <!-- Delete Button -->
                        <IconButton
                          icon="delete"
                          title="Delete Room Type"
                          size="sm"
                          @click="openDeleteModal(room)"
                          class="p-1.5 rounded hover:bg-red-50 transition-colors"
                        />
                      </div>
                    </td>
                  </tr>

                  <!-- Loading State -->
                  <tr v-if="loading">
                    <td colspan="6" class="px-4 py-8 text-center">
                      <div class="flex justify-center items-center">
                        <svg class="animate-spin h-5 w-5 text-[#7A0C23] mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading room types...</span>
                      </div>
                    </td>
                  </tr>

                  <!-- Empty State -->
                  <tr v-if="!loading && roomTypes.length === 0">
                    <td colspan="6" class="px-4 py-8 text-center">
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
            <div v-if="roomTypes.length > 0" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
              <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                <!-- Showing range -->
                <div class="text-sm text-gray-600">
                  Showing {{ paginatedRoomTypes.length }} of {{ filteredCount }} entries
                  <span v-if="searchQuery">(filtered from {{ totalCount }} total)</span>
                </div>

                <!-- Items per page selector -->
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-600">Show:</span>
                  <select
                    v-model="itemsPerPage"
                    @change="fetchRoomTypes"
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
                  <button
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    :class="[
                      'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150 flex items-center',
                      currentPage === 1
                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                    ]"
                  >
                    <IconButton
                      icon="chevronLeft"
                      title="Previous Page"
                      size="sm"
                      color="gray"
                      outlined
                    />
                    Previous
                  </button>

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
                  <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    :class="[
                      'px-3 py-1.5 rounded text-sm font-medium transition-colors duration-150 flex items-center',
                      currentPage === totalPages
                        ? 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                    ]"
                  >
                    Next
                    <IconButton
                      icon="chevronRight"
                      title="Next Page"
                      size="sm"
                      color="gray"
                      outlined
                      class="ml-1"
                    />
                  </button>
                </div>

                <!-- Page indicator -->
                <div class="text-sm text-gray-600">
                  Page {{ currentPage }} of {{ totalPages }}
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Summary -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">



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

    <!-- View Modal -->
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
              <p class="text-sm text-gray-600">Complete information display</p>
            </div>
          </div>
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
          <!-- Room Type Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Basic Info -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                <IconButton
                  icon="info"
                  title="Basic Information"
                  size="sm"
                  disabled
                  class="mr-2"
                />
                Basic Information
              </h4>
              <div class="space-y-4">
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Room Type ID</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    #{{ selectedRoomType.id }}
                  </span>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                  <p class="text-lg font-semibold text-gray-800">{{ selectedRoomType.room_type_name }}</p>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Slug</label>
                  <code class="text-sm font-mono text-gray-800 bg-gray-50 px-2 py-1 rounded">{{ selectedRoomType.slug }}</code>
                </div>
              </div>
            </div>

            <!-- Capacity & Rooms -->
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                <IconButton
                  icon="capacity"
                  title="Capacity Information"
                  size="sm"
                  disabled
                  class="mr-2"
                />
                Capacity & Usage
              </h4>
              <div class="space-y-4">
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Default Capacity</label>
                  <div class="flex items-center">
                    <span class="text-2xl font-bold text-gray-800 mr-2">{{ selectedRoomType.default_capacity }}</span>
                    <span class="text-sm text-gray-500">persons</span>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Rooms Using This Type</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ selectedRoomType.rooms_count || 0 }} rooms
                  </span>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Created Date</label>
                  <p class="text-sm text-gray-600">{{ formatDate(selectedRoomType.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6" v-if="selectedRoomType.description">
            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
              <IconButton
                icon="description"
                title="Description"
                size="sm"
                disabled
                class="mr-2"
              />
              Description
            </h4>
            <div class="bg-gray-50 p-3 rounded border border-gray-200">
              <p class="text-gray-700 whitespace-pre-line">{{ selectedRoomType.description }}</p>
            </div>
          </div>

          <!-- Features -->
          <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6" v-if="selectedRoomType.features">
            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
              <IconButton
                icon="features"
                title="Features"
                size="sm"
                disabled
                class="mr-2"
              />
              Features
            </h4>
            <div class="bg-gray-50 p-3 rounded border border-gray-200">
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="(feature, index) in selectedRoomType.features"
                  :key="index"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                >
                  {{ feature }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <button
              @click="isViewModalOpen = false"
              class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Close
            </button>
            <button
              @click="handleEditFromView"
              class="px-4 py-2 bg-[#7A0C23] text-white rounded hover:bg-red-800 transition-colors"
            >
              Edit Room Type
            </button>
          </div>
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
                Delete <span class="font-semibold">"{{ roomToDelete?.room_type_name }}"</span>?
                <span class="block text-yellow-600 text-xs mt-1">
                  This action cannot be undone.
                  <span v-if="roomToDelete?.rooms_count > 0" class="text-red-600 font-medium">
                    This room type is assigned to {{ roomToDelete?.rooms_count }} rooms and cannot be deleted.
                  </span>
                </span>
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
            :disabled="roomToDelete?.rooms_count > 0"
            :class="[
              'flex items-center px-4 py-2 rounded-lg transition-colors duration-150',
              roomToDelete?.rooms_count > 0
                ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                : 'bg-red-600 text-white hover:bg-red-700'
            ]"
          >
            <IconButton
              icon="delete"
              :title="roomToDelete?.rooms_count > 0 ? 'Cannot Delete' : 'Delete'"
              :color="roomToDelete?.rooms_count > 0 ? 'gray' : 'white'"
              size="sm"
              class="mr-2"
            />
            {{ roomToDelete?.rooms_count > 0 ? 'Cannot Delete' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
