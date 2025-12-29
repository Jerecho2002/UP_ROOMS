<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <img src="/image/uplogo.png" alt="UP Cebu Logo" class="h-20 w-20">
        </div>
        <h1 class="text-3xl font-bold text-[#7A0C23]">UP CEBU</h1>
        <p class="text-gray-600 mt-2">University Management System</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <div v-if="$page.props.flash.error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
          {{ $page.props.flash.error }}
        </div>

        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
            Username
          </label>
          <input
            id="username"
            v-model="form.username"
            type="text"
            required
            :class="[
              'w-full px-4 py-3 border rounded-lg focus:ring-2 focus:border-transparent transition duration-200',
              errors.username ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#7A0C23]'
            ]"
            placeholder="Enter your username"
          >
          <p v-if="errors.username" class="mt-1 text-sm text-red-600">{{ errors.username }}</p>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            :class="[
              'w-full px-4 py-3 border rounded-lg focus:ring-2 focus:border-transparent transition duration-200',
              errors.password ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-[#7A0C23]'
            ]"
            placeholder="Enter your password"
          >
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          :disabled="processing"
          class="w-full bg-[#7A0C23] text-white py-3 px-4 rounded-lg font-semibold hover:bg-[#5a061a] transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="processing" class="flex items-center justify-center">
            <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Logging in...
          </span>
          <span v-else>
            Sign In
          </span>
        </button>
      </form>

      <!-- Demo Credentials -->
      <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Demo Credentials:</h3>
        <div class="space-y-1 text-sm text-gray-600">
          <div><span class="font-medium">Admin:</span> admin / password</div>
          <div><span class="font-medium">Staff:</span> staff / password</div>
          <div><span class="font-medium">Faculty:</span> faculty / password</div>
          <div><span class="font-medium">Sysadmin:</span> sysadmin / password</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';

const processing = ref(false);

const form = useForm({
  username: '',
  password: '',
});

const errors = reactive({
  username: '',
  password: '',
});

const submit = () => {
  processing.value = true;

  form.clearErrors();

  form.post('/login', {
    onFinish: () => {
      processing.value = false;
    },
    onError: (errors) => {
      if (errors.username) {
        errors.username = errors.username;
      }
      if (errors.password) {
        errors.password = errors.password;
      }
    },
  });
};
</script>
