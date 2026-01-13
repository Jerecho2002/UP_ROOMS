<script setup>
import { ref } from "vue";
import Navbar from "@/Components/Navbar.vue";
import Sidebar from "@/Components/Sidebar.vue";
import CollegeTable from "@/Components/CollegeModals/CollegeTable.vue";
import MessageFunction from "@/Components/MessageFunction.vue";

const sidebarVisible = ref(true);
const toggleSidebar = () => (sidebarVisible.value = !sidebarVisible.value);

// --- TOAST STATES ---
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedCollegeName = ref("");

// Toast duration handler
const triggerToast = (type, name = "") => {
  if (type === "create") showCreateSuccess.value = true;
  if (type === "edit") showEditSuccess.value = true;
  if (type === "delete") {
    deletedCollegeName.value = name;
    showDeleteSuccess.value = true;
  }

  setTimeout(() => {
    showCreateSuccess.value = false;
    showEditSuccess.value = false;
    showDeleteSuccess.value = false;
  }, 3000);
};

// --- CATCH EVENTS FROM CHILD ---
const handleCreated = () => triggerToast("create");
const handleEdited = () => triggerToast("edit");
const handleDeleted = (name) => triggerToast("delete", name);
</script>

<template>
  <div class="bg-gray-100 font-sans min-h-screen">

    <Navbar @toggleSidebar="toggleSidebar" />

    <div class="flex pt-14 min-h-screen w-full">
      <Sidebar
        v-show="sidebarVisible"
        class="fixed top-14 left-0 h-[calc(100%-3.5rem)] w-56"
      />

      <main
        id="mainContent"
        class="flex-1 p-6 bg-gray-200 transition-all duration-300 w-full"
        :style="sidebarVisible ? 'margin-left: 14rem;' : 'margin-left: 0;'"
      >
              <div class="mt-5 mb-12">
            <h1 class="ml-10 text-xl md:text-2xl font-bold text-[#7A0C23] mb-1">College Management</h1>
           <div class="absolute right-6 top-14 z-20">
                    <div class="text-sm text-gray-500 whitespace-nowrap ">

        </div>

<div class="mt-10 absolute right-6 top-2 z-20">
                    <div class="text-sm text-gray-500 whitespace-nowrap ">
                        <span>UPCEBU > COLLEGE</span>
                    </div>
                </div>
     </div>
</div>

        <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 mb-6 ml-8 mt-6">
          <!-- CHILD COMPONENT WITH EVENTS -->
          <CollegeTable
          />
        </div>
      </main>
    </div>

    <!-- GLOBAL TOAST COMPONENT -->
    <MessageFunction
      :showCreateSuccess="showCreateSuccess"
      :showEditSuccess="showEditSuccess"
      :showDeleteSuccess="showDeleteSuccess"
      :deletedItemName="deletedCollegeName"
      createMessage="College created successfully!"
      editMessage="College updated successfully!"
      deleteMessage="College deleted successfully!"
    />
  </div>
</template>

<style scoped>
/* Ensure main content area doesn't overflow */
#mainContent {
    overflow-x: hidden;
}

/* Fix layout for smaller screens */
@media (max-width: 768px) {
    .ml-8 {
        margin-left: 1rem !important;
    }

    .ml-10 {
        margin-left: 1rem !important;
    }

    .absolute.right-6 {
        right: 1rem !important;
    }
}

/* Prevent content shifting */
.bg-white.shadow-lg.rounded-lg {
    min-height: 500px;
}
</style>
