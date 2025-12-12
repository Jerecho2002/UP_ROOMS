<script setup>
import { ref } from "vue";
import Navbar from "@/Components/Navbar.vue";
import Sidebar from "@/Components/Sidebar.vue";
import CollegeTableComponent from "@/Components/CollegeModals/CollegeStatsAndLinks.vue";
import MessageFunction from "@/Components/Messagefunction.vue";

const sidebarVisible = ref(true);
const toggleSidebar = () => (sidebarVisible.value = !sidebarVisible.value);

// --- TOAST STATES ---
const showCreateSuccess = ref(false);
const showEditSuccess = ref(false);
const showDeleteSuccess = ref(false);
const deletedRoomName = ref("");

// Toast duration handler
const triggerToast = (type, name = "") => {
  if (type === "create") showCreateSuccess.value = true;
  if (type === "edit") showEditSuccess.value = true;
  if (type === "delete") {
    deletedRoomName.value = name;
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
        <h1 class="text-xl font-bold text-[#7A0C23] mt-3 ml-10 mb-3">COLLEGE DASHBOARD</h1>

        <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 mb-6 ml-8 mt-6">
          <!-- CHILD COMPONENT WITH EVENTS -->
          <CollegeTableComponent
            @created="handleCreated"
            @edited="handleEdited"
            @deleted="handleDeleted"
          />
        </div>
      </main>
    </div>

    <!-- GLOBAL TOAST COMPONENT -->
    <MessageFunction
      :showCreateSuccess="showCreateSuccess"
      :showEditSuccess="showEditSuccess"
      :showDeleteSuccess="showDeleteSuccess"
      :deletedRoomName="deletedRoomName"
    />
  </div>
</template>
