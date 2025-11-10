<script setup>
import { computed, ref, defineProps, defineEmits } from 'vue';
// 1. Import FontAwesomeIcon component
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
// 2. Import the specific icons you need (solid style)
import { faEye, faPen, faTrash } from '@fortawesome/free-solid-svg-icons';

// Define props to receive the list of buildings from the parent
const props = defineProps({
    buildings: {
        type: Array,
        required: true,
        default: () => []
    }
})

// Define the icons for use in the template
const icons = {
    eye: faEye,
    edit: faPen, // Using faPen for a modern edit icon
    delete: faTrash, // Using faTrash for delete
};

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
                                        <!-- Fixed: Using FontAwesomeIcon with the imported icon -->
                                        <FontAwesomeIcon :icon="icons.eye" class="h-5 w-5" />
                                    </button>
                                    <button @click="handleEdit(b)" title="Edit" class="text-blue-500 hover:text-blue-700 transform hover:scale-110 transition">
                                        <!-- Fixed: Using FontAwesomeIcon with the imported icon -->
                                        <FontAwesomeIcon :icon="icons.edit" class="h-5 w-5" />
                                    </button>
                                    <button @click="handleDelete(b)" title="Delete" class="text-red-600 hover:text-red-800 transform hover:scale-110 transition">
                                        <!-- Fixed: Using FontAwesomeIcon with the imported icon -->
                                        <FontAwesomeIcon :icon="icons.delete" class="h-5 w-5" />
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