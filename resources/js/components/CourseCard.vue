<script setup lang="ts">
import type { CourseCard as CourseCardType } from '@/constants/data'
import { useI18n } from '@/composables/useI18n'
import IconClock from '@/icons/IconClock.vue'
import IconUsers from '@/icons/IconUsers.vue'
import IconCheck from '@/icons/IconCheck.vue'
import IconMedal from '@/icons/IconMedal.vue'

const { t } = useI18n()

const props = defineProps<{
  course: CourseCardType
}>()

const emit = defineEmits<{
  (e: 'open', id: string): void
}>()

const getLevelClass = (level: string) => {
  switch (level.toLowerCase()) {
    case 'junior':
      return 'bg-emerald-500/20 text-emerald-400 border-emerald-400/40'
    case 'mid':
      return 'bg-amber-500/20 text-amber-400 border-amber-400/40'
    case 'senior':
      return 'bg-red-500/20 text-red-400 border-red-400/40'
    default:
      return 'bg-emerald-500/20 text-emerald-400 border-emerald-400/40'
  }
}
</script>

<template>
  <article class="group flex flex-col overflow-hidden rounded-2xl border border-strokePrimary/30 bg-card transition-all duration-300 hover:border-cyan-400/50 hover:shadow-xl hover:shadow-cyan-400/10 cursor-pointer" :aria-label="t('course.showDetails')" @click="emit('open', props.course.id)">
    <div class="relative h-44 w-full overflow-hidden bg-bgSecondary">
      <img v-if="props.course.image_path" :src="props.course.image_path" :alt="props.course.title" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
      <div v-else class="flex h-full w-full items-center justify-center text-6xl text-textSecondary/50 bg-gradient-to-br from-bgSecondary to-bgPrimary">📚</div>
    </div>

    <div class="flex flex-1 flex-col gap-4 p-5">
      <h3 class="text-xl font-bold text-textWhite leading-tight group-hover:text-cyan-400 transition-colors line-clamp-2">
        {{ props.course.title }}
      </h3>

      <p class="text-sm text-textSecondary line-clamp-2 leading-relaxed">
        {{ props.course.subtitle || props.course.description }}
      </p>

      <div class="flex flex-wrap gap-2">
        <span v-for="tag in props.course.tags?.slice(0, 2)" :key="tag" class="px-3 py-1.5 bg-cyan-500/15 text-cyan-400 border border-cyan-400/30 rounded-full text-xs font-semibold">
          {{ tag }}
        </span>
      </div>

      <div>
        <span :class="['inline-block px-3 py-1.5 rounded-full text-xs font-semibold border lowercase', getLevelClass(props.course.level)]">
          {{ props.course.level }}
        </span>
      </div>

      <div class="grid grid-cols-2 gap-y-2 pt-3 mt-auto border-t border-strokePrimary/20 text-sm text-textSecondary">
        <div class="flex items-center gap-1.5">
          <IconMedal class="w-4 h-4" />
          <span>{{ props.course.reward }} PD</span>
        </div>

        <div class="flex items-center gap-1.5">
          <IconCheck class="w-4 h-4" />
          <span>{{ props.course.tasks }} {{ t('course.lessonsLabel') }}</span>
        </div>

        <div class="flex items-center gap-1.5">
          <IconClock class="w-4 h-4" />
          <span>{{ props.course.duration }}</span>
        </div>

        <div class="flex items-center gap-1.5">
          <IconUsers class="w-4 h-4" />
          <span>{{ props.course.enrolledCount ?? 0 }} osób na kursie</span>
        </div>
      </div>
    </div>
  </article>
</template>
