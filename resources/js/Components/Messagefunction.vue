<template>
    <div class="fixed top-6 right-6 space-y-2 z-[9999]">

        <transition name="toast">
            <div
                v-if="showCreateSuccess"
                class="bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold flex items-center"
            >
                <span class="mr-2"></span> Successfully created a room!
            </div>
        </transition>

        <transition name="toast">
            <div
                v-if="showEditSuccess"
                class="bg-yellow-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold flex items-center"
            >
                <span class="mr-2"></span> Successfully edited the room data!
            </div>
        </transition>

        <transition name="toast">
            <div
                v-if="showDeleteSuccess"
                class="bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold flex items-center"
            >
                <span class="mr-2"></span> Room {{ deletedRoomName }} successfully deleted!
            </div>
        </transition>

    </div>
</template>

<script setup>
import { defineProps } from 'vue';

// Define props to receive the state variables from the parent component
const props = defineProps({
    showCreateSuccess: {
        type: Boolean,
        default: false,
    },
    showEditSuccess: {
        type: Boolean,
        default: false,
    },
    showDeleteSuccess: {
        type: Boolean,
        default: false,
    },
    deletedRoomName: {
        type: String,
        default: '',
    }
});
</script>

<style scoped>
/* Toast Transition Styles */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.35s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-15px);
}
</style>