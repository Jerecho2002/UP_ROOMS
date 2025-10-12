     <template>
           <aside v-show="sidebarVisible" id="sidebar" class="w-64 bg-white border-r shadow transition-all duration-300">
        <div class="px-6 py-6 text-center">
          <div class="h-20 w-20 rounded-full overflow-hidden border-4 border-white shadow mx-auto">
            <img :src="`/image/jordan.jpg`" alt="profile">
          </div>
          <div class="mt-3 font-semibold text-sm text-gray-800">RUSSELL EVAN</div>
        </div>
        <nav class="px-3 space-y-1 text-sm font-medium text-black-700">
          <a href="/UserAccountPage" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">👤 User Account</a>
          <a href="/building_dashboard" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">🏢 Building</a>
          <a href="/college_dashboard" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">🎓 Colleges</a>
          <a href="/Department" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">🏬 Department</a>
          <a href="/equipment" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">⚙️ Equipment</a>
          <a href="/roomtypes" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">📂 Room Types</a>
          <a href="/room" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">🚪 Rooms</a>
          <a href="/schedule" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">📅 Schedule</a>
          <a href="#" class="flex items-center px-4 py-2 rounded  hover:bg-[#9c1b33]">📝 Terms</a>
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