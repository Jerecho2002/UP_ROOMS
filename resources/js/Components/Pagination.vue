<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true
    }
})
</script>

<template>
    <div class="bg-gray-50 px-6 py-4 border-t">
        <div class="flex justify-end">
            <div class="flex gap-1">
                <Link v-for="(link, index) in links" :key="index" :href="link.url || ''" v-html="link.label" :class="[
                    'px-3 py-1 border rounded text-sm',
                    {
                        'bg-[#7A0C23] text-white': link.active,
                        'opacity-50 pointer-events-none': !link.url,
                        'bg-white': !link.active
                    }
                ]" />
            </div>
        </div>
    </div>
</template>