<script setup lang="ts">
import type { CourseCard as CourseCardType } from '@/constants/data'
import CourseCard from './CourseCard.vue'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()

const props = defineProps<{
  courses: CourseCardType[]
}>()

const emit = defineEmits<{
  (e: 'open', id: string): void
}>()
</script>

<template>
  <div class="space-y-5">
    <h2 class="text-lg font-semibold">{{ t('courses.title') }}</h2>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <CourseCard v-for="course in props.courses" :key="course.id" :course="course" @open="emit('open', $event)" />
    </div>
  </div>
</template>
