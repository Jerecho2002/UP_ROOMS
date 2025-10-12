<script setup>
import { ref, computed } from 'vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'

// =====================
// STATE AND DATA
// =====================
const sidebarOpen = ref(true)
const currentDate = ref(new Date(2025, 9, 1)) // October 1, 2025
const selectedDay = ref(null) // Store the clicked day

const appointments = ref([
  {
    id: 1,
    user: 'Admin',
    date: 'October 12, 2025',
    time: '9:00 AM',
    description: 'Morning meeting with clients.'
  },
  {
    id: 2,
    user: 'Admin',
    date: 'October 12, 2025',
    time: '1:00 PM',
    description: 'Project deadline follow-up and internal sync-up.'
  },
  {
    id: 3,
    user: 'Admin',
    date: 'October 02, 2025',
    time: '11:00 AM',
    description: 'Weekly planning session.'
  },
  {
    id: 4,
    user: 'Admin',
    date: 'October 10, 2025',
    time: '3:00 PM',
    description: 'Client check-in and progress report.'
  }
])

// =====================
// COMPUTED
// =====================
const currentMonthYear = computed(() =>
  currentDate.value.toLocaleString('en-US', { month: 'long', year: 'numeric' })
)

const days = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDayOfWeek = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1
  const calendarDays = []

  for (let i = 0; i < startDayOfWeek; i++) {
    calendarDays.push({ day: '', isCurrentMonth: false })
  }

  for (let i = 1; i <= lastDay.getDate(); i++) {
    const hasAppointment = appointments.value.some(app => {
      const appDate = new Date(app.date)
      return (
        appDate.getDate() === i &&
        appDate.getMonth() === month &&
        appDate.getFullYear() === year
      )
    })

    calendarDays.push({
      day: i,
      isCurrentMonth: true,
      hasAppointment
    })
  }

  return calendarDays
})

// Selected Date Title
const selectedDateTitle = computed(() => {
  if (!selectedDay.value) return 'Select a day'
  const date = new Date(currentDate.value)
  date.setDate(selectedDay.value)
  return date.toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' })
})

// Filtered appointments for selected day
const filteredAppointments = computed(() => {
  if (!selectedDay.value) return []
  const selectedDate = new Date(currentDate.value)
  selectedDate.setDate(selectedDay.value)
  return appointments.value.filter(app => {
    const appDate = new Date(app.date)
    return (
      appDate.getDate() === selectedDate.getDate() &&
      appDate.getMonth() === selectedDate.getMonth() &&
      appDate.getFullYear() === selectedDate.getFullYear()
    )
  })
})

// =====================
// METHODS
// =====================
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)

const previousMonth = () => {
  currentDate.value.setMonth(currentDate.value.getMonth() - 1)
  currentDate.value = new Date(currentDate.value)
  selectedDay.value = null
}

const nextMonth = () => {
  currentDate.value.setMonth(currentDate.value.getMonth() + 1)
  currentDate.value = new Date(currentDate.value)
  selectedDay.value = null
}

const selectDay = day => {
  if (day) selectedDay.value = day
}

const addNewAppointment = () => alert('Opening Add New Appointment form!')
const deleteAppointment = () => alert('Deleting selected appointment!')
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <Navbar @toggleSidebar="toggleSidebar" />

    <div class="flex flex-1 pt-14 transition-all duration-300">
      <Sidebar :sidebarOpen="sidebarOpen" />

      <div class="flex flex-1 flex-col p-4 max-w-screen-2xl mx-auto w-full transition-all duration-300 gap-10">
        <main class="flex flex-1 flex-col lg:flex-row gap-10">
          <!-- Calendar -->
          <div class="bg-white p-6 rounded-lg shadow-md flex-1 max-w-[70%] transition-all duration-300">
            <div class="flex justify-between items-center pb-4 border-b">
              <h2 class="text-xl font-bold text-gray-800">Calendar for Schedules</h2>
              <p class="text-sm text-gray-600">UPCEBU &gt; Schedules</p>
            </div>

            <div class="flex justify-between items-center my-6">
              <button @click="previousMonth" class="text-3xl text-gray-600 hover:text-red-800">&lt;</button>
              <h3 class="text-2xl font-semibold text-gray-800">{{ currentMonthYear }}</h3>
              <button @click="nextMonth" class="text-3xl text-gray-600 hover:text-red-800">&gt;</button>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center">
              <div class="py-2 font-bold text-gray-500">Mon</div>
              <div class="py-2 font-bold text-gray-500">Tue</div>
              <div class="py-2 font-bold text-gray-500">Wed</div>
              <div class="py-2 font-bold text-gray-500">Thu</div>
              <div class="py-2 font-bold text-gray-500">Fri</div>
              <div class="py-2 font-bold text-gray-500">Sat</div>
              <div class="py-2 font-bold text-gray-500">Sun</div>

              <div
                v-for="(day, index) in days"
                :key="index"
                @click="selectDay(day.day)"
                :class="[
                  'h-16 flex items-center justify-center text-xl font-medium cursor-pointer rounded-full transition duration-150',
                  day.isCurrentMonth ? 'text-gray-800 hover:bg-gray-100' : 'text-gray-300 pointer-events-none',
                  { 'bg-red-800 text-white hover:bg-red-700': day.hasAppointment && selectedDay === day.day },
                  { 'border border-red-800 text-red-800 hover:bg-red-50': day.hasAppointment && selectedDay !== day.day }
                ]"
              >
                {{ day.day }}
              </div>
            </div>
          </div>

          <!-- Appointment Panel -->
          <div class="lg:w-80 space-y-4 flex-shrink-0">
            <!-- Search (not functional yet) -->
            <div class="bg-teal-100/70 p-3 rounded-full flex items-center space-x-2 shadow-inner">
              <svg class="w-5 h-5 text-gray-600 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input type="text" placeholder="SEARCH"
                     class="flex-1 bg-transparent text-gray-800 focus:outline-none placeholder-gray-700 font-semibold border-none" />
            </div>

            <!-- Appointments List Panel -->
            <div class="bg-white p-4 rounded-lg shadow-md space-y-4 max-h-[400px] overflow-y-auto">
              <h4 class="text-lg font-semibold border-b pb-2">
                Appointment: {{ selectedDateTitle }}
              </h4>

              <div class="flex justify-between space-x-2">
                <button @click="addNewAppointment"
                        class="flex-1 bg-blue-500 text-white py-2 rounded-lg font-semibold text-sm hover:bg-blue-600 transition">
                  ADD NEW
                </button>
                <button @click="deleteAppointment"
                        class="flex-1 bg-red-500 text-white py-2 rounded-lg font-semibold text-sm hover:bg-red-600 transition">
                  DELETE
                </button>
              </div>

              <!-- Appointments -->
              <div v-if="filteredAppointments.length > 0">
                <div v-for="app in filteredAppointments" :key="app.id"
                     class="p-3 bg-gray-50 rounded-lg shadow-sm">
                  <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                              clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="text-sm">
                      <p class="font-semibold text-gray-800">User: {{ app.user }}</p>
                      <p class="text-xs text-gray-600 mb-1">Time: {{ app.time }} — {{ app.date }}</p>
                      <p class="text-gray-700">{{ app.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-gray-500 text-sm text-center py-10">
                No appointments for this day.
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
