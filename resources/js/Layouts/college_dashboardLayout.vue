<script setup>
import { ref, onMounted } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebarcollege from '@/Components/Sidebarcollege.vue'
import { Link as InertiaLink } from '@inertiajs/vue3'
import $ from 'jquery'   // ✅ Import jQuery for calendar DOM manipulation

// ----------------------------------------------
// Sidebar toggle state and method
// ----------------------------------------------
const sidebarVisible = ref(true)  // Controls sidebar visibility

// Toggle sidebar visibility on toggle event
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}

// ----------------------------------------------
// jQuery calendar initialization and rendering
// ----------------------------------------------
onMounted(() => {
  // Generate calendar grid for given month and year
  function generateCalendar(month, year) {
    // Number of days in the month
    const daysInMonth = new Date(year, month + 1, 0).getDate()
    // Day of the week the month starts on (0 = Sunday)
    const firstDay = new Date(year, month).getDay()
    
    let tableBody = ''
    let date = 1

    // Build 6 rows (weeks)
    for (let i = 0; i < 6; i++) {
      let row = '<tr>'
      // Build 7 columns (days of week)
      for (let j = 0; j < 7; j++) {
        if (i === 0 && j < firstDay) {
          // Empty cells before first day of month
          row += '<td class="p-2"></td>'
        } else if (date > daysInMonth) {
          // Empty cells after last day of month
          row += '<td class="p-2"></td>'
        } else {
          // Valid date cells with hover styles and data attribute for date
          row += `<td class="p-2 hover:bg-[#7A0C23] hover:text-white cursor-pointer rounded" data-date="${date}">${date}</td>`
          date++
        }
      }
      row += '</tr>'
      tableBody += row
    }

    // Inject generated calendar rows into table body
    $('#calendar-body').html(tableBody)

    // Update header text showing current month and year
    $('#monthAndYear').text(`${monthNames[month]} ${year}`)
  }

  // Get today's date info
  const today = new Date()
  let currentMonth = today.getMonth()
  let currentYear = today.getFullYear()

  // Month names for display
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ]

  // Initial calendar render on mount
  generateCalendar(currentMonth, currentYear)

  // Prev button click handler - navigate to previous month
  $('#prevMonth').on('click', function () {
    currentYear = currentMonth === 0 ? currentYear - 1 : currentYear
    currentMonth = currentMonth === 0 ? 11 : currentMonth - 1
    generateCalendar(currentMonth, currentYear)
  })

  // Next button click handler - navigate to next month
  $('#nextMonth').on('click', function () {
    currentYear = currentMonth === 11 ? currentYear + 1 : currentYear
    currentMonth = (currentMonth + 1) % 12
    generateCalendar(currentMonth, currentYear)
  })

  // Today button click handler - jump to current month and highlight today
  $('#todayBtn').on('click', function () {
    currentMonth = today.getMonth()
    currentYear = today.getFullYear()
    generateCalendar(currentMonth, currentYear)
    // Highlight today's date cell with background and text color
    $(`#calendar-body td[data-date=${today.getDate()}]`).addClass('bg-[#7A0C23] text-white')
  })
})
</script>

<template>
  <div class="bg-gray-100 font-sans">
    <!-- NAVBAR -->
    <!-- Navbar contains toggle button which triggers sidebar visibility toggle -->
    <Navbar @toggleSidebar="toggleSidebar" />

    <!-- MAIN LAYOUT -->
    <div class="flex pt-14 min-h-screen transition-all duration-300">
      
      <!-- SIDEBAR -->
      <!-- Sidebar visibility controlled reactively -->
      <Sidebarcollege v-show="sidebarVisible" @toggleSidebar="toggleSidebar" />

      <!-- MAIN CONTENT AREA -->
      <main id="mainContent" class="flex-1 p-6 bg-gray-50 transition-all duration-300">

        <!-- PAGE HEADER -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <!-- STAT CARDS -->
        <!-- Displays summary cards for Class, Students, Teacher, New Messages -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-gray-500">Class</p>
            <h2 class="text-2xl font-bold">0</h2>
          </div>
          <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-gray-500">Students</p>
            <h2 class="text-2xl font-bold">0</h2>
          </div>
          <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-gray-500">Teacher</p>
            <h2 class="text-2xl font-bold">0</h2>
          </div>
          <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-gray-500">New Messages</p>
            <h2 class="text-2xl font-bold">0</h2>
          </div>
        </div>

        <!-- MIDDLE CONTENT GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

          <!-- LEFT SIDE - Leaderboard, News & Events, Quick Links -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Leader Board Section -->
            <div class="bg-white shadow rounded-lg p-4">
              <h2 class="font-semibold text-gray-800">Leader Board</h2>
              <div class="h-24 flex items-center justify-center text-gray-400">No Data</div>
            </div>

            <!-- News & Events Section -->
            <div class="bg-white shadow rounded-lg p-4">
              <h2 class="font-semibold text-gray-800">News & Events</h2>
              <div class="h-24 flex items-center justify-center text-gray-400">No Data</div>
            </div>

            <!-- Quick Links Buttons -->
            <div class="bg-white shadow rounded-lg p-4">
              <h2 class="font-semibold text-gray-800 mb-3">Quick Links</h2>
              <div class="grid grid-cols-2 gap-4">
                <!-- Buttons for navigation or actions -->
                <button class="bg-[#7A0C23] text-white py-2 rounded">News Board</button>
                <button class="bg-[#7A0C23] text-white py-2 rounded">Event</button>
                <button class="bg-[#7A0C23] text-white py-2 rounded">Class Schedule</button>
                <button class="bg-[#7A0C23] text-white py-2 rounded">Mail / SMS</button>
              </div>
            </div>
          </div>

          <!-- RIGHT SIDE - Calendar -->
          <div class="bg-white shadow rounded-lg p-4">

            <!-- Calendar Header with Prev/Next Buttons -->
            <div class="flex justify-between items-center mb-3">
              <!-- Prev month button triggers calendar to go to previous month -->
              <button id="prevMonth" class="px-2 py-1 bg-gray-200 rounded">&lt;</button>
              
              <!-- Display current month and year -->
              <h2 id="monthAndYear" class="font-semibold text-gray-800">October 2016</h2>
              
              <!-- Next month button triggers calendar to go to next month -->
              <button id="nextMonth" class="px-2 py-1 bg-gray-200 rounded">&gt;</button>
            </div>

            <!-- Today button resets calendar to current month -->
            <button id="todayBtn" class="mb-2 px-4 py-1 bg-[#7A0C23] text-white rounded">Today</button>

            <!-- Calendar Table -->
            <table class="w-full text-center text-sm border-collapse">
              <thead class="text-gray-600">
                <tr>
                  <!-- Days of the week header -->
                  <th class="p-2">sun</th>
                  <th class="p-2">mon</th>
                  <th class="p-2">tue</th>
                  <th class="p-2">wed</th>
                  <th class="p-2">thu</th>
                  <th class="p-2">fri</th>
                  <th class="p-2">sat</th>
                </tr>
              </thead>

              <!-- Calendar body populated dynamically via jQuery -->
              <tbody id="calendar-body" class="text-gray-800"></tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>
