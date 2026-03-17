<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const colleges = computed(() => page.props.colleges ?? {})
const filters = computed(() => page.props.filters ?? {})
const deans = computed(() =>
    page.props.deans.map(d => ({ label: d.username, value: d.id }))
)

const form = useForm({
    college_name: '',
    college_code: '',
    description: '',
    dean_id: '',
    contact_email: '',
    contact_phone: '',
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
        form.post('/CollegeDashboard', options)
    }
    if (modalType.value === 'edit') {
        form.put(`/CollegeDashboard/${modalData.value.id}`, options)
    }
    if (modalType.value === 'delete') {
        form.delete(`/CollegeDashboard/${modalData.value.id}`, options)
    }
}
</script>

<template>
    <DynamicLayout :items="colleges" :filters="filters" :route-name="'CollegeDashboard'" :title="'College Management'"
        :buttonName="'College'" @add="openModal('add')" @edit="(row) => openModal('edit', row)"
        @delete="(row) => openModal('delete', row)" @view="(row) => openModal('view', row)" :columns="[
            { label: 'College Name', field: 'college_name' },
            { label: 'Code', field: 'college_code' },
            { label: 'Dean', field: 'dean', render: (item) => item.dean?.username ?? 'N/A' },
            { label: 'Email', field: 'contact_email' },
            { label: 'Phone Number', field: 'contact_phone' },
        ]" />

    <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData" title="College" :fields="[
        { label: 'College Name', field: 'college_name' },
        { label: 'College Code', field: 'college_code' },
        { label: 'Description', field: 'description', type: 'textarea' },
        { label: 'Dean', field: 'dean_id', type: 'select', options: deans },
        { label: 'Contact Email', field: 'contact_email', type: 'email' },
        { label: 'Contact Phone', field: 'contact_phone' },
    ]" @close="closeModal" @submit="handleSubmit" />
</template>