<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import type { Notification } from '@/composables/useNotifications'
import IconCheck from '@/icons/IconCheck.vue'
import IconClose from '@/icons/IconClose.vue'
import IconAlert from '@/icons/IconAlert.vue'

const props = defineProps<{
  notification: Notification
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const isVisible = ref(false)

onMounted(() => {
  requestAnimationFrame(() => {
    isVisible.value = true
  })
})

const styles = computed(() => {
  switch (props.notification.type) {
    case 'success':
      return 'border-green-500/50 bg-green-500/10 text-green-400'
    case 'error':
      return 'border-red-500/50 bg-red-500/10 text-red-400'
    case 'info':
    default:
      return 'border-blue-500/50 bg-blue-500/10 text-blue-400'
  }
})

const handleClose = () => {
  isVisible.value = false
  setTimeout(() => {
    emit('close')
  }, 300)
}
</script>

<template>
  <div class="pointer-events-auto mb-3 flex w-full max-w-sm items-start gap-3 rounded-xl border px-4 py-3 shadow-lg transition-all duration-300 backdrop-blur-md" :class="[styles, isVisible ? 'translate-x-0 opacity-100' : 'translate-x-8 opacity-0']" role="alert">
    <div class="mt-0.5">
      <IconCheck v-if="notification.type === 'success'" class="w-5 h-5" />
      <IconClose v-else-if="notification.type === 'error'" class="w-5 h-5" />
      <IconAlert v-else class="w-5 h-5" />
    </div>
    <div class="flex-1 text-sm font-medium leading-relaxed">
      {{ notification.message }}
    </div>
    <button class="ml-2 -mr-1 rounded-lg p-1 transition hover:bg-white/10 focus:outline-none focus:ring-2" aria-label="Close" @click="handleClose">
      <IconClose class="h-4 w-4" />
    </button>
  </div>
</template>
