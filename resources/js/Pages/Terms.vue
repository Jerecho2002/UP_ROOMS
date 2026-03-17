<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const terms = computed(() => page.props.terms ?? {})
const filters = computed(() => page.props.filters ?? {})

const form = useForm({
  term_name: '',
  term_code: '',
  term_type: '',
  start_date: '',
  end_date: '',
  enrollment_start: '',
  enrollment_end: '',
  classes_start: '',
  classes_end: '',
  examination_start: '',
  examination_end: '',
  is_current: 0,
  status: '',
  academic_year: '',
  notes: ''
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
  if ('is_current' in data) {
    data.is_current = data.is_current ? 1 : 0
  }
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
    form.post('/Terms', options)
  }

  if (modalType.value === 'edit') {
    form.put(`/Terms/${modalData.value.id}`, options)
  }

  if (modalType.value === 'delete') {
    form.delete(`/Terms/${modalData.value.id}`, options)
  }
}
</script>

<template>
  <DynamicLayout :items="terms" :filters="filters" :route-name="'Terms'" :title="'Term Management'" :buttonName="'Term'"
    @add="openModal('add')" @edit="(row) => openModal('edit', row)" @delete="(row) => openModal('delete', row)"
    @view="(row) => openModal('view', row)" :columns="[
      { label: 'Term Name', field: 'term_name' },
      { label: 'Term Code', field: 'term_code' },
      { label: 'Term Type', field: 'term_type' },
      { label: 'Start Date', field: 'start_date' },
      { label: 'End Date', field: 'end_date' },
      { label: 'Enrollment Start', field: 'enrollment_start' },
      { label: 'Enrollment End', field: 'enrollment_end' },
    ]" />

  <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData" title="Term" :fields="[
    { label: 'Term Name', field: 'term_name' },
    { label: 'Term Code', field: 'term_code' },
    { label: 'Term Type', field: 'term_type' },
    { label: 'Start Date', field: 'start_date', type: 'date' },
    { label: 'End Date', field: 'end_date', type: 'date' },
    { label: 'Enrollment Start', field: 'enrollment_start', type: 'date' },
    { label: 'Enrollment End', field: 'enrollment_end', type: 'date' },
    { label: 'Classes Start', field: 'classes_start', type: 'date' },
    { label: 'Classes End', field: 'classes_end', type: 'date' },
    { label: 'Examination Start', field: 'examination_start', type: 'date' },
    { label: 'Examination End', field: 'examination_end', type: 'date' },
    { label: 'Is Current', field: 'is_current', type: 'checkbox' },
    {
      label: 'Status', field: 'status', type: 'select', options: [
        { label: 'Upcoming', value: 'upcoming' },
        { label: 'Active', value: 'active' },
        { label: 'Completed', value: 'completed' },
        { label: 'Cancelled', value: 'cancelled' },
      ]
    },
    {
      label: 'Term Type', field: 'term_type', type: 'select', options: [
        { label: 'Semester', value: 'semester' },
        { label: 'Trimester', value: 'trimester' },
        { label: 'Quarter', value: 'quarter' },
        { label: 'Summer', value: 'summer' },
        { label: 'Special', value: 'special' },
      ]
    },
    { label: 'Academic Year', field: 'academic_year' },
    { label: 'Notes', field: 'notes', type: 'textarea' },
  ]" @close="closeModal" @submit="handleSubmit" />
</template>