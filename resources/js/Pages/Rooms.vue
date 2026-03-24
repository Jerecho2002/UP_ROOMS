<script setup>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import DynamicLayout from '@/Layouts/DynamicLayout.vue'
import DynamicModal from '@/Components/DynamicModal.vue'

const isModalVisible = ref(false)
const modalType = ref(null)
const modalData = ref(null)

const page = usePage()
const rooms = computed(() => page.props.rooms ?? {})
const filters = computed(() => page.props.filters ?? {})
const equipment = computed(() => page.props.equipment ?? [])

const buildings = computed(() =>
  page.props.buildings.map(b => ({ label: b.building_name, value: b.id }))
)

const departments = computed(() =>
  page.props.departments.map(d => ({ label: d.department_name, value: d.id }))
)

const colleges = computed(() =>
  page.props.colleges.map(c => ({ label: c.college_name, value: c.id }))
)

const room_types = computed(() =>
  page.props.roomTypes.map(rt => ({ label: rt.room_type_name, value: rt.id }))
)

const users = computed(() =>
  page.props.users.map(u => ({ label: `${u.first_name} ${u.last_name}`, value: u.id }))
)

const form = useForm({
  room_name: '',
  room_code: '',
  building_id: '',
  college_id: '',
  department_id: '',
  room_type_id: '',
  assigned_user_id: '',
  floor_number: '',
  location: '',
  capacity: '',
  description: '',
  equipment: []
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
    form.post('/Rooms', options)
  }
  if (modalType.value === 'edit') {
    form.put(`/Rooms/${modalData.value.id}`, options)
  }
  if (modalType.value === 'delete') {
    form.delete(`/Rooms/${modalData.value.id}`, options)
  }
}
</script>

<template>
  <DynamicLayout :items="rooms" :filters="filters" :route-name="'Rooms'" :title="'Room Management'" :buttonName="'Room'"
    @add="openModal('add')" @edit="(row) => openModal('edit', row)" @delete="(row) => openModal('delete', row)"
    @view="(row) => openModal('view', row)" :columns="[
      { label: 'Room Name', field: 'room_name' },
      { label: 'Room Code', field: 'room_code' },
      { label: 'Building Name', field: 'building_id', render: (item) => item.building?.building_name ?? 'N/A' },
      { label: 'College Name', field: 'college_id', render: (item) => item.college?.college_name ?? 'N/A' },
      { label: 'Department Name', field: 'department_id', render: (item) => item.department?.department_name ?? 'N/A' },
      { label: 'Room Type', field: 'room_type_id', render: (item) => item.room_type?.room_type_name ?? 'N/A' },
      { label: 'Assigned User', field: 'assigned_user_id', render: (item) => item.assigned_user || 'N/A' },
      { label: 'Capacity', field: 'capacity' },
      { label: 'Location', field: 'location' },
    ]" />

  <DynamicModal v-if="isModalVisible" :type="modalType" :data="modalData || {}"
    :key="modalType + (modalData?.id || 'new')" title="Room" :fields="[
      { label: 'Room Name', field: 'room_name' },
      { label: 'Room Code', field: 'room_code' },
      { label: 'Building', field: 'building_id', type: 'select', options: buildings },
      { label: 'College', field: 'college_id', type: 'select', options: colleges },
      { label: 'Department', field: 'department_id', type: 'select', options: departments },
      {
        label: 'Equipment',
        field: 'equipment',
        type: 'equipment-qty',
        options: equipment.map(e => ({
          id: e.id,
          name: e.equipment_name,
          stock: e.quantity ?? 0
        })),
        render: (items) => items.map(i => `${i.name} (Qty: ${i.qty})`)
      },
      { label: 'Room Type', field: 'room_type_id', type: 'select', options: room_types },
      { label: 'Assigned User', field: 'assigned_user_id', type: 'select', options: users },
      { label: 'Floor Number', field: 'floor_number', type: 'number' },
      { label: 'Location', field: 'location' },
      { label: 'Capacity', field: 'capacity', type: 'number' },
      { label: 'Description', field: 'description', type: 'textarea' },
    ]" @close="closeModal" @submit="handleSubmit" />
</template>