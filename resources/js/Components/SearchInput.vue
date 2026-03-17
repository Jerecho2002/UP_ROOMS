<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'

const props = defineProps({
    modelValue: String,
    routeName: String
})

const search = ref(props.modelValue || '')

const handleSearch = debounce((value) => {
    const url = props.routeName.startsWith('/')
        ? props.routeName
        : route(props.routeName)

    router.get(url,
        { search: value },
        { preserveState: true, replace: true }
    )
}, 400)

watch(search, handleSearch)
</script>

<template>
    <div class="relative w-full sm:w-96 mb-4">
        <input v-model="search" type="text" placeholder="Search..."
            class="max-w-[300px] border border-yellow-300 rounded-lg pl-10 pr-4 py-2 w-full bg-gray-200" />
    </div>
</template>