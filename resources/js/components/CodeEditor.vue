<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

defineProps<{
  modelValue: string
  language?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const textareaRef = ref<HTMLTextAreaElement | null>(null)

const handleInput = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
  emit('update:modelValue', target.value)
}

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Tab') {
    event.preventDefault()
    const target = event.target as HTMLTextAreaElement
    const start = target.selectionStart
    const end = target.selectionEnd

    const value = target.value
    target.value = value.substring(0, start) + '  ' + value.substring(end)
    target.selectionStart = target.selectionEnd = start + 2
    emit('update:modelValue', target.value)
  }
}
</script>

<template>
  <div class="flex h-full flex-col overflow-hidden rounded-xl border border-strokePrimary/30 bg-inputBg">
    <div class="flex items-center justify-between border-b border-strokePrimary/30 bg-card px-4 py-2">
      <span class="text-xs font-semibold uppercase tracking-wider text-textSecondary">{{ language || t('editor.code') }}</span>
    </div>
    <textarea ref="textareaRef" class="h-full w-full resize-none bg-transparent p-4 font-mono text-sm leading-relaxed text-textWhite focus:outline-none whitespace-pre-wrap break-words" :value="modelValue" spellcheck="false" @input="handleInput" @keydown="handleKeydown"></textarea>
  </div>
</template>
