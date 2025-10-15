<script setup>
// Assuming no props are needed here, as it's a content component.
</script>

<template>
  <div class="p-6">
    <div class="mb-6">
      <h2 class="text-xl font-semibold text-gray-800">Terms</h2>
      </div>

    <div class="flex items-center bg-gray-100 rounded-full w-full md:w-fit p-1 mb-6">
      <button 
        class="flex-1 bg-[#850038] text-white font-medium py-2 px-6 rounded-full transition text-sm md:text-base"
      >
        Employee
      </button>
      <button 
        class="flex-1 text-black font-medium py-2 px-6 rounded-full transition hover:bg-gray-200 text-sm md:text-base"
      >
        Students
      </button>
    </div>

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
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Panfilo Lacson</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Senator</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-15-09</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2026-15-09</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                Active
              </span>
            </td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Panfilo Lacson</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Senator</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-15-09</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2026-15-09</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                Expired
              </span>
            </td>
          </tr>
          <tr><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
          <tr><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
          <tr><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
          <tr><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
          <tr><td class="px-6 py-4 text-sm text-gray-500 h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td><td class="h-10"></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</template>