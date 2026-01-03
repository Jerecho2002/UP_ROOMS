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
          class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150"
        >
          Add Department
        </IconButton>
      </div>

      <div class="overflow-x-auto border rounded-lg border-yellow-400">
        <table class="min-w-full divide-y divide-yellow-300">
          <thead class="bg-[#7A0C23] text-white">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Department Code</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Department Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">College</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Dean/Head</th>
              <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-yellow-400 bg-white">
            <tr v-for="item in departments" :key="item.id" class="hover:bg-gray-300">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.department_code || 'N/A' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.department_name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.college }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.dean }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                <IconButton
                  @click="handleViewDetails(item)"
                  icon="eye"
                  title="View Details"
                  size="sm"
                  color="blue"
                  class="hover:scale-110 transition-transform"
                />
                <IconButton
                  @click="handleEditDetails(item)"
                  icon="edit"
                  title="Edit Department"
                  size="sm"
                  color="green"
                  class="hover:scale-110 transition-transform"
                />
                <IconButton
                  @click="handleDeleteDetails(item)"
                  icon="delete"
                  title="Delete Department"
                  size="sm"
                  color="red"
                  class="hover:scale-110 transition-transform"
                />
              </td>
            </tr>
            <tr v-if="loading">
              <td colspan="5" class="px-6 py-4 text-center">
                <div class="flex justify-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#7A0C23]"></div>
                </div>
              </td>
            </tr>
            <tr v-else-if="departments.length === 0 && !loading">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                No departments found{{ searchTerm ? ' matching "' + searchTerm + '"' : '' }}.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-gray-50 px-6 py-4 border-t border-yellow-400">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
          <div class="text-sm text-gray-600">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
          </div>

          <div class="flex items-center space-x-2">
            <select
              v-model="pagination.perPage"
              @change="changePerPage"
              class="text-sm border border-gray-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-2 focus:ring-[#7A0C23]"
            >
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-600">per page</span>
          </div>

          <div class="flex items-center space-x-2">
            <IconButton
              @click="prevPage"
              :disabled="pagination.currentPage === 1"
              icon="chevronLeft"
              title="Previous Page"
              size="sm"
              color="gray"
              outlined
              :class="[
                'px-3 py-1.5 rounded text-sm font-medium',
                pagination.currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              Previous
            </IconButton>

            <div class="flex items-center space-x-1">
              <button
                v-for="page in pagination.lastPage"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-1.5 rounded border text-sm font-medium min-w-[36px]',
                  pagination.currentPage === page
                    ? 'bg-[#7A0C23] text-white border-[#7A0C23]'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </div>

            <IconButton
              @click="nextPage"
              :disabled="pagination.currentPage === pagination.lastPage"
              icon="chevronRight"
              title="Next Page"
              size="sm"
              color="gray"
              outlined
              :class="[
                'px-3 py-1.5 rounded text-sm font-medium',
                pagination.currentPage === pagination.lastPage ? 'opacity-50 cursor-not-allowed' : ''
              ]"
            >
              Next
            </IconButton>
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
              class="mt-4 bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded"
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
              class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded"
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
              class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded"
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
              class="bg-gray-400 hover:bg-gray-500 text-white py-2 px-4 rounded"
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
              class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded"
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
import { ref, reactive, onMounted, watch } from 'vue'
import axios from 'axios'
import IconButton from '@/Components/IconButton.vue'

const props = defineProps({
  // Remove hardcoded data prop since we'll fetch from API
})

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
  pagination.currentPage = page
  fetchDepartments()
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
</style>
