<script setup>
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebarcollege from '@/Components/Sidebarcollege.vue'   // ✅ Correct name, no dash

// Props from Laravel controller
const sidebarVisible = ref(true)
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}
</script>


<template>
  <div class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <Navbar @toggleSidebar="toggleSidebar" />
   
     <!---User Account Title-->
    <div class="flex pt-14 min-h-screen transition-all duration-300">
     
      <!-- Sidebar -->
      <Sidebarcollege @toggleSidebar="toggleSidebar" v-show="sidebarVisible" />

      <!-- Main Content -->
      <main id="main" class="flex-1 p-6 transition-all">
        <h2 class="text-xl font-bold mb-4 text-gray-700">User Account</h2>

        <!-- Search and Add -->
        <div class="mb-4 flex justify-between items-center">
          <input
            type="text"
            placeholder="SEARCH"
            class="border border-gray-300 rounded-full px-4 py-2 w-72 focus:outline-none focus:ring-2 focus:ring-sky-400"
          />
          <button class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow">
            Add Account
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
          <table class="min-w-full border border-gray-200 text-sm text-center">
            <thead class="bg-[#7A0C23] text-white">
              <tr>
                <th class="px-4 py-2 border">NAME</th>
                <th class="px-4 py-2 border">SCHOOL</th>
                <th class="px-4 py-2 border">AGE</th>
                <th class="px-4 py-2 border">ADDRESS</th>
                <th class="px-4 py-2 border">ROOM</th>
                <th class="px-4 py-2 border">START</th>
                <th class="px-4 py-2 border">END</th>
                <th class="px-4 py-2 border">ACTION</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(user, index) in users"
                :key="index"
                class="odd:bg-white even:bg-gray-100 hover:bg-gray-200"
              >
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.school }}</td>
                <td class="px-4 py-2">{{ user.age }}</td>
                <td class="px-4 py-2">{{ user.address }}</td>
                <td class="px-4 py-2">{{ user.room }}</td>
                <td class="px-4 py-2">{{ user.start }}</td>
                <td class="px-4 py-2">{{ user.end }}</td>
                <td class="px-4 py-2 space-x-2">
                  <a href="#" class="text-green-600 hover:text-green-800">👁️</a>
                  <a href="#" class="text-green-600 hover:text-green-800">✏️</a>
                  <a href="#" class="text-red-600 hover:text-red-800">🗑️</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
</template>
