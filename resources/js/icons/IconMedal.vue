<script setup lang="ts">
defineProps<{
  size?: string
  color?: string
}>()
</script>

<template>
  <svg :width="size || '24'" :height="size || '24'" :class="color || 'text-current'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="8" r="6" />
    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />
  </svg>
</template>
