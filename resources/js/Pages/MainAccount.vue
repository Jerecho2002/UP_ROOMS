<script setup>
import MainAccountLayout from '@/Layouts/MainAccountLayout.vue';

// 1. PROPS/MOCK DATA
// Define props to receive data from the backend (e.g., Laravel/Inertia controller)
defineProps({
  // Defensive prop definitions with defaults
  totalAccounts: { type: Number, default: 0 },
  totalDepartments: { type: Number, default: 0 },
  totalColleges: { type: Number, default: 0 },
  totalRooms: { type: Number, default: 0 },
  // Mock data structure based on the table columns in the image
  tableData: {
    type: Array,
    default: () => [
      { id: 1, name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
      { id: 2, name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
      { id: 3, name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
      { id: 4, name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
      { id: 5, name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
    ],
  },
});
</script>

<template>
  <MainAccountLayout>
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">

      <div class="rounded-xl text-center shadow-lg overflow-hidden">
        <div class="bg-cyan-500 text-white p-3 font-semibold">
          Total Accounts
        </div>
        <div class="bg-white p-3">
          <p class="text-3xl font-bold text-gray-800">{{ totalAccounts }}</p>
        </div>
      </div>

      <div class="rounded-xl text-center shadow-lg overflow-hidden">
        <div class="bg-purple-600 text-white p-3 font-semibold">
          Total Department
        </div>
        <div class="bg-white p-3">
          <p class="text-3xl font-bold text-gray-800">{{ totalDepartments }}</p>
        </div>
      </div>

      <div class="rounded-xl text-center shadow-lg overflow-hidden">
        <div class="bg-orange-500 text-white p-3 font-semibold">
          Total Colleges
        </div>
        <div class="bg-white p-3">
          <p class="text-3xl font-bold text-gray-800">{{ totalColleges }}</p>
        </div>
      </div>

      <div class="rounded-xl text-center shadow-lg overflow-hidden">
        <div class="bg-red-500 text-white p-3 font-semibold">
          Total Rooms
        </div>
        <div class="bg-white p-3">
          <p class="text-3xl font-bold text-gray-800">{{ totalRooms }}</p>
        </div>
      </div>

    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-xl mb-6">
      <table class="min-w-full text-sm text-center border-collapse">
        <thead class="bg-[#800020] text-white">
          <tr>
            <th class="px-4 py-3 font-semibold text-left">NAME</th>
            <th class="px-4 py-3 font-semibold text-left hidden sm:table-cell">SCHOOL</th>
            <th class="px-4 py-3 font-semibold hidden md:table-cell">AGE</th>
            <th class="px-4 py-3 font-semibold hidden md:table-cell">ADDRESS</th>
            <th class="px-4 py-3 font-semibold hidden md:table-cell">ROOM</th>
            <th class="px-4 py-3 font-semibold hidden lg:table-cell">START</th>
            <th class="px-4 py-3 font-semibold hidden lg:table-cell">END</th>
            <th class="px-4 py-3 font-semibold">ACTION</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          <tr v-if="tableData.length === 0">
            <td colspan="8" class="px-4 py-6 text-gray-500 italic">No records found.</td>
          </tr>

          <tr
            v-for="item in tableData"
            :key="item.id"
            class="odd:bg-white even:bg-gray-50 hover:bg-gray-100"
          >
            <td class="px-4 py-3 text-left">{{ item.name }}</td>
            <td class="px-4 py-3 text-left hidden sm:table-cell">{{ item.school }}</td>
            <td class="px-4 py-3 hidden md:table-cell">{{ item.age }}</td>
            <td class="px-4 py-3 hidden md:table-cell">{{ item.address }}</td>
            <td class="px-4 py-3 hidden md:table-cell">{{ item.room }}</td>
            <td class="px-4 py-3 hidden lg:table-cell">{{ item.start }}</td>
            <td class="px-4 py-3 hidden lg:table-cell">{{ item.end }}</td>
            
            <td class="px-4 py-3 space-x-2">
              <a href="#" class="text-green-500 hover:text-green-700" title="Edit">✏️</a>
              <a href="#" class="text-red-500 hover:text-red-700" title="Delete">🗑️</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </MainAccountLayout>
</template>

<style scoped>
/* No additional styles needed */
</style>