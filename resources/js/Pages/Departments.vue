<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const departments = computed(() => page.props.departments ?? {})
const filters = computed(() => page.props.filters ?? {})

const colleges = computed(() =>
  page.props.colleges.map(c => ({ label: c.college_name, value: c.id }))
)

const users = computed(() =>
  page.props.users.map(u => ({ label: u.username, value: u.id }))
)

const form = useForm({
  department_name: '',
  department_code: '',
  college_id: '',
  department_head_id: '',
  description: '',
  office_location: '',
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
    form.post('/Departments', options)
  }
  if (modalType.value === 'edit') {
    form.put(`/Departments/${modalData.value.id}`, options)
  }
  if (modalType.value === 'delete') {
    form.delete(`/Departments/${modalData.value.id}`, options)
  }
}
</script>

<template>
  <DynamicLayout :items="departments" :filters="filters" :route-name="'Departments'" :title="'Department Management'"
    :buttonName="'Department'" @add="openModal('add')" @edit="(row) => openModal('edit', row)"
    @delete="(row) => openModal('delete', row)" @view="(row) => openModal('view', row)" :columns="[
      { label: 'Department Name', field: 'department_name' },
      { label: 'Department Code', field: 'department_code' },
      { label: 'College Name', field: 'college_id', render: (item) => item.college?.college_name ?? 'N/A' },
      { label: 'Department Head', field: 'department_head_id', render: (item) => item.head?.username ?? 'N/A' },
      { label: 'Office', field: 'office_location' },
      { label: 'Phone Number', field: 'contact_phone' },
    ]" />

  <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData" title="College" :fields="[
    { label: 'Department Name', field: 'department_name' },
    { label: 'Department Code', field: 'department_code' },
    { label: 'Colleges', field: 'college_id', type: 'select', options: colleges },
    { label: 'Department Head', field: 'department_head_id', type: 'select', options: users },
    { label: 'Description', field: 'description', type: 'textarea' },
    { label: 'Office', field: 'office_location' },
    { label: 'Contact Email', field: 'contact_email' },
    { label: 'Contact Phone', field: 'contact_phone' },
  ]" @close="closeModal" @submit="handleSubmit" />
</template>