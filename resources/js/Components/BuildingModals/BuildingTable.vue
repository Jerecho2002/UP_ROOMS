<script setup>
import { computed, ref, defineProps, defineEmits } from 'vue';
// 1. Import FontAwesomeIcon component
// NOTE: This assumes you have already configured Font Awesome in your Vue project's main.js/app.js
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
// 2. Import the specific icons you need (solid style)
import { faEye,  faPenToSquare, faTrash } from '@fortawesome/free-solid-svg-icons';

// Define props to receive the list of buildings from the parent
const props = defineProps({
    buildings: {
        type: Array,
        required: true,
        default: () => []
    }
})

// Define the icons for use in the template
// Before (Error: faPenToSquaren is not defined)
const icons = {
    eye: faEye,
    edit: faPenToSquare, // <--- Fixed!
    delete: faTrash,
};
// After (Fix: Correctly referencing the imported faPenToSquare)


// Define events to notify the parent about modal actions
const emit = defineEmits(['openModal']);

// Local state for search query
const searchQuery = ref('');

// Computed property for filtering the buildings array
const filteredBuildings = computed(() => {
    if (!searchQuery.value) {
        return props.buildings;
    }
    const query = searchQuery.value.toLowerCase();
    return props.buildings.filter(building =>
        building.name.toLowerCase().includes(query) ||
        building.address.toLowerCase().includes(query) ||
        building.total_space.toLowerCase().includes(query)
    );
});

// --- Action Handlers (Emit events to parent) ---

const handleAddBuilding = () => {
    emit('openModal', 'add');
};

const handleEdit = (building) => {
    emit('openModal', 'edit', building);
};

const handleDelete = (building) => {
    emit('openModal', 'delete', building);
};

const handleView = (building) => {
    // We can use the 'edit' modal as a view-only for simplicity, or create a dedicated 'view' mode in the modal
    emit('openModal', 'view', building);
};

</script>

<template>
    <div class="p-4 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Search Input -->
                <div class="relative w-full sm:w-96">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search buildings by Name, Address, or Space..."
                        class="border border-gray-300 rounded-lg pl-10 pr-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-[#7A0C23] transition duration-150"
                    />
                    <!-- Search Icon (Using SVG/Lucide equivalent since FA is already imported) -->
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <!-- Add Button -->
                <button @click="handleAddBuilding" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-150 transform hover:scale-[1.02] active:scale-[0.98]">
                    Add Building
                </button>
            </div>
    
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200">
                
                <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                    
                    <table class="min-w-full text-sm text-gray-800 border-collapse">
                        <thead class="bg-[#7A0C23] text-white sticky top-0 shadow-lg z-10">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left font-semibold uppercase tracking-wider">Address</th>
                                <th class="px-6 py-3 text-center font-semibold uppercase tracking-wider">Total Space (sqft)</th>
                                <th class="px-6 py-3 text-center font-semibold uppercase tracking-wider">Lift</th>
                                <th class="px-6 py-3 text-center font-semibold uppercase tracking-wider">Parking</th>
                                <th class="px-6 py-3 text-center font-semibold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="b in filteredBuildings"
                                :key="b.id"
                                class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-100 border-b border-gray-200"
                            >
                                <td class="px-6 py-4 font-medium text-gray-900 truncate max-w-xs">{{ b.name }}</td>
                                <td class="px-6 py-4 text-gray-600 truncate max-w-sm">{{ b.address }}</td>
                                <td class="px-6 py-4 text-center font-mono">{{ b.total_space }}</td>
                                <td class="px-6 py-4 text-center">{{ b.lift }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="b.parking ? 'bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold' : 'bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold'">
                                        {{ b.parking ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-3">
                                        <!-- View Button -->
                                        <button @click="handleView(b)" title="View Details" class="text-sky-500 hover:text-sky-700 transform hover:scale-110 transition duration-150 p-1 rounded-full bg-sky-50 hover:bg-sky-100">
                                            <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                        </button>
                                        <!-- Edit Button -->
                                        <button @click="handleEdit(b)" title="Edit Building" class="text-green-600 hover:text-green-800 transform hover:scale-110 transition duration-150 p-1 rounded-full bg-green-50 hover:bg-green-100">
                                            <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                        </button>
                                        <!-- Delete Button -->
                                        <button @click="handleDelete(b)" title="Delete Building" class="text-red-600 hover:text-red-800 transform hover:scale-110 transition duration-150 p-1 rounded-full bg-red-50 hover:bg-red-100">
                                            <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredBuildings.length === 0">
                                <td colspan="6" class="text-center py-10 text-gray-500 font-medium bg-white">No buildings found matching your search.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
    
            </div>
        </div>
    </div>
</template>