<template>
  <div class="space-y-6">
    <div class="bg-white shadow rounded-lg p-4">
      <h6 class="font-bold text-xl text-[#7A0C23] mb-4 pb-2 border-b">Department List 📃</h6>

      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
        <div class="relative w-full sm:w-auto max-w-[400px]">
          <input
            type="text"
            placeholder="Search Department, College, or Dean"
            v-model="searchTerm"
            @input="handleSearch"
            class="border-yellow-400 w-full p-3 pl-10 rounded-lg focus:ring-2 focus:ring-[#7A0C23] shadow-sm bg-gray-100"
          >
          <IconButton
            icon="search"
            size="sm"
            color="gray"
            class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
          />
        </div>

        <IconButton
          @click="openAddModal"
          icon="plus"
          title="Add Department"
          size="sm"
          color="green"
          outlined
          class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150 flex items-center justify-center"
        >
          <span>Add Department</span>
        </IconButton>
      </div>

      <!-- Fixed Width Table Container -->
      <div class="border rounded-lg border-yellow-400 overflow-hidden">
        <div class="overflow-x-hidden"> <!-- Changed from overflow-x-auto to overflow-x-hidden -->
          <table class="min-w-full divide-y divide-yellow-300">
            <thead class="bg-[#7A0C23] text-white">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-[12%]">Dept Code</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-[28%]">Department Name</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-[25%]">College</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-[20%]">Dean/Head</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider w-[15%]">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-yellow-400 bg-white">
              <tr v-for="item in departments" :key="item.id" class="hover:bg-gray-300">
                <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate">{{ item.department_code || 'N/A' }}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate">{{ item.department_name }}</td>
                <td class="px-4 py-3 text-sm text-gray-700 truncate">{{ item.college }}</td>
                <td class="px-4 py-3 text-sm text-gray-700 truncate">{{ item.dean }}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center justify-center space-x-1">
                    <IconButton
                      @click="handleViewDetails(item)"
                      icon="eye"
                      title="View Details"
                      size="xs"
                      color="blue"
                      class="inline-flex items-center justify-center hover:scale-110 transition-transform"
                    />
                    <IconButton
                      @click="handleEditDetails(item)"
                      icon="edit"
                      title="Edit Department"
                      size="xs"
                      color="green"
                      class="inline-flex items-center justify-center hover:scale-110 transition-transform"
                    />
                    <IconButton
                      @click="handleDeleteDetails(item)"
                      icon="delete"
                      title="Delete Department"
                      size="xs"
                      color="red"
                      class="inline-flex items-center justify-center hover:scale-110 transition-transform"
                    />
                  </div>
                </td>
              </tr>
              <tr v-if="loading">
                <td colspan="5" class="px-4 py-4 text-center">
                  <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#7A0C23]"></div>
                  </div>
                </td>
              </tr>
              <tr v-else-if="departments.length === 0 && !loading">
                <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                  No departments found{{ searchTerm ? ' matching "' + searchTerm + '"' : '' }}.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Horizontal Pagination - Fixed at Bottom -->
      <div v-if="pagination.total > 0" class="mt-4">
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
          <div class="text-sm text-gray-600 whitespace-nowrap">
            Showing <span class="font-semibold">{{ pagination.from }}</span> to
            <span class="font-semibold">{{ pagination.to }}</span> of
            <span class="font-semibold">{{ pagination.total }}</span> entries
          </div>

          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
              <span class="text-sm text-gray-600 whitespace-nowrap">Show:</span>
              <select
                v-model="pagination.perPage"
                @change="changePerPage"
                class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23]"
              >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
              </select>
              <span class="text-sm text-gray-600 whitespace-nowrap">per page</span>
            </div>

            <!-- Horizontal Page Numbers -->
            <div class="flex items-center space-x-1">
              <!-- Previous Button -->
              <button
                @click="prevPage"
                :disabled="pagination.currentPage === 1"
                :class="[
                  'flex items-center justify-center w-8 h-8 rounded border border-gray-300 text-sm font-medium',
                  pagination.currentPage === 1
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                ]"
                title="Previous Page"
              >
                &lt;
              </button>

              <!-- Page Numbers -->
              <button
                v-for="page in getDisplayPages()"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'flex items-center justify-center w-8 h-8 rounded border text-sm font-medium',
                  page === '...'
                    ? 'border-transparent text-gray-500 cursor-default'
                    : pagination.currentPage === page
                    ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                ]"
                :disabled="page === '...'"
              >
                {{ page }}
              </button>

              <!-- Next Button -->
              <button
                @click="nextPage"
                :disabled="pagination.currentPage === pagination.lastPage"
                :class="[
                  'flex items-center justify-center w-8 h-8 rounded border border-gray-300 text-sm font-medium',
                  pagination.currentPage === pagination.lastPage
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                ]"
                title="Next Page"
              >
                &gt;
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <Teleport to="body">
      <div v-if="isViewModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 space-y-4">
          <h3 class="text-2xl font-bold text-[#7A0C23] border-b pb-2">View Department Details</h3>
          <div v-if="currentItem" class="space-y-3">
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-blue-50 p-3 rounded border border-blue-100">
                <p class="text-sm text-gray-500 mb-1">Department Code</p>
                <p class="text-lg font-semibold text-gray-800">{{ currentItem.department_code || 'N/A' }}</p>
              </div>
              <div class="bg-green-50 p-3 rounded border border-green-100">
                <p class="text-sm text-gray-500 mb-1">Department Name</p>
                <p class="text-lg font-semibold text-gray-800">{{ currentItem.department_name }}</p>
              </div>
            </div>
            <div class="bg-purple-50 p-3 rounded border border-purple-100">
              <p class="text-sm text-gray-500 mb-1">College</p>
              <p class="text-lg font-semibold text-gray-800">{{ currentItem.college }}</p>
            </div>
            <div class="bg-yellow-50 p-3 rounded border border-yellow-100">
              <p class="text-sm text-gray-500 mb-1">Dean/Head</p>
              <p class="text-lg font-semibold text-gray-800">{{ currentItem.dean }}</p>
            </div>
            <div v-if="currentItem.office_location" class="bg-gray-50 p-3 rounded border border-gray-100">
              <p class="text-sm text-gray-500 mb-1">Office Location</p>
              <p class="text-lg font-semibold text-gray-800">{{ currentItem.office_location }}</p>
            </div>
            <div v-if="currentItem.contact_email || currentItem.contact_phone" class="bg-red-50 p-3 rounded border border-red-100">
              <p class="text-sm text-gray-500 mb-1">Contact Information</p>
              <p class="text-sm text-gray-800">{{ currentItem.contact_email }}</p>
              <p class="text-sm text-gray-800">{{ currentItem.contact_phone }}</p>
            </div>
          </div>
          <div class="flex justify-end">
            <IconButton
              @click="isViewModalOpen = false"
              icon="times"
              title="Close"
              size="sm"
              color="gray"
              outlined
              class="mt-4 bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded flex items-center justify-center"
            >
              Close
            </IconButton>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Edit Modal -->
    <Teleport to="body">
      <div v-if="isEditModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
          <h3 class="text-2xl font-bold text-green-600 border-b pb-2">Edit Department</h3>
          <div v-if="currentItem" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Name *</label>
                <input type="text" v-model="editForm.department_name" required
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Code</label>
                <input type="text" v-model="editForm.department_code"
                       placeholder="e.g., COE-CPE"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">College *</label>
                <select v-model="editForm.college_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                  <option value="">Select College</option>
                  <option v-for="college in colleges" :key="college.id" :value="college.id">
                    {{ college.college_name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Head</label>
                <select v-model="editForm.department_head_id"
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                  <option value="">Select Department Head</option>
                  <option v-for="head in departmentHeads" :key="head.id" :value="head.id">
                    {{ head.full_name }}
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Office Location</label>
              <input type="text" v-model="editForm.office_location"
                     placeholder="e.g., Room 201, Engineering Building"
                     class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input type="email" v-model="editForm.contact_email"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                <input type="text" v-model="editForm.contact_phone"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="editForm.description" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <IconButton
              @click="isEditModalOpen = false"
              icon="times"
              title="Cancel"
              size="sm"
              color="gray"
              outlined
              class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded flex items-center justify-center"
            >
              Cancel
            </IconButton>
            <IconButton
              @click="saveEdit"
              icon="check"
              title="Save Changes"
              size="sm"
              color="green"
              outlined
              class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded flex items-center justify-center"
            >
              Save Changes
            </IconButton>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Add Modal -->
    <Teleport to="body">
      <div v-if="isAddModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
          <h3 class="text-2xl font-bold text-[#7A0C23] border-b pb-2">Add New Department</h3>
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Name *</label>
                <input type="text" v-model="newDepartment.department_name" required
                       placeholder="e.g., Computer Engineering"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Code</label>
                <input type="text" v-model="newDepartment.department_code"
                       placeholder="e.g., COE-CPE"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">College *</label>
                <select v-model="newDepartment.college_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                  <option value="">Select College</option>
                  <option v-for="college in colleges" :key="college.id" :value="college.id">
                    {{ college.college_name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department Head</label>
                <select v-model="newDepartment.department_head_id"
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
                  <option value="">Select Department Head</option>
                  <option v-for="head in departmentHeads" :key="head.id" :value="head.id">
                    {{ head.full_name }}
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Office Location</label>
              <input type="text" v-model="newDepartment.office_location"
                     placeholder="e.g., Room 201, Engineering Building"
                     class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input type="email" v-model="newDepartment.contact_email"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                <input type="text" v-model="newDepartment.contact_phone"
                       class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="newDepartment.description" rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#7A0C23] focus:border-[#7A0C23]"></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <IconButton
              @click="isAddModalOpen = false"
              icon="times"
              title="Cancel"
              size="sm"
              color="gray"
              outlined
              class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded flex items-center justify-center"
            >
              Cancel
            </IconButton>
            <IconButton
              @click="saveNewDepartment"
              icon="check"
              title="Add Department"
              size="sm"
              color="green"
              outlined
              class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded flex items-center justify-center"
            >
              Add Department
            </IconButton>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import IconButton from '@/Components/IconButton.vue'

const props = defineProps({})

const emit = defineEmits(['created', 'edited', 'deleted'])

// Data state
const departments = ref([])
const colleges = ref([])
const departmentHeads = ref([])
const loading = ref(false)

// Search state
const searchTerm = ref('')

// Modal state
const isViewModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isAddModalOpen = ref(false)
const currentItem = ref(null)

// Form state
const editForm = reactive({
  department_name: '',
  department_code: '',
  college_id: '',
  department_head_id: '',
  description: '',
  office_location: '',
  contact_email: '',
  contact_phone: ''
})

const newDepartment = reactive({
  department_name: '',
  department_code: '',
  college_id: '',
  department_head_id: '',
  description: '',
  office_location: '',
  contact_email: '',
  contact_phone: ''
})

// Pagination state
const pagination = reactive({
  currentPage: 1,
  perPage: 10,
  total: 0,
  lastPage: 1,
  from: 0,
  to: 0
})

// Get pages to display in horizontal format
const getDisplayPages = () => {
  const pages = []
  const current = pagination.currentPage
  const last = pagination.lastPage
  const maxVisible = 7 // Show maximum 7 page numbers including ellipsis

  if (last <= maxVisible) {
    // Show all pages
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
    return pages
  }

  // Always show first page
  pages.push(1)

  // Calculate start and end
  let start = Math.max(2, current - 2)
  let end = Math.min(last - 1, current + 2)

  // Adjust if near start
  if (current <= 3) {
    end = Math.min(last - 1, maxVisible - 1)
  }

  // Adjust if near end
  if (current >= last - 2) {
    start = Math.max(2, last - (maxVisible - 2))
  }

  // Add ellipsis after first page if needed
  if (start > 2) {
    pages.push('...')
  }

  // Add middle pages
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  // Add ellipsis before last page if needed
  if (end < last - 1) {
    pages.push('...')
  }

  // Always show last page if more than 1 page
  if (last > 1) {
    pages.push(last)
  }

  return pages
}

// Fetch data on mount
onMounted(() => {
  fetchDepartments()
  fetchColleges()
  fetchDepartmentHeads()
})

// Fetch departments from API
const fetchDepartments = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/departments', {
      params: {
        page: pagination.currentPage,
        per_page: pagination.perPage,
        search: searchTerm.value
      }
    })

    if (response.data.success) {
      departments.value = response.data.data
      Object.assign(pagination, response.data.meta)
      pagination.from = (pagination.currentPage - 1) * pagination.perPage + 1
      pagination.to = Math.min(pagination.currentPage * pagination.perPage, pagination.total)
    }
  } catch (error) {
    console.error('Error fetching departments:', error)
    alert('Failed to load departments')
  } finally {
    loading.value = false
  }
}

