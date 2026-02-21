<script setup lang="ts">
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

defineProps<{
  output: string
  isError?: boolean
  isLoading?: boolean
}>()
</script>

<template>
  <div class="flex h-full flex-col overflow-hidden rounded-xl border border-strokePrimary/30 bg-[#111a2c]">
    <div class="border-b border-strokePrimary/30 px-4 py-2 flex justify-between items-center">
      <span class="text-xs font-semibold uppercase tracking-wider text-textSecondary">{{ t('console.title') }}</span>
      <span v-if="isLoading" class="text-xs text-textPrimary animate-pulse">{{ t('console.executing') }}</span>
    </div>
    <div class="h-full w-full overflow-auto bg-[#0d1422] p-4 font-mono text-sm leading-relaxed">
      <pre v-if="output" :class="[isError ? 'text-red-400' : 'text-emerald-400', 'whitespace-pre-wrap break-all']">{{ output }}</pre>
      <div v-else-if="!isLoading" class="text-textSecondary opacity-50">
        {{ t('console.placeholder') }}
      </div>
    </div>
  </div>
</template>
