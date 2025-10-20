<script setup>
import { ref, onMounted, onUnmounted } from "vue";

// --- Component Imports ---
// Assuming these components are correctly defined and imported
import Navbar from "@/Components/Navbar.vue";
import Sidebar from "@/Components/Sidebar.vue";

// ===========================================
// === 1. State Management & Core Functions ===
// ===========================================

// State for sidebar visibility
const sidebarOpen = ref(true);
let intervalId = null;

/** Toggles the visibility state of the sidebar. */
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

// --- Dummy Action Functions ---
// These functions simulate opening different management panels
const manageRoom = (roomType) => alert(`Opening management panel for ${roomType}`);
const addNewRoomType = () => alert('Opening new room type creation form');
const viewAnalytics = () => alert('Opening room analytics dashboard');
const bulkSettings = () => alert('Opening bulk settings panel');

// --- Availability Pulse Effect ---
/** Starts the animation interval for the availability dot. */
const startAvailabilityPulse = () => {
  // Uses a manual class toggle to simulate a pulse effect (alternative to CSS animation)
  // Note: The @keyframes pulse CSS animation is generally cleaner, but the JS toggle
  // was kept to show how intervals can be used.
  intervalId = setInterval(() => {
    const dots = document.querySelectorAll('.availability-dot-js');
    dots.forEach(dot => dot.classList.toggle('opacity-75'));
  }, 2000);
};

// --- Lifecycle Hooks ---
onMounted(() => {
  startAvailabilityPulse();
});

