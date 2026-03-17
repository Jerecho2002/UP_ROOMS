<script setup>
import { ref, watch, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

const page = usePage()

const visible = ref(false)
const message = ref('')
const type = ref('success')

let timeout = null

const showToast = (msg, toastType = 'success') => {
    message.value = msg
    type.value = toastType
    visible.value = true

    clearTimeout(timeout)
    timeout = setTimeout(() => {
        visible.value = false
    }, 3000)
}

onMounted(() => {
    window.addEventListener('toast', (event) => {
        showToast(event.detail.message, event.detail.type)
    })
})

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return

        if (flash.success) showToast(flash.success, 'success')
        if (flash.error) showToast(flash.error, 'error')
        if (flash.warning) showToast(flash.warning, 'warning')
        if (flash.info) showToast(flash.info, 'info')
    },
    { immediate: true }
)

defineExpose({ showToast })
</script>

<template>
    <transition name="fade">
        <div v-if="visible"
            class="fixed top-5 right-5 z-50 px-5 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-3"
            :class="{
                'bg-green-600 text-white': type === 'success',
                'bg-red-600 text-white': type === 'error',
                'bg-yellow-500 text-white': type === 'warning',
                'bg-blue-600 text-white': type === 'info',
            }">

            <FontAwesomeIcon :icon="{
                success: 'check',
                error: 'times',
                warning: 'exclamation',
                info: 'info'
            }[type]" />

            {{ message }}
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>