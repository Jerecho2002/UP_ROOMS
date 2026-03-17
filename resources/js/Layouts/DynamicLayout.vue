<script setup>
import { ref } from 'vue'
import DynamicTable from '@/Components/DynamicTable.vue'
import SearchInput from '@/Components/SearchInput.vue'
import Navbar from '@/Components/Navbar.vue'
import Sidebar from '@/Components/Sidebar.vue'
import DynamicButton from '@/Components/DynamicButton.vue'
import Toast from '@/Components/Toast.vue'

defineProps({
    items: Object,
    title: String,
    buttonName: String,
    routeName: String,
    columns: Array,
    filters: Object,
})

const sidebarVisible = ref(true)

const toggleSidebar = () => {
    sidebarVisible.value = !sidebarVisible.value
}

defineEmits(['add', 'edit', 'view', 'delete'])

</script>

<template>
    <div class="flex flex-col h-screen bg-gray-200">
        <Navbar @toggleSidebar="toggleSidebar" />
        <Toast />
        <div :class="[
            'flex flex-1 h-full overflow-hidden relative mt-14',
            sidebarVisible ? 'lg:grid lg:grid-cols-[256px_1fr]' : 'flex'
        ]">
            <Sidebar :sidebarOpen="sidebarVisible" @toggleSidebar="toggleSidebar" :class="[
                'lg:relative lg:translate-x-0 lg:h-full',
                sidebarVisible ? 'lg:block' : 'lg:hidden'
            ]" />

            <main class="flex-1 p-6 overflow-y-auto transition-all duration-300">
                <div class="p-4 pb-[3%] bg-white rounded-md">
                    <div class="mb-6">
                        <div class="flex justify-between items-start">
                            <h1 class="text-xl md:text-2xl font-bold text-[#7A0C23]">
                                {{ title }}
                            </h1>

                            <div class="text-sm text-gray-500">
                                <span>UPCEBU &gt; COLLEGE</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <SearchInput :model-value="filters.search" :route-name="`/${routeName}`" />
                        <DynamicButton :label="`Add ${buttonName}`" @click="$emit('add')" />
                    </div>
                    <DynamicTable :items="items.data" :columns="columns" :links="items.links"
                        @edit="$emit('edit', $event)" @view="$emit('view', $event)" @delete="$emit('delete', $event)" />
                </div>
            </main>
        </div>
    </div>
</template>