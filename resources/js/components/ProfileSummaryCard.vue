<script setup lang="ts">
import { computed, ref } from 'vue'
import type { ProfileSummary } from '@/constants/data'
import { useI18n } from '@/composables/useI18n'
import IconInfo from '@/icons/IconInfo.vue'

const { t } = useI18n()

const props = defineProps<{
  summary: ProfileSummary
}>()

const totalXP = computed(() => props.summary.xp ?? 0)
const userLevel = computed(() => Math.floor(totalXP.value / 1000) + 1)
const currentXP = computed(() => totalXP.value % 1000)
const xpProgress = computed(() => (currentXP.value / 1000) * 100)

const levelTitle = computed(() => {
  const level = userLevel.value
  if (level <= 5) return 'Junior'
  if (level <= 15) return 'Mid'
  return 'Senior'
})

const showLevelTooltip = ref(false)
</script>

<template>
  <section class="w-full rounded-3xl border border-strokePrimary/30 bg-bgSecondary/60 p-8">
    <div class="flex flex-col gap-8">
      <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
        <div class="flex items-center gap-5">
          <div class="relative flex h-20 w-20 items-center justify-center rounded-full border-2 border-strokePrimary bg-bgPrimary text-4xl shadow-lg">
            <img v-if="props.summary.avatarUrl" :src="props.summary.avatarUrl" :alt="props.summary.nickname" class="h-full w-full rounded-full object-cover" />
            <span v-else>?</span>
            <span class="absolute -bottom-1 -right-1 flex h-8 min-w-8 items-center justify-center rounded-full bg-textPrimary text-xs font-bold text-bgPrimary px-1">{{ userLevel }}</span>
          </div>
          <div>
            <div class="flex items-center gap-3">
              <h2 class="text-3xl font-bold text-textWhite">{{ props.summary.nickname }}</h2>
              <span class="text-2xl text-textSecondary">-</span>
              <span class="text-2xl font-semibold text-textPrimary">{{ levelTitle }}</span>
              <div class="relative">
                <button class="flex items-center justify-center w-5 h-5 rounded-full border border-textSecondary/50 text-textSecondary hover:text-textPrimary hover:border-textPrimary transition-colors" @mouseenter="showLevelTooltip = true" @mouseleave="showLevelTooltip = false" @focus="showLevelTooltip = true" @blur="showLevelTooltip = false">
                  <IconInfo class="w-3.5 h-3.5" />
                </button>
                <div v-if="showLevelTooltip" class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-48 px-3 py-2 bg-bgPrimary border border-strokePrimary/50 rounded-lg shadow-lg text-xs text-textSecondary z-50">
                  <p class="font-semibold text-textWhite mb-1">{{ t('profile.levelInfo') }}</p>
                  <ul class="space-y-0.5">
                    <li><span class="text-textPrimary">Junior:</span> lvl 1-5</li>
                    <li><span class="text-textPrimary">Mid:</span> lvl 6-15</li>
                    <li><span class="text-textPrimary">Senior:</span> lvl 16+</li>
                  </ul>
                </div>
              </div>
            </div>
            <p class="text-sm text-textSecondary mt-1">{{ props.summary.description }}</p>
            <div class="mt-2 flex items-center gap-2">
              <span class="text-sm font-bold text-textPrimary">{{ currentXP }}/1000 PD</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col items-end gap-1">
          <span class="text-sm font-medium text-textPrimary">{{ t('profile.courseSummary') }}</span>
          <div class="flex gap-8 text-center">
            <div>
              <div class="text-xl font-bold text-textPrimary">{{ props.summary.completed }}</div>
              <div class="text-[10px] uppercase tracking-wider text-textSecondary">
                {{ t('lessonStatus.completed') }}
              </div>
            </div>
            <div>
              <div class="text-xl font-bold text-textPrimary">{{ props.summary.inProgress }}</div>
              <div class="text-[10px] uppercase tracking-wider text-textSecondary">
                {{ t('lessonStatus.inProgress') }}
              </div>
            </div>
            <div>
              <div class="text-xl font-bold text-textPrimary flex items-center gap-1">
                {{ props.summary.waitingTasks }}
                <span class="text-red-500">🔥</span>
              </div>
              <div class="text-[10px] uppercase tracking-wider text-textSecondary">
                {{ t('profile.streak') }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="relative w-full">
        <div class="h-3 w-full overflow-hidden rounded-full bg-bgPrimary border border-strokePrimary/30">
          <div class="h-full bg-gradient-to-r from-textPrimary to-blue-500 transition-all duration-500 ease-out relative" :style="{ width: `${xpProgress}%` }">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-full bg-white/30"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
