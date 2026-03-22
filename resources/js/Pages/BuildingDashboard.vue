<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const buildings = computed(() => page.props.buildings ?? {})
const filters = computed(() => page.props.filters ?? {})
const colleges = computed(() => page.props.colleges ?? [])

const form = useForm({
    building_name: '',
    address: '',
    description: '',
    total_floors: '',
    total_rooms: '',
    has_elevator: 0,
    has_parking: 0,
    restroom_count: '',
    ramp_count: '',
    college_ids: []
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
        form.post('/BuildingDashboard', options)
    }

    if (modalType.value === 'edit') {
        form.put(`/BuildingDashboard/${modalData.value.id}`, options)
    }

    if (modalType.value === 'delete') {
        form.delete(`/BuildingDashboard/${modalData.value.id}`, options)
    }
}
</script>

<template>
    <DynamicLayout :items="buildings" :filters="filters" :route-name="'BuildingDashboard'"
        :title="'Building Management'" :buttonName="'Building'" @add="openModal('add')"
        @edit="(row) => openModal('edit', row)" @delete="(row) => openModal('delete', row)"
        @view="(row) => openModal('view', row)" :columns="[
            { label: 'Building Name', field: 'building_name' },
            { label: 'Address', field: 'address' },
            { label: 'Floors', field: 'total_floors' },
            { label: 'Rooms', field: 'total_rooms' },
        ]" />

    <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData || {}"
        :key="modalType + (modalData?.id || 'new')" title="Building" :fields="[
            { label: 'Building Name', field: 'building_name' },
            { label: 'Address', field: 'address' },
            { label: 'Description', field: 'description', type: 'textarea' },
            {
                label: 'Colleges',
                field: 'college_ids',
                type: modalType === 'view' ? 'display-badges' : 'multiselect',
                options: colleges.map(c => ({
                    label: c.college_name,
                    value: c.id
                })),
                render: (item) => (colleges.filter(c => (item.college_ids || []).includes(c.id))).map(c => c.college_name)
            },
            { label: 'Floors', field: 'total_floors', type: 'number' },
            { label: 'CR', field: 'restroom_count', type: 'number' },
            { label: 'Ramps', field: 'ramp_count', type: 'number' },
            { label: 'Rooms', field: 'total_rooms', type: 'number' },
            { label: 'Has Elevator', field: 'has_elevator', type: 'select', options: [{ label: 'Yes', value: 1 }, { label: 'No', value: 0 }] },
            { label: 'Has Parking', field: 'has_parking', type: 'select', options: [{ label: 'Yes', value: 1 }, { label: 'No', value: 0 }] },
        ]" @close="closeModal" @submit="handleSubmit" />
</template>