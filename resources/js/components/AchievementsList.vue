<script setup lang="ts">
import type { Achievement } from '@/constants/data'
import { useI18n } from '@/composables/useI18n'

const { t, isEnglish } = useI18n()

const props = defineProps<{
  items: Achievement[]
}>()

import { computed } from 'vue'

const sortedAchievements = computed(() => {
  return [...props.items].sort((a, b) => {
    if (a.isEarned === b.isEarned) return 0
    return a.isEarned ? -1 : 1
  })
})
</script>

<template>
  <section class="space-y-4 rounded-3xl border border-strokePrimary/30 bg-bgSecondary/60 p-6">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">{{ t('profile.achievements') }}</h3>
    </div>
    <div class="space-y-3">
      <article v-for="achievement in sortedAchievements" :key="achievement.id" class="flex items-center justify-between rounded-2xl border border-strokePrimary/30 bg-bgPrimary/40 px-4 py-3 transition-opacity" :class="{ 'opacity-50 grayscale': !achievement.isEarned }">
        <div>
          <div class="flex items-center gap-2">
            <p class="text-lg font-semibold text-textWhite">
              {{ isEnglish && achievement.title_en ? achievement.title_en : achievement.title }}
            </p>
            <span class="rounded-full bg-textPrimary/20 px-2 py-0.5 text-[10px] uppercase tracking-wider text-textPrimary">
              {{ achievement.level }}
            </span>
          </div>
          <p class="text-xs text-textSecondary mt-1">
            {{ isEnglish && achievement.description_en ? achievement.description_en : achievement.description }}
          </p>
        </div>
        <div class="flex flex-col items-end gap-1">
          <span v-if="!achievement.isEarned" class="text-[10px] uppercase font-bold text-textSecondary">{{ t('profile.notEarned') }}</span>
        </div>
      </article>
    </div>
  </section>
</template>
