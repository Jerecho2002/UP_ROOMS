<script setup>
import { onMounted } from 'vue'
import $ from 'jquery'   // Import jQuery for calendar DOM manipulation

// ----------------------------------------------
// jQuery calendar initialization and rendering
// ----------------------------------------------
onMounted(() => {
  // Month names for display
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ]

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
          // Check if today and apply initial highlight only on first render
          const isToday = new Date().getMonth() === month && new Date().getFullYear() === year && new Date().getDate() === date;
          row += `<td class="p-2 hover:bg-[#7A0C23] hover:text-white cursor-pointer rounded ${isToday ? 'bg-[#7A0C23] text-white' : ''}" data-date="${date}">${date}</td>`
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
    // Highlight today's date cell
    $(`#calendar-body td[data-date=${today.getDate()}]`).addClass('bg-[#7A0C23] text-white')
  })
})
</script>

<template>
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
</template>
