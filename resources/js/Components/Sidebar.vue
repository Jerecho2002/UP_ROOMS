<template>
  <aside 
    v-show="sidebarOpen" 
    id="sidebar" 
    class="w-64 bg-white text-black h-screen p-4 flex flex-col transition-all duration-300 z-10"
  >
    <div class="text-center mb-6">
      <p class="mt-3 font-semibold text-xl text-[#00b3ff]">Dashboard</p>
    </div>

    <nav class="space-y-1 text-sm overflow-y-auto">

      <div 
      
        @click="toggleMenu('Building')" 
        class="flex justify-between items-center cursor-pointer px-4 py-2 rounded transition duration-150"
        :class="isBuildingOpen ? 'bg-gray-100 font-semibold text-gray-800' : 'hover:bg-gray-200 text-gray-700'"
      >
      
        <span>🏢 Building Management</span>
        <svg 
          :class="{'rotate-90': isBuildingOpen}" 
          class="w-4 h-4 transform transition-transform duration-300" 
          fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </div>
       
      <div v-show="isBuildingOpen" class="pl-4 space-y-1 transition-all duration-300 overflow-hidden">
        <a href="/BuildingDashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition duration-150">🏢 Building</a>
        <a href="/equipment" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition duration-150">⚙️ Equipment</a>
      </div>
      
      <hr class="border-gray-100 my-1">
      
      <div 
        @click="toggleMenu('College')" 
        class="flex justify-between items-center cursor-pointer px-4 py-2 rounded transition duration-150"
        :class="isCollegeOpen ? 'bg-gray-100 font-semibold text-gray-800' : 'hover:bg-gray-200 text-gray-700'"
      >
        <span>🏛️ College & Dept.</span>
        <svg 
          :class="{'rotate-90': isCollegeOpen}" 
          class="w-4 h-4 transform transition-transform duration-300" 
          fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </div>

      
      <div v-show="isCollegeOpen" class="pl-4 space-y-1 transition-all duration-300 overflow-hidden">
        <a href="/CollegeDashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition duration-150">🏛️ College Dashboard</a>
        <a href="/Department" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition duration-150">👥 Department</a>
      </div>

      <hr class="border-gray-100 my-1">

      <div 
        @click="toggleMenu('Rooms')" 
        class="flex justify-between items-center cursor-pointer px-4 py-2 rounded transition duration-150"
        :class="isRoomsOpen ? 'bg-gray-100 font-semibold text-gray-800' : 'hover:bg-gray-200 text-gray-700'"
      >
        <span>🚪 Room Management</span>
        <svg 
          :class="{'rotate-90': isRoomsOpen}" 
          class="w-4 h-4 transform transition-transform duration-300" 
          fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </div>

      <div v-show="isRoomsOpen" class="pl-4 space-y-1 transition-all duration-300 overflow-hidden">
        <a href="/room" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition duration-150">🚪 Rooms</a>
        <a href="/roomtypes" class="block px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition duration-150">📂 Room Types</a>
      </div>
      
      <hr class="border-gray-200 my-2">
       <a href="/UserAccountPage" class="block px-4 py-2 hover:bg-[#9c1b33] hover:text-white rounded transition duration-150">🤵 User Account</a>
      <a href="/schedule" class="block px-4 py-2 hover:bg-[#9c1b33] hover:text-white rounded transition duration-150">📅 Schedules</a>
      <hr class="border-gray-100 my-1">
      <a href="/Terms" class="block px-4 py-2 hover:bg-[#9c1b33] hover:text-white rounded transition duration-150">📝 Terms</a>
      
    </nav>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// --- PROPS ---
/**
 * Defines a prop to receive the visibility state of the sidebar from the parent component.
 */
defineProps({
  sidebarOpen: {
    type: Boolean,
    required: true
  }
});

// --- REACTIVE STATE ---
/**
 * Reactive variables (refs) controlling the open/closed state of each accordion menu.
 * They are initialized to 'false' (closed) to ensure no menu is automatically open on page load.
 */
const isBuildingOpen = ref(false); 
const isCollegeOpen = ref(false); 
const isRoomsOpen = ref(false);   

// --- FUNCTIONS ---

/**
 * Toggles the state of the clicked menu, implementing **Accordion Logic**.
 * It ensures that only one menu is open at any time by closing the others.
 * * @param {string} menuName - The identifier for the menu to toggle ('Building', 'College', or 'Rooms').
 */
const toggleMenu = (menuName) => {
  // Map the string name to the corresponding ref variable
  const menuState = {
    Building: isBuildingOpen,
    College: isCollegeOpen,
    Rooms: isRoomsOpen,
  };

  const currentState = menuState[menuName].value;
  
  // 1. If the current menu is already open, click-to-close it.
  if (currentState) {
    menuState[menuName].value = false;
  } else {
    // 2. Close all menus (enforcing the accordion rule).
    isBuildingOpen.value = false;
    isCollegeOpen.value = false;
    isRoomsOpen.value = false;
    
    // 3. Open the newly clicked menu.
    menuState[menuName].value = true;
  }
};

/**
 * Implements **URL Persistence Logic** by checking the browser's current path
 * and automatically opening the relevant parent menu on component mount. 
 * This keeps the menu "standing by" after navigation/page reload.
 */
const setInitialMenuState = () => {
  // Defines the sub-routes belonging to each parent menu
  const pathMap = {
    Building: ['/BuildingDashboard', '/equipment'],
    College: ['/CollegeDashboard', '/Department'],
    Rooms: ['/room', '/roomtypes'],
  };

  // Get the current URL path
  const currentPath = window.location.pathname;

  // Check the current path against the defined map and set the corresponding state to true
  if (pathMap.Building.includes(currentPath)) {
    isBuildingOpen.value = true;
  } else if (pathMap.College.includes(currentPath)) {
    isCollegeOpen.value = true;
  } else if (pathMap.Rooms.includes(currentPath)) {
    isRoomsOpen.value = true;
  }
};

// --- LIFECYCLE HOOKS ---
/**
 * `onMounted` hook: Executes the persistence logic immediately after the component is added to the DOM.
 */
onMounted(() => {
  setInitialMenuState();
});
</script>