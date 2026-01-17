<script setup>
import { computed } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false,
    },
    selectedUsage: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

const equipmentList = computed(() => props.selectedUsage?.equipmentUsed || []);

// Function to view item details
const viewItemDetails = (item) => {
    // You can implement item details view logic here
    console.log('View item details:', item);
};
</script>

<template>
    <transition name="fade">
        <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-5xl p-6 relative max-h-[90vh] overflow-hidden flex flex-col">

                <!-- Header -->
                <div class="border-b pb-4 mb-4">
                    <h3 class="text-xl font-bold text-[#800020] mb-2">
                        Equipment Details for: {{ selectedUsage?.name }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-gray-600">
                        <div>
                            <span class="font-semibold">Room:</span> {{ selectedUsage?.room }}
                            <span v-if="selectedUsage?.room_name">({{ selectedUsage?.room_name }})</span>
                        </div>
                        <div><span class="font-semibold">Building:</span> {{ selectedUsage?.building }}</div>
                        <div><span class="font-semibold">College:</span> {{ selectedUsage?.college }}</div>
                        <div v-if="selectedUsage?.department">
                            <span class="font-semibold">Department:</span> {{ selectedUsage?.department }}
                        </div>
                        <div class="md:col-span-2">
                            <span class="font-semibold">Total Equipment:</span> {{ equipmentList.length }} items
                        </div>
                    </div>
                </div>

                <!-- Equipment Table -->
                <div class="flex-1 overflow-y-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Inventory ID</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Property ID</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Equipment Name</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Brand/Model</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Status</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in equipmentList" :key="item.id" class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-gray-700">{{ item.inventory_id }}</td>
                                <td class="px-4 py-3 font-mono text-gray-700">{{ item.property_id }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ item.name }}</div>
                                    <div v-if="item.description" class="text-xs text-gray-500 mt-1">{{ item.description }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">{{ item.brand || 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ item.model || 'N/A' }}</div>
                                    <div v-if="item.serial_number" class="text-xs text-gray-400 font-mono">
                                        S/N: {{ item.serial_number }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="{
                                        'bg-green-100 text-green-700': item.status === 'Available',
                                        'bg-blue-100 text-blue-700': item.status === 'In use',
                                        'bg-yellow-100 text-yellow-700': item.status === 'Maintenance',
                                        'bg-red-100 text-red-700': item.status === 'Retired',
                                        'bg-gray-100 text-gray-700': !item.status
                                    }"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ item.status || 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        @click="viewItemDetails(item)"
                                        class="text-xs text-[#7A0C23] hover:text-[#5a071a] font-medium hover:underline"
                                    >
                                        View Full Details
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="equipmentList.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    No equipment items found for this user.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="mt-6 pt-4 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        Showing {{ equipmentList.length }} equipment item(s)
                    </div>
                    <button
                        @click="close"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-150"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
