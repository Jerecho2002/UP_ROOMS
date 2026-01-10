<script setup>
import { defineProps, defineEmits, ref } from 'vue';

const props = defineProps({
    formData: Object,
    facultySuggestions: Array,
    requesterSuggestions: Array
});

const emit = defineEmits([
    'faculty-input',
    'requester-input',
    'select-faculty',
    'select-requester'
]);

const showFacultySuggestions = ref(false);
const showRequesterSuggestions = ref(false);
</script>

<template>
  <div>
    <h4 class="text-md font-semibold mb-2 text-gray-800">Class Details</h4>

    <!-- Course Code -->
    <label for="courseCode" class="block text-sm font-medium text-gray-700">Course Code</label>
    <input type="text" id="courseCode" v-model="formData.courseCode"
      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

    <!-- Subject -->
    <label for="subject" class="block text-sm font-medium text-gray-700 mt-4">Subject*</label>
    <input type="text" id="subject" v-model="formData.subject" required
      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

    <!-- Section -->
    <label for="section" class="block text-sm font-medium text-gray-700 mt-4">Section*</label>
    <input type="text" id="section" v-model="formData.section" required
      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

    <!-- Faculty with autocomplete -->
    <label for="faculty" class="block text-sm font-medium text-gray-700 mt-4">Faculty*</label>
    <div class="relative">
      <input
        type="text"
        id="faculty"
        v-model="formData.faculty"
        required
        @input="(e) => {
          emit('faculty-input', e);
          showFacultySuggestions = true;
        }"
        @focus="showFacultySuggestions = true"
        @blur="setTimeout(() => showFacultySuggestions = false, 200)"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
      >
      <!-- Faculty Suggestions -->
      <div
        v-if="showFacultySuggestions && facultySuggestions.length > 0"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
      >
        <div
          v-for="faculty in facultySuggestions"
          :key="faculty.id"
          @mousedown="() => {
            emit('select-faculty', faculty);
            showFacultySuggestions = false;
          }"
          class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
        >
          <div class="font-medium">{{ faculty.first_name }} {{ faculty.last_name }}</div>
          <div class="text-xs text-gray-500">{{ faculty.email }}</div>
        </div>
      </div>
    </div>

    <!-- Number of Students -->
    <label for="students" class="block text-sm font-medium text-gray-700 mt-4">Number of Students*</label>
    <input type="number" id="students" v-model.number="formData.numberOfStudents" required min="1"
      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent">

    <!-- Requester with autocomplete -->
    <label for="requester" class="block text-sm font-medium text-gray-700 mt-4">Requester*</label>
    <div class="relative">
      <input
        type="text"
        id="requester"
        v-model="formData.requester"
        required
        @input="(e) => {
          emit('requester-input', e);
          showRequesterSuggestions = true;
        }"
        @focus="showRequesterSuggestions = true"
        @blur="setTimeout(() => showRequesterSuggestions = false, 200)"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-2 focus:ring-[#7A0C23] focus:border-transparent"
      >
      <!-- Requester Suggestions -->
      <div
        v-if="showRequesterSuggestions && requesterSuggestions.length > 0"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
      >
        <div
          v-for="requester in requesterSuggestions"
          :key="requester.id"
          @mousedown="() => {
            emit('select-requester', requester);
            showRequesterSuggestions = false;
          }"
          class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
        >
          <div class="font-medium">{{ requester.first_name }} {{ requester.last_name }}</div>
          <div class="text-xs text-gray-500">{{ requester.email }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
