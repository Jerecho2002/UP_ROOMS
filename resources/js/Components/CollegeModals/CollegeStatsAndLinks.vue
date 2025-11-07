<script setup>
// Dummy data for the User Details table to make the component functional
const users = [
    { user: 'User 1', email: 'user1@example.com', phone: '0912345601', profession: 'Student' },
    { user: 'User 2', email: 'user2@example.com', phone: '0912345602', profession: 'Instructor' },
    { user: 'User 3', email: 'user3@example.com', phone: '0912345603', profession: 'Admin' },
    // Add more users here if needed
];

// Utility function to get the badge color based on profession
const getBadgeColor = (profession) => {
    switch (profession) {
        case 'Student':
            return 'bg-green-100 text-green-800';
        case 'Instructor':
            return 'bg-indigo-100 text-indigo-800';
        case 'Admin':
            return 'bg-pink-100 text-pink-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <div class="space-y-6">

        <h1 class="text-3xl font-bold text-[#7A0C23]">Department Dashboard</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="rounded-xl text-center shadow-lg overflow-hidden transition-transform duration-200 hover:scale-[1.02]">
                <div class="bg-teal-500 text-white p-3">
                    <h3 class="text-lg font-semibold">Total Accounts</h3>
                </div>
                <div class="bg-white p-3">
                    <p class="text-3xl font-bold text-gray-800">20</p>
                </div>
            </div>

            <div class="rounded-xl text-center shadow-lg overflow-hidden transition-transform duration-200 hover:scale-[1.02]">
                <div class="bg-purple-700 text-white p-3">
                    <h3 class="text-lg font-semibold">Total Depart.</h3>
                </div>
                <div class="bg-white p-3">
                    <p class="text-3xl font-bold text-gray-800">5</p>
                </div>
            </div>

            <div class="rounded-xl text-center shadow-lg overflow-hidden transition-transform duration-200 hover:scale-[1.02]">
                <div class="bg-orange-600 text-white p-3">
                    <h3 class="text-lg font-semibold">Total Colleges</h3>
                </div>
                <div class="bg-white p-3">
                    <p class="text-3xl font-bold text-gray-800">3</p>
                </div>
            </div>

            <div class="rounded-xl text-center shadow-lg overflow-hidden transition-transform duration-200 hover:scale-[1.02]">
                <div class="bg-red-600 text-white p-3">
                    <h3 class="text-lg font-semibold">Total Rooms</h3>
                </div>
                <div class="bg-white p-3">
                    <p class="text-3xl font-bold text-gray-800">10</p>
                </div>
            </div>

        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">User Details</h2>

            <div class="mb-4">
                <input type="text" placeholder="Search by User or Email..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-[#7A0C23] focus:border-[#7A0C23]">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#7A0C23]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email-ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Profession</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="user in users" :key="user.user">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.user }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getBadgeColor(user.profession)]">
                                    {{ user.profession }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-lg">
                                <button class="text-blue-500 hover:text-blue-700 mx-1">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button class="text-yellow-500 hover:text-yellow-700 mx-1">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.284-8.284z"></path></svg>
                                </button>
                                <button class="text-red-500 hover:text-red-700 mx-1">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="font-semibold text-xl text-gray-800 mb-4 border-b pb-2">Quick Links</h2>
            <div class="grid grid-cols-2 gap-4">
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">News Board</button>
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">Event</button>
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">Class Schedule</button>
                <button class="bg-[#7A0C23] hover:bg-[#5C0A1C] text-white py-3 rounded-lg shadow transition duration-150 font-medium">Mail / SMS</button>
            </div>
        </div>

    </div>
</template>