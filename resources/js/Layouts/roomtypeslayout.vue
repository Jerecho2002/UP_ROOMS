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
          <h2 class="text-2xl font-bold text-[#7A0C23] mb-6">ROOM TYPE </h2>



          <div class="flex justify-between items-center mb-6">
            <div class="text-xl font-bold text-[#7A0C23]">
              {{ activeTable === '' ? '' : 'Room List 📃' }}
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

        

         
        </div>
      </main>
    </div>
  </div>
</template>