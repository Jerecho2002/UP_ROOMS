<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const filters = computed(() => page.props.filters ?? {})
const equipment = computed(() => page.props.equipment ?? {})

const form = useForm({
    equipment_name: '',
    inventory_id: '',
    property_id: '',
    cfic_id: '',
    quantity: '',
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
        form.post('/Equipment', options)
    }

    if (modalType.value === 'edit') {
        form.put(`/Equipment/${modalData.value.id}`, options)
    }

    if (modalType.value === 'delete') {
        form.delete(`/Equipment/${modalData.value.id}`, options)
    }
}
</script>

<template>
    <DynamicLayout :items="equipment" :filters="filters" :route-name="'Equipment'" :title="'Equipment'"
        :buttonName="'Equipment'" @add="openModal('add')" @edit="(row) => openModal('edit', row)"
        @delete="(row) => openModal('delete', row)" @view="(row) => openModal('view', row)" :columns="[
            { label: 'Equipment Name', field: 'equipment_name' },
            { label: 'Inventory ID', field: 'inventory_id' },
            { label: 'Property ID', field: 'property_id' },
            { label: 'CFIC ID', field: 'cfic_id' },
            { label: 'Quantity', field: 'quantity' },
        ]" />

    <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData || {}"
        :key="modalType + (modalData?.id || 'new')" title="Equipment" :fields="[
            { label: 'Equipment Name', field: 'equipment_name' },
            { label: 'Inventory ID', field: 'inventory_id' },
            { label: 'Property ID', field: 'property_id' },
            { label: 'CFIC ID', field: 'cfic_id' },
            { label: 'Quantity', field: 'quantity', type: 'number' },
        ]" @close="closeModal" @submit="handleSubmit" />
</template>