<script setup>
import { ref } from 'vue';

// Assuming these components are available in your project structure
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';

const sidebarOpen = ref(true);

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

const activeTable = ref('residents');

function setActiveTable(tableKey) {
  activeTable.value = tableKey;
}

const manageRoom = (roomType) => console.log(`Opening management panel for ${roomType}`);
const addNewRoomType = () => console.log('Opening new room type creation form');
const viewAnalytics = () => console.log('Opening room analytics dashboard');
const bulkSettings = () => console.log('Opening bulk settings panel');
const editResident = (name) => console.log(`Editing resident: ${name}`);
const deleteResident = (name) => console.log(`Deleting resident: ${name}`);


const colorMap = {
  blue: {
    text: 'text-blue-600',
    price: 'text-blue-600',
    amenityBg: 'bg-blue-50',
    amenityText: 'text-blue-700',
    button: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
  },
  purple: {
    text: 'text-purple-600',
    price: 'text-purple-600',
    amenityBg: 'bg-purple-50',
    amenityText: 'text-purple-700',
    button: 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
  },
  amber: {
    text: 'text-amber-600',
    price: 'text-amber-600',
    amenityBg: 'bg-amber-50',
    amenityText: 'text-amber-700',
    button: 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
  },
  green: {
    text: 'text-green-600',
    price: 'text-green-600',
    amenityBg: 'bg-green-50',
    amenityText: 'text-green-700',
    button: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
  },
  rose: {
    text: 'text-rose-600',
    price: 'text-rose-600',
    amenityBg: 'bg-rose-50',
    amenityText: 'text-rose-700',
    button: 'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500',
  },
  gray: {
    text: 'text-gray-600',
    price: 'text-gray-600',
    amenityBg: 'bg-gray-50',
    amenityText: 'text-gray-700',
    button: 'bg-gray-400 cursor-not-allowed',
  },
};

const roomTypes = ref([
  {
    name: 'Standard Room',
    description: 'Comfortable single or double occupancy with essential amenities.',
    price: '$89/night',
    maxGuests: 2,
    amenities: ['WiFi', 'AC', 'TV'],
    available: 8,
    isBooked: false,
    colorKey: 'blue',
  },
  {
    name: 'Deluxe Room',
    description: 'Spacious room with premium amenities and city view.',
    price: '$149/night',
    maxGuests: 3,
    amenities: ['WiFi', 'Minibar', 'Balcony'],
    available: 5,
    isBooked: false,
    colorKey: 'purple',
  },
  {
    name: 'Executive Suite',
    description: 'Luxury suite with separate living area and premium services.',
    price: '$299/night',
    maxGuests: 4,
    amenities: ['Concierge', 'Jacuzzi', 'Butler'],
    available: 3,
    isBooked: false,
    colorKey: 'amber',
  },
  {
    name: 'Family Room',
    description: 'Perfect for families with connecting rooms and kid-friendly amenities.',
    price: '$199/night',
    maxGuests: 6,
    amenities: ['Bunk Beds', 'Game Area', 'Kitchenette'],
    available: 2,
    isBooked: false,
    colorKey: 'green',
  },
  {
    name: 'Business Room',
    description: 'Designed for business travelers with work desk and meeting space.',
    price: '$129/night',
    maxGuests: 2,
    amenities: ['Work Desk', 'Printer', 'Coffee'],
    available: 0,
    isBooked: true,
    colorKey: 'gray',
  },
  {
    name: 'Penthouse Suite',
    description: 'Ultimate luxury with panoramic views and exclusive amenities.',
    price: '$599/night',
    maxGuests: 8,
    amenities: ['Rooftop', 'Chef', 'Spa'],
    available: 1,
    isBooked: false,
    colorKey: 'rose',
  },
]);

