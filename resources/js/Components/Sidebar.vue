<template>
  <aside v-show="sidebarOpen" id="sidebar" class="w-64 bg-white text-black h-screen p-4 transition-all">
    <div class="text-center mb-6">
      
      <p class="mt-3 font-semibold text-xl text-[#00b3ff]">Dashboard</p>
    </div>
    <nav class="space-y-2 text-sm">
      <a href="/BuildingDashboard" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">🏢 Building</a>
      <a href="/CollegeDashboard" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">🎓 Colleges</a>
      <a href="/Department" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">🏬 Department</a>
      <a href="/equipment" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">⚙️ Equipment</a>
      <a href="/RoomTypes" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">📂 Room Types</a>
      <a href="/room" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">🚪 Rooms</a>
      <a href="/Schedule" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">📅 Schedules</a>
      <a href="/Terms" class="block px-4 py-2 hover:bg-[#9c1b33] rounded">📝 Terms</a>
    </nav>
  </aside>
</template>

<script setup>
// Define props to receive the state of the sidebar from the parent
defineProps({
  sidebarOpen: {
    type: Boolean,
    required: true
  }
});
</script>