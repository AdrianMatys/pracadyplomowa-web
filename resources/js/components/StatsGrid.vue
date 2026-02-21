<script setup lang="ts">
import type { UserStat } from '@/constants/data'

const props = defineProps<{
  stats: UserStat[]
}>()
</script>

<template>
  <section class="grid gap-4 md:grid-cols-3">
    <article v-for="stat in props.stats" :key="stat.id" class="rounded-2xl border border-strokePrimary/30 bg-bgSecondary/40 p-6 transition hover:bg-bgSecondary/60">
      <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-textSecondary">
        {{ stat.label }}
      </p>
      <p class="mt-2 text-4xl font-bold text-textPrimary">{{ stat.value }}</p>
    </article>
  </section>
</template>
