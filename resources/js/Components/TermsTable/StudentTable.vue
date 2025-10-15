<script setup>
// Student data based on the provided image
const students = [
  { name: 'Leandro Pampampiti', course: 'Biology', yearStarted: 2019, graduateYear: 2023, status: 'Graduated', statusClass: 'bg-green-100 text-green-800' },
  { name: 'Leandro Pampampiti', course: 'Biology', yearStarted: 2019, graduateYear: 2023, status: 'Enrolled', statusClass: 'bg-blue-100 text-blue-800' },
  { name: 'Leandro Pampampiti', course: 'Biology', yearStarted: 2019, graduateYear: 2023, status: 'Undergraduated', statusClass: 'bg-red-100 text-red-800' },
];
</script>

<template>
  <div class="overflow-x-auto border border-gray-200 rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-[#850038]">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Name
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Course
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Year Started
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Graduate
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Status
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="student in students" :key="student.name + student.status">
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ student.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.course }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.yearStarted }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.graduateYear }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="student.statusClass">
              {{ student.status }}
            </span>
          </td>
        </tr>
        <tr v-for="n in 5" :key="'empty-student-' + n"><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
      </tbody>
    </table>
  </div>
</template>