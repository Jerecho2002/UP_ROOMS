<template>
  <AppLayout>
    <!-- UserAccountTable Component -->
    <UserAccountTable :users="users" @open-modal="handleOpenModal" />

    <!-- Toast Notifications -->
    <MessageFunction
      :show-create-success="showCreateSuccess"
      :show-edit-success="showEditSuccess"
      :show-delete-success="showDeleteSuccess"
      :deleted-user-name="deletedUserName"
      @close-create="closeCreateToast"
      @close-edit="closeEditToast"
      @close-delete="closeDeleteToast"
    />

    <!-- User Modal -->
    <UserModal
      :is-visible="isModalVisible"
      :type="modalType"
      :user="modalData"
      @close="handleCloseModal"
      @data-updated="handleDataUpdated"
    />
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import UserAccountTable from '@/Components/UserAccountModal/UserAccountTable.vue';
import UserModal from '@/Components/UserAccountModal/UserModal.vue';
import MessageFunction from '@/Components/MessageFunction.vue';
import axios from 'axios';

// Get current user from Inertia props
const currentUser = $page.props.auth.user;

// Check if user has permission to access this page
if (!['Admin', 'SYSADMIN'].includes(currentUser.role)) {
  // Redirect unauthorized users using Inertia
  window.location.href = '/MainDashboard';
}

// --- Data State ---
const users = ref([]);
const nextId = ref(16);

// --- Modal and Toast States ---
const isModalVisible = ref(false);
const modalType = ref(null);
const modalData = ref(null);
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedUserName = ref('');

// Fetch users from database
const fetchUsers = async () => {
  try {
    // In a real app, you would make an API call here
    // For demo purposes, we'll use mock data
    users.value = Array.from({ length: 35 }, (_, i) => ({
      id: i + 1,
      username: `user_${i + 1}`,
      email: `user${i + 1}@example.com`,
      first_name: i % 2 === 0 ? `Alice${i + 1}` : `Bob${i + 1}`,
      last_name: `Smith${i + 1}`,
      role: ['Admin', 'Staff', 'Faculty','DPTAPR','AO','ADPD','OCS','SYSADMIN','USER'][i % 9],
      department: ['Computer Science', 'Electrical Engineering', 'Mechanical Engineering', 'Physics', 'Mathematics'][i % 5],
      college: ['College of Engineering (CoE)', 'College of Arts and Sciences (CAS)', 'College of Business and Accountancy (CBA)', 'College of Education (CoEd)', 'College of Information Technology (CIT)'][i % 5],
      permissions: getDefaultPermissions(i % 9)
    }));
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

function getDefaultPermissions(roleIndex) {
  const permissionsMap = {
    0: ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'], // Admin
    1: ['Can Book', 'Staff Work'], // Staff
    2: ['Can Book', 'User Type Only'], // Faculty
    3: ['Can Approve', 'Can Book', 'User Type Only'], // DPTAPR
    4: ['Can Approve', 'Can Edit', 'Staff Work'], // AO
    5: ['Can Approve', 'Can Edit'], // ADPD
    6: ['Can Approve', 'Can Edit', 'Can Book'], // OCS
    7: ['Can Approve', 'Can Edit', 'Can Book', 'Staff Work', 'User Type Only'], // SYSADMIN
    8: ['Can Book', 'User Type Only'] // USER
  };
  return permissionsMap[roleIndex] || [];
}

// --- Modal Functions ---
const handleOpenModal = (type, data = null) => {
  modalType.value = type;
  modalData.value = data;
  isModalVisible.value = true;
};

const handleCloseModal = () => {
  isModalVisible.value = false;
  modalData.value = null;
  modalType.value = null;
};

// --- Toast Functions ---
const triggerToast = (type, name = "") => {
  showCreateSuccess.value = false;
  showEditSuccess.value = false;
  showDeleteSuccess.value = false;

  if (type === "create") {
    showCreateSuccess.value = true;
  } else if (type === "edit") {
    showEditSuccess.value = true;
  } else if (type === "delete") {
    deletedUserName.value = name;
    showDeleteSuccess.value = true;
  }

  setTimeout(() => {
    showCreateSuccess.value = false;
    showEditSuccess.value = false;
    showDeleteSuccess.value = false;
    deletedUserName.value = "";
  }, 3000);
};

// --- CRUD Operations ---
const handleDataUpdated = async (data, type) => {
  try {
    switch (type) {
      case 'add':
        // In real app: await axios.post('/api/users', data);
        data.id = nextId.value++;
        users.value.unshift(data);
        triggerToast("create");
        break;
      case 'edit':
        // In real app: await axios.put(`/api/users/${data.id}`, data);
        const index = users.value.findIndex(u => u.id === data.id);
        if (index !== -1) {
          users.value[index] = data;
        }
        triggerToast("edit");
        break;
      case 'delete':
        // In real app: await axios.delete(`/api/users/${data.id}`);
        users.value = users.value.filter(u => u.id !== data.id);
        triggerToast("delete", data.username);
        break;
    }
  } catch (error) {
    console.error(`Error ${type} user:`, error);
  } finally {
    handleCloseModal();
  }
};

// --- Close Toast Handlers ---
const closeCreateToast = () => showCreateSuccess.value = false;
const closeEditToast = () => showEditSuccess.value = false;
const closeDeleteToast = () => {
  showDeleteSuccess.value = false;
  deletedUserName.value = '';
};

// Fetch users on component mount
onMounted(() => {
  fetchUsers();
});
</script>