onUnmounted(() => {
  // Clean up the interval when the component is destroyed to prevent memory leaks
  clearInterval(intervalId);
});
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
       

          <h2 class="text-2xl font-bold text-gray-800 mb-6">Room Types Overview</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            
            <div class="room-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
              <div class="relative">
                <div class="h-48 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                  <svg class="w-16 h-16 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l4-4 4 4"></path>
                  </svg>
                </div>
                <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-md"> 8 Available </div>
              </div>
              <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Standard Room</h3>
                <p class="text-gray-600 mb-4 text-sm">Comfortable single or double occupancy with essential amenities.</p>
                <div class="flex items-center justify-between border-t border-b py-3 mb-4">
                  <span class="text-2xl font-extrabold text-blue-600">$89<span class="text-base font-normal text-gray-500">/night</span></span>
                  <span class="text-sm text-gray-500 font-medium">Max 2 guests</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                  <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">WiFi</span>
                  <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">AC</span>
                  <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">TV</span>
                </div>
                <button @click="manageRoom('Standard Room')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-md"> Manage Room Type </button>
              </div>
            </div>

            <div class="room-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
              <div class="relative">
                <div class="h-48 bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center">
                  <svg class="w-16 h-16 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-md"> 5 Available </div>
              </div>
              <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Deluxe Room</h3>
                <p class="text-gray-600 mb-4 text-sm">Spacious room with premium amenities and city view.</p>
                <div class="flex items-center justify-between border-t border-b py-3 mb-4">
                  <span class="text-2xl font-extrabold text-purple-600">$149<span class="text-base font-normal text-gray-500">/night</span></span>
                  <span class="text-sm text-gray-500 font-medium">Max 3 guests</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                  <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">WiFi</span>
                  <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">Minibar</span>
                  <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">Balcony</span>
                </div>
                <button @click="manageRoom('Deluxe Room')" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-md"> Manage Room Type </button>
              </div>
            </div>

            <div class="room-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
              <div class="relative">
                <div class="h-48 bg-gradient-to-r from-amber-400 to-amber-600 flex items-center justify-center">
                  <svg class="w-16 h-16 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-md"> 3 Available </div>
              </div>
              <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Executive Suite</h3>
                <p class="text-gray-600 mb-4 text-sm">Luxury suite with separate living area and premium services.</p>
                <div class="flex items-center justify-between border-t border-b py-3 mb-4">
                  <span class="text-2xl font-extrabold text-amber-600">$299<span class="text-base font-normal text-gray-500">/night</span></span>
                  <span class="text-sm text-gray-500 font-medium">Max 4 guests</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                  <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-medium">Concierge</span>
                  <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-medium">Jacuzzi</span>
                  <span class="bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-medium">Butler</span>
                </div>
                <button @click="manageRoom('Executive Suite')" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-md"> Manage Room Type </button>
              </div>
            </div>

            <div class="room-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
              <div class="relative">
                <div class="h-48 bg-gradient-to-r from-green-400 to-green-600 flex items-center justify-center">
                  <svg class="w-16 h-16 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div class="absolute top-4 right-4 bg-yellow-600 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold shadow-md"> 2 Available </div>
              </div>
              <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Family Room</h3>
                <p class="text-gray-600 mb-4 text-sm">Perfect for families with connecting rooms and kid-friendly amenities.</p>
                <div class="flex items-center justify-between border-t border-b py-3 mb-4">
                  <span class="text-2xl font-extrabold text-green-600">$199<span class="text-base font-normal text-gray-500">/night</span></span>
                  <span class="text-sm text-gray-500 font-medium">Max 6 guests</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                  <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Bunk Beds</span>
                  <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Game Area</span>
                  <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-medium">Kitchenette</span>
                </div>
                <button @click="manageRoom('Family Room')" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-md"> Manage Room Type </button>
              </div>
            </div>
            
            <div class="room-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
              <div class="relative">
                <div class="h-48 bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center">
                  <svg class="w-16 h-16 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0v2a2 2 0 002 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2V8"></path>
                  </svg>
                </div>
                <div class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-md"> Fully Booked </div>
              </div>
              <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Business Room</h3>
                <p class="text-gray-600 mb-4 text-sm">Designed for business travelers with work desk and meeting space.</p>
                <div class="flex items-center justify-between border-t border-b py-3 mb-4">
                  <span class="text-2xl font-extrabold text-gray-600">$129<span class="text-base font-normal text-gray-500">/night</span></span>
                  <span class="text-sm text-gray-500 font-medium">Max 2 guests</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                  <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Work Desk</span>
                  <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Printer</span>
                  <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">Coffee</span>
                </div>
                <button class="w-full bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg cursor-not-allowed shadow-md" disabled> Fully Booked </button>
              </div>
            </div>

            <div class="room-card bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
              <div class="relative">
                <div class="h-48 bg-gradient-to-r from-rose-400 to-rose-600 flex items-center justify-center">
                  <svg class="w-16 h-16 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                  </svg>
                </div>
                <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-md"> 1 Available </div>
              </div>
              <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Penthouse Suite</h3>
                <p class="text-gray-600 mb-4 text-sm">Ultimate luxury with panoramic views and exclusive amenities.</p>
                <div class="flex items-center justify-between border-t border-b py-3 mb-4">
                  <span class="text-2xl font-extrabold text-rose-600">$599<span class="text-base font-normal text-gray-500">/night</span></span>
                  <span class="text-sm text-gray-500 font-medium">Max 8 guests</span>
                </div>
                <div class="flex flex-wrap gap-2 mb-6">
                  <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-xs font-medium">Rooftop</span>
                  <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-xs font-medium">Chef</span>
                  <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-xs font-medium">Spa</span>
                </div>
                <button @click="manageRoom('Penthouse Suite')" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors shadow-md"> Manage Room Type </button>
              </div>
            </div>

          </div>
          <div class="mt-12 bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-5">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              
              <button @click="addNewRoomType"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-3 transition-colors transform hover:scale-[1.02] shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New Room Type
              </button>

              <button @click="viewAnalytics"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-3 transition-colors transform hover:scale-[1.02] shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                View Analytics
              </button>

              <button @click="bulkSettings"
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg flex items-center justify-center gap-3 transition-colors transform hover:scale-[1.02] shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Bulk Settings
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style>
/* Basic CSS Reset/Setup */
body {
  box-sizing: border-box;
  /* Use a nice dashboard-friendly font if not already imported */
  font-family: 'Inter', sans-serif;
}

/* Hover effect for the room cards */
.room-card {
  /* Ensure the hover effect applies to the entire card */
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.room-card:hover {
  /* Subtle lift and stronger shadow on hover */
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
              0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Availability dot pulse animation */
.availability-dot {
  /* Keyframes defined below */
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* The actual pulse animation for the Available count dot */
@keyframes pulse {
  0%, 100% { 
    opacity: 1; 
    transform: scale(1);
  }
  50% { 
    opacity: 0.5; 
    transform: scale(1.1); /* Slight size change for a better effect */
  }
}
</style>