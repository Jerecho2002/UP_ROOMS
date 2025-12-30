<template>
    <div>
        <UserAccountLayout :initialUsers="initialUsers" />
    </div>
</template>

<script setup>
import UserAccountLayout from '@/Layouts/UserAccountLayout.vue';

defineProps({
    initialUsers: {
        type: Array,
        default: () => []
    }
});
</script>