// Fetch colleges for dropdown
const fetchColleges = async () => {
  try {
    const response = await axios.get('/api/departments/colleges')
    if (response.data.success) {
      colleges.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching colleges:', error)
  }
}

// Fetch department heads for dropdown
const fetchDepartmentHeads = async () => {
  try {
    const response = await axios.get('/api/departments/heads')
    if (response.data.success) {
      departmentHeads.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching department heads:', error)
  }
}

// Handle search with debounce
let searchTimeout = null
const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.currentPage = 1
    fetchDepartments()
  }, 300)
}

// Pagination methods
const changePerPage = () => {
  pagination.currentPage = 1
  fetchDepartments()
}

const prevPage = () => {
  if (pagination.currentPage > 1) {
    pagination.currentPage--
    fetchDepartments()
  }
}

const nextPage = () => {
  if (pagination.currentPage < pagination.lastPage) {
    pagination.currentPage++
    fetchDepartments()
  }
}

const goToPage = (page) => {
  if (page !== '...' && page >= 1 && page <= pagination.lastPage) {
    pagination.currentPage = page
    fetchDepartments()
  }
}

// Action Handlers
const handleViewDetails = (item) => {
  currentItem.value = item
  isViewModalOpen.value = true
}

const handleEditDetails = (item) => {
  currentItem.value = item
  Object.assign(editForm, {
    department_name: item.department_name,
    department_code: item.department_code || '',
    college_id: item.college_id,
    department_head_id: item.department_head_id || '',
    description: item.description || '',
    office_location: item.office_location || '',
    contact_email: item.contact_email || '',
    contact_phone: item.contact_phone || ''
  })
  isEditModalOpen.value = true
}

