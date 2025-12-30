<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import UserAccountTable from '@/Components/UserAccountModal/UserAccountTable.vue';
import UserModal from '@/Components/UserAccountModal/UserModal.vue';
import MessageFunction from '@/Components/MessageFunction.vue';



// --- Props ---
const props = defineProps({
    initialUsers: {
        type: Array,
        default: () => []
    }
});

// --- Data State ---
const users = ref([]);
const isLoading = ref(false);

// --- Toast States ---
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedUserName = ref('');

// --- Layout State ---
const sidebarVisible = ref(true)
const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}

// --- Modal State ---
const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

// --- Database Methods (Using Laravel Controllers) ---
const fetchUsers = async () => {
    isLoading.value = true;
    try {
        // In a real app, you might fetch from controller
        // For now, use the initial users passed from Laravel
        users.value = props.initialUsers;
    } catch (error) {
        console.error('Error fetching users:', error);
    } finally {
        isLoading.value = false;
    }
};

const addUser = async (newUser) => {
    try {
        const response = await fetch('/user-accounts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            body: JSON.stringify(newUser)
        });

        const result = await response.json();

        if (result.success) {
            // Add the new user to the local array
            users.value.unshift(result.user);
            return { success: true, data: result.user };
        } else {
            return { success: false, error: result };
        }
    } catch (error) {
        console.error('Error adding user:', error);
        return { success: false, error: { message: 'Network error' } };
    }
};

const updateUser = async (updatedUser) => {
    try {
        const response = await fetch(`/user-accounts/${updatedUser.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            body: JSON.stringify(updatedUser)
        });

        const result = await response.json();

        if (result.success) {
            // Update the user in the local array
            const index = users.value.findIndex(u => u.id === updatedUser.id);
            if (index !== -1) {
                users.value[index] = result.user;
            }
            return { success: true, data: result.user };
        } else {
            return { success: false, error: result };
        }
    } catch (error) {
        console.error('Error updating user:', error);
        return { success: false, error: { message: 'Network error' } };
    }
};

const deleteUser = async (userId) => {
    try {
        const response = await fetch(`/user-accounts/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            }
        });

        const result = await response.json();

        if (result.success) {
            // Remove the user from the local array
            users.value = users.value.filter(u => u.id !== userId);
            return { success: true, username: result.username };
        } else {
            return { success: false, error: result.message };
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        return { success: false, error: 'Network error' };
    }
};

// --- Event Handlers ---
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

const handleDataUpdated = async (data, type) => {
    let result;

    switch (type) {
        case 'add':
            result = await addUser(data);
            if (result.success) {
                triggerToast("create");
                handleCloseModal();
            } else {
                return result;
            }
            break;
        case 'edit':
            result = await updateUser(data);
            if (result.success) {
                triggerToast("edit");
                handleCloseModal();
            } else {
                return result;
            }
            break;
        case 'delete':
            result = await deleteUser(data.id);
            if (result.success) {
                triggerToast("delete", result.username);
                handleCloseModal();
            }
            break;
    }
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

const closeCreateToast = () => showCreateSuccess.value = false;
const closeEditToast = () => showEditSuccess.value = false;
const closeDeleteToast = () => {
    showDeleteSuccess.value = false;
    deletedUserName.value = '';
};

// Lifecycle
onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <div class="bg-gray-200 font-sans min-h-screen">
        <!-- Toast Notifications -->
        <MessageFunction
            :show-create-success="showCreateSuccess"
            :show-edit-success="showEditSuccess"
            :show-delete-success="showDeleteSuccess"
            :deleted-room-name="deletedUserName"
            @close-create="closeCreateToast"
            @close-edit="closeEditToast"
            @close-delete="closeDeleteToast"
        />

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#7A0C23] mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading users...</p>
            </div>
        </div>

        <Navbar @toggle-sidebar="toggleSidebar" />

        <div class="flex pt-10 min-h-screen transition-all duration-300">
            <Sidebar v-show="sidebarVisible" class="fixed top-5 left-0 h-full z-20 w-64 lg:relative" />

            <main id="main" class="flex-1 overflow-y-auto p-0 md:p-6 bg-gray-200">
                <UserAccountTable
                    :users="users"
                    :loading="isLoading"
                    @open-modal="handleOpenModal"
                />
            </main>
        </div>

        <UserModal
            :is-visible="isModalVisible"
            :type="modalType"
            :user="modalData"
            @close="handleCloseModal"
            @data-updated="handleDataUpdated"
        />
    </div>
</template>
