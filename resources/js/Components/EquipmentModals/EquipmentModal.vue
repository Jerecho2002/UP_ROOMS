<script setup>
import { computed } from 'vue';

const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false,
    },
    // Data for the selected row from EquipmentTable
    selectedUsage: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

// Access the nested equipment details
const equipmentList = computed(() => props.selectedUsage?.equipmentUsed || []);
</script>

<template>
    <transition name="fade">
        <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-70 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-3xl p-6 relative">

                <h3 class="text-xl font-bold text-[#800020] mb-2 pb-2 border-b">
                    Equipment Used by: {{ selectedUsage?.name }}
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Room: {{ selectedUsage?.room }} | Building: {{ selectedUsage?.building }} | College: {{ selectedUsage?.college }}
                </p>

                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">Inventory ID</th>
                                <th class="px-4 py-2 text-left text-gray-600">Property ID</th>
                                <th class="px-4 py-2 text-left text-gray-600">Name</th>
                                <th class="px-4 py-2 text-left text-gray-600">CFIC</th>
                                <th class="px-4 py-2 text-center text-gray-600">Item Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in equipmentList" :key="item.property_id" class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-gray-700">{{ item.inventory_id }}</td>
                                <td class="px-4 py-3 font-mono text-gray-700">{{ item.property_id }}</td>
                                <td class="px-4 py-3 font-medium text-left">{{ item.name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ item.cfic }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="{'bg-green-100 text-green-700': item.status === 'Returned', 'bg-red-100 text-red-700': item.status === 'Out' || item.status === 'Cancelled'}"
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
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
/* Modal Transition */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
