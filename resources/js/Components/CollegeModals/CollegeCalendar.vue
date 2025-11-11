<script setup>
import { onMounted } from 'vue'
import $ from 'jquery'   // Import jQuery for calendar DOM manipulation

// ----------------------------------------------
// jQuery calendar initialization and rendering
// ----------------------------------------------
onMounted(() => {
    // Month names for display
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ]

    // Get today's date info for reference
    const today = new Date()
    let currentMonth = today.getMonth() // Start with the current month
    let currentYear = today.getFullYear() // Start with the current year
    
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
                    // Check if the cell is the actual 'today' date
                    const isToday = new Date().getMonth() === month && new Date().getFullYear() === year && new Date().getDate() === date;
                    
                    // Apply current day style: 'bg-[#7A0C23] text-white'
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

    // Initial calendar render on mount (uses current day)
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
    })
})
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-3">
            
            <h2 id="monthAndYear" class="font-semibold text-gray-800 text-base"></h2>
            
            <div class="flex items-center space-x-1">
                <button id="todayBtn" class="px-3 py-1 bg-[#7A0C23] text-white rounded text-xs">Today</button>

                <div class="flex bg-gray-200 rounded text-gray-800">
                    <button id="prevMonth" class="px-2 py-1 hover:bg-gray-300 rounded-l">&lt;</button>
                    <button id="nextMonth" class="px-2 py-1 hover:bg-gray-300 rounded-r">&gt;</button>
                </div>
            </div>
        </div>

        <table class="w-full text-center text-sm border-collapse">
            <thead class="text-gray-600">
                <tr>
                    <th class="p-1">sun</th>
                    <th class="p-1">mon</th>
                    <th class="p-1">tue</th>
                    <th class="p-1">wed</th>
                    <th class="p-1">thu</th>
                    <th class="p-1">fri</th>
                    <th class="p-1">sat</th>
                </tr>
            </thead>

            <tbody id="calendar-body" class="text-gray-800"></tbody>
        </table>
    </div>
</template>