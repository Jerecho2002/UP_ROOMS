<script setup>
import { computed, ref, defineProps, defineEmits } from 'vue';

// Define props to receive the list of buildings from the parent
const props = defineProps({
    buildings: {
        type: Array,
        required: true,
        default: () => []
    }
})

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
    <div class="p-4">
        <div class="mb-6 flex justify-between items-center">
            <!-- Search Input -->
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search buildings by Name, Address, or Space..."
                class="border border-gray-300 rounded-lg px-4 py-2 w-96 focus:outline-none focus:ring-2 focus:ring-[#7A0C23]"
            />
            <!-- Add Button -->
            <button @click="handleAddBuilding" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition duration-150 transform hover:scale-[1.02]">
                Add Building
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            
            <div class="overflow-x-auto max-h-[80vh] overflow-y-auto">
                
                <table class="min-w-full text-sm text-gray-800 border-collapse">
                    <thead class="bg-[#7A0C23] text-white sticky top-0 shadow-lg">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Name</th>
                            <th class="px-6 py-3 text-left font-semibold">Address</th>
                            <th class="px-6 py-3 text-center font-semibold">Total Space</th>
                            <th class="px-6 py-3 text-center font-semibold">Lift</th>
                            <th class="px-6 py-3 text-center font-semibold">Parking</th>
                            <th class="px-6 py-3 text-center font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="b in filteredBuildings"
                            :key="b.id"
                            class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition duration-100 border-b border-gray-200"
                        >
                            <td class="px-6 py-3 font-medium">{{ b.name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ b.address }}</td>
                            <td class="px-6 py-3 text-center">{{ b.total_space }}</td>
                            <td class="px-6 py-3 text-center">{{ b.lift }}</td>
                            <td class="px-6 py-3 text-center">
                                <span :class="b.parking ? 'text-green-600 font-semibold' : 'text-red-500'">
                                    {{ b.parking ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button @click="handleView(b)" title="View" class="text-sky-500 hover:text-sky-700 transform hover:scale-110 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </button>
                                    <button @click="handleEdit(b)" title="Edit" class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-7.586 11l-2 2h4l9.293-9.293-2.828-2.828L5 14.004v-2.004H3v4h4l-2 2H3v-2z" />
                                        </svg>
                                    </button>
                                    <button @click="handleDelete(b)" title="Delete" class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 11a1 1 0 10-2 0v5a1 1 0 102 0v-5zm6-1a1 1 0 00-1 1v5a1 1 0 102 0v-5a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredBuildings.length === 0">
                            <td colspan="6" class="text-center py-6 text-gray-500">No buildings found matching your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</template>
