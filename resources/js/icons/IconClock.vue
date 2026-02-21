<script setup lang="ts">
defineProps<{
  size?: string
  color?: string
}>()
</script>

<template>
  <svg :width="size || '24'" :height="size || '24'" :class="color || 'text-current'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10" />
    <polyline points="12 6 12 12 16 14" />
  </svg>
</template>
