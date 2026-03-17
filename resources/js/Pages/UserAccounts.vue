<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const users = computed(() => page.props.users ?? {})
const filters = computed(() => page.props.filters ?? {})

const colleges = computed(() =>
    page.props.colleges.map(c => ({ label: c.college_name, value: c.id }))
)

const departments = computed(() =>
    page.props.departments.map(d => ({ label: d.department_name, value: d.id }))
)

const form = useForm({
    username: '',
    email: '',
    password: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    employee_number: '',
    gender: '',
    contact_number: '',
    college_id: '',
    department_id: '',
    status: '',
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
        form.post('/UserAccounts', options)
    }
    if (modalType.value === 'edit') {
        form.put(`/UserAccounts/${modalData.value.id}`, options)
    }
    if (modalType.value === 'delete') {
        form.delete(`/UserAccounts/${modalData.value.id}`, options)
    }
}
</script>

<template>
    <DynamicLayout :items="users" :filters="filters" :route-name="'UserAccounts'" :title="'User Account Management'"
        :buttonName="'User Account'" @add="openModal('add')" @edit="(row) => openModal('edit', row)"
        @delete="(row) => openModal('delete', row)" @view="(row) => openModal('view', row)" :columns="[
            { label: 'Username', field: 'username' },
            { label: 'Employee Number', field: 'employee_number' },
            { label: 'Gender', field: 'gender' },
            { label: 'College Name', field: 'college_id', render: (item) => item.college?.college_name ?? 'N/A' },
            { label: 'Department Name', field: 'department_id', render: (item) => item.department?.department_name ?? 'N/A' },
            { label: 'Contact Number', field: 'contact_number' },
            { label: 'Contact Email', field: 'email' },
            { label: 'Status', field: 'status' },
        ]" />

    <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData" title="User Account" :fields="[
        { label: 'Username', field: 'username' },
        { label: 'Email', field: 'email' },
        { label: 'Password', field: 'password', type: 'password' },
        { label: 'First Name', field: 'first_name' },
        { label: 'Middle Name', field: 'middle_name' },
        { label: 'Last Name', field: 'last_name' },
        { label: 'Employee Number', field: 'employee_number' },
        {
            label: 'Gender', field: 'gender', type: 'select', options: [
                { label: 'Male', value: 'male' },
                { label: 'Female', value: 'female' },
                { label: 'Other', value: 'other' }]
        },
        { label: 'Contact Number', field: 'contact_number' },
        { label: 'Departments', field: 'department_id', type: 'select', options: departments },
        { label: 'Colleges', field: 'college_id', type: 'select', options: colleges },
        {
            label: 'Status', field: 'status', type: 'select', options: [
                { label: 'Active', value: 'active' },
                { label: 'Inactive', value: 'inactive' },
                { label: 'Suspended', value: 'suspended' },
                { label: 'Pending', value: 'pending' }]
        },
    ]" @close="closeModal" @submit="handleSubmit" />
</template>