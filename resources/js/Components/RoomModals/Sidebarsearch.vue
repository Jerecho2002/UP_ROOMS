<template>
  <div class="h-full p-4 border-l border-gray-200 flex flex-col">
    
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
      <h3 class="text-xl font-semibold text-gray-800">Search Room</h3>
      <button @click="$emit('close-search')" class="text-gray-500 hover:text-gray-700">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>

    <div class="relative mb-6">
      <input
        type="text"
        placeholder=" "
        class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:border-green-500 transition duration-150"
      >
      <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>

    <div class="mb-6">
      <p class="text-sm font-medium text-gray-500 mb-1">Room name</p>
      <p class="text-lg font-bold text-gray-900">UG 114</p>
    </div>

    <div class="mb-6 space-y-1">
      <h4 class="text-sm font-medium text-gray-500 mb-2">Room Details:</h4>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Description:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">College:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Building:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Department:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Capacity:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Location:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Type:</span> <span class="text-gray-900">...</span></p>
      <p class="text-sm text-gray-900"><span class="font-semibold text-gray-600 w-20 inline-block">Floor Number:</span> <span class="text-gray-900">...</span></p>
    </div>

    <div class="flex-1 overflow-y-auto">
      <h4 class="text-sm font-medium text-gray-500 mb-2">Schedules</h4>
      <div class="space-y-2 text-sm">
        <div class="p-2 border rounded-lg bg-gray-50">
          <p class="font-semibold">English 3 A</p>
          <p class="text-xs text-gray-600">9:00AM - 12:00PM T CCAD</p>
        </div>
        <div class="p-2 border rounded-lg bg-gray-50">
          <p class="font-semibold">FA 12 B</p>
          <p class="text-xs text-gray-600">1:30PM - 4:30PM MW CCAD</p>
        </div>
        </div>
    </div>

  </div>
</template>

<script setup>
// Emit an event when the close button is clicked
defineEmits(['close-search']); 
</script>