const residents = ref([
  { name: 'REY JANOSALEM', school: 'CEBU EASTERN COLLEGE', age: 29, address: 'CEBU CITY', room: 'ROOM 101', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
  { name: 'MARIA SANTOS', school: 'UNIVERSITY OF CEBU', age: 22, address: 'LAPU-LAPU CITY', room: 'ROOM 203', start: 'SEPTEMBER 01, 2025', end: 'JUNE 30, 2026' },
  { name: 'KEN TANG', school: 'USC', age: 34, address: 'TALISAY CITY', room: 'ROOM 310', start: 'AUGUST 15, 2025', end: 'APRIL 10, 2026' },
  { name: 'LEE CHEEN', school: 'CEBU TECH', age: 25, address: 'MANDAUE CITY', room: 'ROOM 112', start: 'JULY 20, 2025', end: 'MAY 15, 2026' },
  { name: 'RUSSELL EVAN', school: 'Velez College', age: 31, address: 'CEBU CITY', room: 'ROOM 201', start: 'AUGUST 08, 2025', end: 'MARCH 25, 2026' },
]);
</script>

<template>
  <div class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">
    <Navbar @toggle-sidebar="toggleSidebar" />

    <div class="flex flex-1 pt-14 overflow-hidden">

      <div
        class="fixed top-14 left-0 h-[calc(100vh-3.5rem)] bg-white shadow-xl z-30 transition-all duration-300"
        :class="sidebarOpen ? 'w-64' : 'w-20'"
      >
        <Sidebar :sidebar-open="sidebarOpen" />
      </div>

      <main
        id="mainContent"
        class="flex-1 bg-gray-50 transition-all duration-300 overflow-y-auto"
        :class="sidebarOpen ? 'ml-64' : 'ml-20'"
      >

        <div class="p-6 md:p-10">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">User and Room Management</h2>


          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="rounded-xl text-center shadow-lg overflow-hidden flex-1 min-w-[150px]">
              <div class="bg-cyan-500 text-white p-3">
                <h3 class="text-lg font-semibold">Total Accounts</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-4xl font-extrabold text-gray-800">120</p>
              </div>
            </div>
            <div class="rounded-xl text-center shadow-lg overflow-hidden flex-1 min-w-[150px]">
              <div class="bg-purple-600 text-white p-3">
                <h3 class="text-lg font-semibold">Total Department</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-4xl font-extrabold text-gray-800">15</p>
              </div>
            </div>
            <div class="rounded-xl text-center shadow-lg overflow-hidden flex-1 min-w-[150px]">
              <div class="bg-orange-500 text-white p-3">
                <h3 class="text-lg font-semibold">Total Colleges</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-4xl font-extrabold text-gray-800">5</p>
              </div>
            </div>
            <div class="rounded-xl text-center shadow-lg overflow-hidden flex-1 min-w-[150px]">
              <div class="bg-red-600 text-white p-3">
                <h3 class="text-lg font-semibold">Total Rooms</h3>
              </div>
              <div class="bg-white p-3">
                <p class="text-4xl font-extrabold text-gray-800">250</p>
              </div>
            </div>
          </div>

          <div class="flex justify-between items-center mb-6">
            <div class="text-xl font-bold text-gray-800">
              {{ activeTable === 'residents' ? 'Current Residents' : 'Room Types Management' }}
            </div>
            <div class="inline-flex rounded-full shadow-lg p-1 bg-white border border-gray-200" role="group">
              <button
                @click="setActiveTable('room_types')"
                :class="[
                  activeTable === 'room_types'
                    ? 'bg-gray-200 text-gray-800 font-semibold'
                    : 'text-gray-600 hover:bg-gray-100',
                  'py-2 px-6 rounded-full text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300'
                ]"
                aria-pressed="activeTable === 'room_types'"
              >
                Room Types
              </button>
              <button
                @click="setActiveTable('residents')"
                :class="[
                  activeTable === 'residents'
                    ? 'bg-red-700 text-white font-semibold shadow-inner'
                    : 'text-gray-600 hover:bg-gray-100',
                  'py-2 px-6 rounded-full text-base transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-700'
                ]"
                aria-pressed="activeTable === 'residents'"
              >
                Current Residents
              </button>
            </div>
          </div>

          <div v-if="activeTable === 'room_types'" class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-10">
            <table class="min-w-full divide-y divide-gray-200 table-fixed">
              <caption class="sr-only">List of all available room types and their details.</caption>
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="w-1/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Room Type </th>
                  <th scope="col" class="w-2/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Rooms </th>
                    <th scope="col" class="w-2/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Building</th>
                  <th scope="col" class="w-1/12 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">College</th>
                  <th scope="col" class="w-[10%] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              
                  <th scope="col" class="w-[10%] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"> Action </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="room in roomTypes" :key="room.name" class="hover:bg-gray-50 transition-colors">
                  <td class="px-2 py-4 text-sm font-semibold text-gray-900">
                    {{ room.name }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-500 text-wrap">
                    {{ room.building }}
                  </td>
                  <td :class="[colorMap[room.colorKey].price, 'px-2 py-4 text-lg font-extrabold text-center']">
                    {{ room.college }}
                  </td>
                  <td class="px-2 py-4 text-sm text-gray-500 text-center">
                    Max {{ room.location }}
                  </td>
                  <td class="px-2 py-4">
                    <div class="flex flex-wrap gap-1">
                      <span v-for="amenity in room.amenities" :key="amenity"
                        :class="[colorMap[room.colorKey].amenityBg, colorMap[room.colorKey].amenityText, 'px-2 py-0.5 rounded-full text-xs font-medium']">
                        {{ amenity }}
                      </span>
                    </div>
                  </td>
                  <td class="px-2 py-4 text-center">
                    <span v-if="room.isBooked" class="bg-red-100 text-red-800 px-1 py-0.5 rounded-full text-xs font-semibold inline-flex items-center justify-center space-x-1">
                      <span class="w-2 h-2 bg-red-600 rounded-full inline-block"></span>
                      <span>Fully Booked</span>
                    </span>
                  
                  </td>
                  <td class="px-2 py-4 text-center text-sm font-medium">
                    <button @click="manageRoom(room.name)"
                      :class="[
                        room.isBooked ? colorMap.gray.button : colorMap[room.colorKey].button,
                        'text-white font-medium py-2 px-3 rounded-lg transition-colors text-xs whitespace-nowrap focus:outline-none focus:ring-4 focus:ring-opacity-50'
                      ]"
                      :disabled="room.isBooked">
                      {{ room.isBooked ? 'View' : 'Manage' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="activeTable === 'room_types'" class="mt-12 bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-5">Quick Actions for Room Types</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

              <button @click="addNewRoomType"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-3 transition-colors transform hover:scale-[1.02] shadow-md focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Room Type
              </button>

              <button @click="viewAnalytics"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-3 transition-colors transform hover:scale-[1.02] shadow-md focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                View Analytics
              </button>

              <button @click="bulkSettings"
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-3 transition-colors transform hover:scale-[1.02] shadow-md focus:outline-none focus:ring-4 focus:ring-purple-500 focus:ring-opacity-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Bulk Settings
              </button>
            </div>
          </div>


          <div v-if="activeTable === 'residents'" class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-10">
            <table class="min-w-full divide-y divide-gray-200 table-fixed">
              <caption class="sr-only">List of all current residents and their details.</caption>
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="w-1/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> NAME </th>
                  <th scope="col" class="w-1/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> SCHOOL </th>
                  <th scope="col" class="w-[5%] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"> AGE </th>
                  <th scope="col" class="w-1/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> ADDRESS </th>
                  <th scope="col" class="w-[8%] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> ROOM </th>
                  <th scope="col" class="w-1/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> START </th>
                  <th scope="col" class="w-1/6 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> END </th>
                  <th scope="col" class="w-[5%] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"> ACT. </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="resident in residents" :key="resident.name" class="hover:bg-gray-50 transition-colors">
                  <td class="px-2 py-4 text-sm font-medium text-gray-900"> {{ resident.name }} </td>
                  <td class="px-2 py-4 text-sm text-gray-500"> {{ resident.school }} </td>
                  <td class="px-2 py-4 text-sm text-gray-500 text-center"> {{ resident.age }} </td>
                  <td class="px-2 py-4 text-sm text-gray-500"> {{ resident.address }} </td>
                  <td class="px-2 py-4 text-sm text-gray-500"> {{ resident.room }} </td>
                  <td class="px-2 py-4 text-sm text-gray-500"> {{ resident.start }} </td>
                  <td class="px-2 py-4 text-sm text-gray-500"> {{ resident.end }} </td>
                  <td class="px-2 py-4 text-sm font-medium flex justify-center space-x-1">
                    <button @click="editResident(resident.name)" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md" title="Edit">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    <button @click="deleteResident(resident.name)" class="text-red-600 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded-md" title="Delete">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>