const saveEdit = async () => {
  if (!editForm.department_name || !editForm.college_id) {
    alert('Please fill in all required fields (Department Name and College)')
    return
  }

  try {
    const response = await axios.put(`/api/departments/${currentItem.value.id}`, editForm)

    if (response.data.success) {
      // Update the local department data
      const index = departments.value.findIndex(d => d.id === currentItem.value.id)
      if (index !== -1) {
        departments.value[index] = response.data.data
      }

      isEditModalOpen.value = false
      emit('edited', response.data.data)
    } else {
      alert(response.data.message || 'Failed to update department')
    }
  } catch (error) {
    console.error('Error updating department:', error)
    if (error.response?.data?.errors) {
      alert(Object.values(error.response.data.errors).flat().join('\n'))
    } else {
      alert('Failed to update department')
    }
  }
}

const handleDeleteDetails = async (item) => {
  if (!confirm(`Are you sure you want to delete the department: ${item.department_name}?`)) {
    return
  }

  try {
    const response = await axios.delete(`/api/departments/${item.id}`)

    if (response.data.success) {
      // Remove from local data
      departments.value = departments.value.filter(d => d.id !== item.id)
      emit('deleted', response.data.deleted_name)

      // Refresh data if we deleted the last item on the page
      if (departments.value.length === 0 && pagination.currentPage > 1) {
        pagination.currentPage--
        fetchDepartments()
      }
    } else {
      alert(response.data.message || 'Failed to delete department')
    }
  } catch (error) {
    console.error('Error deleting department:', error)
    if (error.response?.data?.message) {
      alert(error.response.data.message)
    } else {
      alert('Failed to delete department')
    }
  }
}

