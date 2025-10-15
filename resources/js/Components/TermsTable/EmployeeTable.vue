<script setup>
// Employee data based on your initial HTML
const employees = [
  { name: 'Panfilo Lacson', position: 'Senator', startDate: '2024-15-09', endDate: '2026-15-09', status: 'Active', statusClass: 'bg-green-100 text-green-800' },
  { name: 'Panfilo Lacson', position: 'Senator', startDate: '2024-15-09', endDate: '2026-15-09', status: 'Expired', statusClass: 'bg-red-100 text-red-800' },
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
            Position
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Start Date
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            End Date
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
            Status
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="employee in employees" :key="employee.name + employee.status">
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ employee.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.position }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.startDate }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ employee.endDate }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="employee.statusClass">
              {{ employee.status }}
            </span>
          </td>
        </tr>
        <tr v-for="n in 5" :key="'empty-employee-' + n"><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
      </tbody>
    </table>
  </div>
</template>