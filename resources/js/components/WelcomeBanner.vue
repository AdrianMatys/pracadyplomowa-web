<script setup lang="ts">
import { useI18n } from '@/composables/useI18n'

type BannerStat = {
  label: string
  value: string
}

const props = defineProps<{
  stats: BannerStat[]
  isLoggedIn: boolean
  userLevel?: string
}>()

defineEmits<{
  (e: 'primary'): void
  (e: 'secondary'): void
  (e: 'login'): void
}>()

const { t } = useI18n()
</script>

<template>
  <div class="relative overflow-hidden rounded-3xl border border-strokePrimary/50 bg-bgPrimary p-8 shadow-[0_0_40px_-10px_rgba(2,178,208,0.1)]">
    <div class="absolute top-0 right-0 -mr-20 -mt-20 h-64 w-64 rounded-full bg-textPrimary/5 blur-[100px]"></div>

    <div class="relative z-10">
      <div v-if="!isLoggedIn" class="space-y-4 text-center md:text-left">
        <h1 class="text-2xl font-bold text-textPrimary md:text-3xl">
          {{ t('banner.guestTitle') || 'Dołącz do nas!' }}
        </h1>
        <p class="max-w-2xl text-sm leading-relaxed text-textWhite/80">
          {{ t('banner.guestDescription') || 'Zaloguj się, aby uzyskać dostęp do kursów i śledzić swoje postępy.' }}
        </p>
        <button class="mt-4 inline-flex items-center justify-center rounded-xl bg-textPrimary px-6 py-3 text-sm font-bold text-bgPrimary transition hover:bg-textPrimary/90 focus:outline-none focus:ring-2 focus:ring-textPrimary focus:ring-offset-2 focus:ring-offset-bgPrimary" @click="$emit('login')">
          {{ t('auth.login') || 'Zaloguj się' }}
        </button>
      </div>

      <div v-else>
        <h1 class="text-xl font-bold text-textPrimary md:text-2xl">{{ t('banner.welcome') }} {{ userLevel }} - {{ t('banner.developerWord') }}!</h1>

        <p class="mt-3 max-w-3xl text-sm leading-relaxed text-textWhite">
          {{ t('banner.description') }}
        </p>

        <div class="mt-6">
          <p class="mb-3 text-xs font-bold text-textPrimary uppercase tracking-wide">
            {{ t('banner.available') }}
          </p>
          <div class="flex flex-wrap gap-3">
            <div v-for="(stat, index) in props.stats" :key="index" class="flex items-center gap-2 rounded bg-cardHover px-4 py-2 border border-strokePrimary/30">
              <span class="text-xs text-textPrimary">{{ stat.label }}</span>
              <span class="text-xs font-bold text-textWhite">{{ stat.value }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
