<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const roomtypes = computed(() => page.props.roomtypes ?? {})
const filters = computed(() => page.props.filters ?? {})

const form = useForm({
    room_type_name: '',
    slug: '',
    description: '',
    default_capacity: '',
})

const openModal = (type, row = null) => {
    modalType.value = type
    modalData.value = row
    isModalVisible.value = true
}

const closeModal = () => {
    isModalVisible.value = false
}

const handleSubmit = (data) => {
    Object.assign(form, data)

    const options = {
        onSuccess: closeModal,
        onError: (errors) => {
            const firstError = Object.values(errors)[0]
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message: firstError, type: 'error' }
            }))
        }
    }

    if (modalType.value === 'add') {
        form.post('/RoomTypes', options)
    }

    if (modalType.value === 'edit') {
        form.put(`/RoomTypes/${modalData.value.id}`, options)
    }

    if (modalType.value === 'delete') {
        form.delete(`/RoomTypes/${modalData.value.id}`, options)
    }
}
</script>

<template>
    <DynamicLayout :items="roomtypes" :filters="filters" :route-name="'RoomTypes'" :title="'Room Type Management'"
        :buttonName="'Room Type'" @add="openModal('add')" @edit="(row) => openModal('edit', row)"
        @delete="(row) => openModal('delete', row)" @view="(row) => openModal('view', row)" :columns="[
            { label: 'Room Type Name', field: 'room_type_name' },
            { label: 'Slug', field: 'slug' },
            { label: 'Default Capacity', field: 'default_capacity' },
        ]" />

    <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData" title="Room Type" :fields="[
        { label: 'Room Type Name', field: 'room_type_name' },
        { label: 'Slug', field: 'slug' },
        { label: 'description', field: 'description', type: 'textarea' },
        { label: 'Default Capacity', field: 'default_capacity', type: 'number' },
    ]" @close="closeModal" @submit="handleSubmit" />
</template>