const openAddModal = () => {
  Object.assign(newDepartment, {
    department_name: '',
    department_code: '',
    college_id: '',
    department_head_id: '',
    description: '',
    office_location: '',
    contact_email: '',
    contact_phone: ''
  })
  isAddModalOpen.value = true
}

const saveNewDepartment = async () => {
  if (!newDepartment.department_name || !newDepartment.college_id) {
    alert('Please fill in all required fields (Department Name and College)')
    return
  }

  try {
    const response = await axios.post('/api/departments', newDepartment)

    if (response.data.success) {
      // Add to local data and refresh
      departments.value.unshift(response.data.data)
      fetchDepartments() // Refresh to get proper pagination

      isAddModalOpen.value = false
      emit('created', response.data.data)
    } else {
      alert(response.data.message || 'Failed to create department')
    }
  } catch (error) {
    console.error('Error creating department:', error)
    if (error.response?.data?.errors) {
      alert(Object.values(error.response.data.errors).flat().join('\n'))
    } else {
      alert('Failed to create department')
    }
  }
}

// Watch for changes in search term
watch(searchTerm, () => {
  handleSearch()
})
</script>

<style scoped>
/* Custom styles */
input:focus, select:focus, textarea:focus {
  outline: none;
  ring-width: 2px;
}

.hover\:scale-110:hover {
  transform: scale(1.1);
}

.transition-transform {
  transition: transform 0.2s ease;
}

/* Prevent text overflow in table cells */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Align icons properly */
.flex.items-center.justify-center {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Pagination button styles */
button:disabled {
  cursor: not-allowed;
}

/* Ensure consistent button heights */
button {
  min-height: 32px;
  transition: all 0.2s ease;
}

/* Table cell styles */
td, th {
  padding: 12px 16px;
}

/* Fixed table layout to prevent overflow */
.overflow-x-hidden {
  overflow-x: hidden !important;
}
</style>
