<script setup lang="ts">
defineProps<{
  isOpen: boolean
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  isDanger?: boolean
}>()

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'cancel'): void
  (e: 'update:isOpen', value: boolean): void
}>()

const handleConfirm = () => {
  emit('confirm')
  emit('update:isOpen', false)
}

const handleCancel = () => {
  emit('cancel')
  emit('update:isOpen', false)
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="handleCancel">
    <div class="bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all">
      <div class="p-6">
        <h3 class="text-xl font-bold text-textWhite mb-2">{{ title }}</h3>
        <p class="text-textSecondary text-sm mb-6">{{ message }}</p>

        <div class="flex justify-end gap-3">
          <button class="px-4 py-2 rounded-lg text-textSecondary hover:text-textWhite font-semibold transition-colors" @click="handleCancel">
            {{ cancelText || 'Anuluj' }}
          </button>
          <button :class="['px-4 py-2 rounded-lg font-bold text-textWhite transition-all', isDanger ? 'bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/20' : 'bg-primary hover:bg-primary/90 shadow-lg shadow-primary/20']" @click="handleConfirm">
            {{ confirmText || 'Potwierdź' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
