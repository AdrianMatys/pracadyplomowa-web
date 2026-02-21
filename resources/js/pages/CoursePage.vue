<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import type { CourseCard, Lesson } from '@/constants/data'
import { useDashboardData } from '@/composables/useDashboardData'
import { useProfileData } from '@/composables/useProfileData'
import { useI18n } from '@/composables/useI18n'
import { useAuth } from '@/composables/useAuth'
import { useNotifications } from '@/composables/useNotifications'
import IconPlus from '@/icons/IconPlus.vue'
import IconClose from '@/icons/IconClose.vue'
import IconLock from '@/icons/IconLock.vue'
import IconCheck from '@/icons/IconCheck.vue'
import IconArrowLeft from '@/icons/IconArrowLeft.vue'
import IconAlert from '@/icons/IconAlert.vue'
import { fetchCourseById } from '@/services/dataService'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const { dashboardData, isDashboardLoading, loadDashboardData } = useDashboardData()
const { profileData, loadProfileData, enrollInCourse, leaveCourse } = useProfileData()
const { t, currentLanguage } = useI18n()
const { user } = useAuth()
const { notify } = useNotifications()

const courseId = computed(() => route.params.courseId as string)
const selectedCourseDetails = ref<CourseCard | null>(null)
const isDetailsLoading = ref(false)

const loadCourseDetails = async () => {
  isDetailsLoading.value = true
  try {
    const details = await fetchCourseById(courseId.value, currentLanguage.value)
    if (details) {
      selectedCourseDetails.value = details
    }
  } catch (err) {
    console.error('Error loading course details:', err)
  } finally {
    isDetailsLoading.value = false
  }
}

onMounted(() => {
  loadDashboardData()
  loadProfileData()
  loadCourseDetails()
})

watch(currentLanguage, () => {
  loadCourseDetails()
})

const availableNavLinks = computed(() => dashboardData.value?.navLinks ?? [])
const courseList = computed<CourseCard[]>(() => dashboardData.value?.courses ?? [])
const userCourseProgress = computed(() => profileData.value?.courseProgress.find((progress) => progress.courseId === courseId.value))

const selectedCourse = computed<CourseCard | undefined>(() => {
  return selectedCourseDetails.value || courseList.value.find((course) => course.id === courseId.value)
})

const isGuestCourse = computed(() => {
  return selectedCourse.value?.title === 'Darmowy kurs dla gości' || selectedCourse.value?.title === 'Free guest course'
})

const courseLessons = computed<Lesson[]>(() => {
  const lessons = selectedCourse.value?.lessons ?? []

  if (isGuestCourse.value) {
    return lessons.map((lesson) => ({
      ...lesson,
      isCompleted: false,
      isLocked: false,
    }))
  }

  const progress = userCourseProgress.value

  if (!progress) {
    return lessons.map((lesson) => ({
      ...lesson,
      isCompleted: false,
      isLocked: true,
    }))
  }

  return lessons.map((lesson, index) => {
    const lessonIdStr = lesson.id.toString()
    const isCompleted = progress.completedLessonIds.includes(lessonIdStr)

    const isUnlocked = index === 0 || isCompleted || (index > 0 && progress.completedLessonIds.includes(lessons[index - 1].id.toString()))

    return {
      ...lesson,
      isCompleted,
      isLocked: !isUnlocked,
    }
  })
})

const completedLessonsCount = computed(() => {
  if (courseLessons.value.length === 0) {
    return 0
  }

  return courseLessons.value.filter((lesson) => lesson.isCompleted).length
})

const progressPercent = computed(() => {
  if (courseLessons.value.length === 0) {
    return 0
  }

  return Math.round((completedLessonsCount.value / courseLessons.value.length) * 100)
})

type LessonStatus = 'completed' | 'locked' | 'available' | 'in-progress'

const lessonStatusLabelMap = computed<Record<LessonStatus, string>>(() => ({
  completed: t('lessonStatus.completed'),
  locked: t('lessonStatus.locked'),
  available: t('lessonStatus.available'),
  'in-progress': t('lessonStatus.inProgress'),
}))

const lessonStatusClassMap: Record<LessonStatus, string> = {
  completed: 'bg-[#1f8a70]/30 text-[#60f5d2]',
  locked: 'bg-[#3a1d32] text-[#ffb4c8]',
  available: 'bg-[#1e2b3f] text-[#7fd1ff]',
  'in-progress': 'bg-[#312440] text-[#e5c1ff]',
}

const isJoined = ref(false)
const isLeaveDialogOpen = ref(false)
const leaveDialogConfirmRef = ref<HTMLButtonElement | null>(null)

const isCourseCompleted = computed(() => {
  if (!isJoined.value) {
    return false
  }

  if (courseLessons.value.length === 0) {
    return false
  }

  return progressPercent.value === 100
})

watch(
  [userCourseProgress, isGuestCourse],
  ([progress, isGuest]) => {
    if (isGuest) {
      isJoined.value = true
    } else {
      isJoined.value = !!progress
    }
  },
  { immediate: true },
)

const levelToNumber = (level: string): number => {
  switch (level?.toLowerCase()) {
    case 'junior':
      return 1
    case 'mid':
      return 2
    case 'senior':
      return 3
    default:
      return 1
  }
}

const isCourseLevelHigher = computed(() => {
  const courseLevel = selectedCourse.value?.level?.toLowerCase() ?? 'junior'
  const userLevel = (profileData.value?.summary?.level || user.value?.level || 'junior').toLowerCase()

  return levelToNumber(courseLevel) > levelToNumber(userLevel)
})

const handleJoinCourse = async () => {
  if (isJoined.value) {
    return
  }

  if (isCourseLevelHigher.value) {
    notify('error', 'Masz niewystarczający poziom konta, aby zapisać się na ten kurs')
    return
  }

  try {
    await enrollInCourse(courseId.value, {
      title: selectedCourse.value?.title || '',
      image_path: selectedCourse.value?.image_path,
      level: selectedCourse.value?.level,
      totalLessons: courseLessons.value.length,
    })
  } catch {
    // ignore
  }
}

const handleOpenLeaveDialog = () => {
  if (!isJoined.value || isCourseCompleted.value) {
    return
  }

  isLeaveDialogOpen.value = true
}

const handleCloseLeaveDialog = () => {
  if (!isLeaveDialogOpen.value) {
    return
  }

  isLeaveDialogOpen.value = false
}

const handleConfirmLeaveCourse = async () => {
  if (!isJoined.value || isCourseCompleted.value) {
    handleCloseLeaveDialog()
    return
  }

  try {
    await leaveCourse(courseId.value)
    // isJoined will be updated automatically by the watcher on userCourseProgress
  } catch {
    // ignore
  } finally {
    isLeaveDialogOpen.value = false
  }
}

const handleBackToCourses = () => {
  router.push({ name: 'main' })
}

const getLessonStatus = (lesson: Lesson): LessonStatus => {
  if (lesson.isLocked) {
    return 'locked'
  }

  if (lesson.isCompleted) {
    return 'completed'
  }

  if (!isJoined.value) {
    return 'available'
  }

  return 'in-progress'
}

const handleLessonSelect = (lesson: Lesson) => {
  if (lesson.isLocked) {
    return
  }

  if (!isJoined.value) {
    return
  }

  router.push({
    name: 'lesson',
    params: {
      courseId: courseId.value,
      lessonOrder: lesson.order,
    },
  })
}

watch(isLeaveDialogOpen, async (isOpen) => {
  if (!isOpen) {
    return
  }

  await nextTick()
  leaveDialogConfirmRef.value?.focus()
})
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="availableNavLinks" />

    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-8 px-6 py-10">
      <button class="inline-flex w-max items-center gap-2 rounded-full border border-strokePrimary/40 px-5 py-2 text-sm text-textSecondary transition hover:border-textPrimary hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('course.backToCourses')" tabindex="0" @click="handleBackToCourses">
        <IconArrowLeft class="h-4 w-4" />
        {{ t('course.backToCourses') }}
      </button>

      <section v-if="selectedCourse" class="space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-6 shadow-2xl shadow-black/20">
        <div class="space-y-4">
          <p class="text-xs uppercase tracking-[0.3em] text-textSecondary">
            {{ t('course.label') }}
          </p>
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
              <div class="flex items-center gap-4">
                <div v-if="selectedCourse.image_path" class="flex h-16 w-16 items-center justify-center rounded-2xl bg-cardDarker p-3 shadow-lg">
                  <img :src="selectedCourse.image_path" :alt="selectedCourse.title" class="h-full w-full object-contain" />
                </div>
                <h1 class="text-3xl font-bold text-textWhite">{{ selectedCourse.title }}</h1>
              </div>
              <p class="text-sm leading-relaxed text-textSecondary lg:max-w-2xl">
                {{ selectedCourse.description }}
              </p>
              <div class="flex flex-wrap gap-2">
                <span v-for="tag in selectedCourse.tags || []" :key="tag" class="rounded-full border border-textPrimary/30 bg-cardDarker px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-textPrimary">
                  {{ tag }}
                </span>
              </div>
            </div>

            <div class="w-full max-w-sm space-y-4 rounded-2xl border border-strokePrimary/30 bg-cardDarker p-5">
              <div class="flex items-center justify-between">
                <div class="flex flex-col text-sm text-textSecondary">
                  <span>PD</span>
                  <strong class="text-lg text-textWhite">{{ selectedCourse.reward }}</strong>
                </div>
                <div class="flex flex-col text-sm text-textSecondary">
                  <span>Tasks</span>
                  <strong class="text-lg text-textWhite">{{ selectedCourse.tasks }}</strong>
                </div>
                <div class="flex flex-col text-sm text-textSecondary">
                  <span>Time</span>
                  <strong class="text-lg text-textWhite">{{ selectedCourse.duration }}</strong>
                </div>
              </div>

              <div>
                <div class="mb-2 flex items-center justify-between text-xs text-textSecondary">
                  <span>{{ t('course.lessonsCompleted') }}</span>
                  <span>{{ completedLessonsCount }}/{{ courseLessons.length }} • {{ progressPercent }}%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-cardHover">
                  <div class="h-full rounded-full bg-gradient-to-r from-textPrimary to-[#6dd5ed]" :style="{ width: `${progressPercent}%` }"></div>
                </div>
              </div>

              <div v-if="!isGuestCourse" class="space-y-3">
                <template v-if="!isJoined">
                  <button class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('course.joinCourse')" tabindex="0" @click="handleJoinCourse">
                    <IconPlus class="h-4 w-4" />
                    {{ t('course.joinCourse') }}
                  </button>
                </template>

                <template v-else-if="isCourseCompleted">
                  <div class="rounded-2xl border border-emerald-400/30 bg-[#102421] px-4 py-3 text-sm text-emerald-200">
                    {{ t('course.completed') }}
                  </div>
                  <button class="flex w-full items-center justify-center gap-2 rounded-full border border-emerald-300/60 bg-transparent px-6 py-3 text-sm font-semibold text-emerald-200" :aria-label="t('course.completed')" tabindex="0" disabled>
                    <IconCheck class="h-4 w-4" />
                    {{ t('course.completed') }}
                  </button>
                </template>
                <template v-else>
                  <div class="rounded-2xl border border-textPrimary/20 bg-card px-4 py-3 text-sm text-textSecondary">
                    {{ t('course.alreadyJoined') }}
                  </div>
                  <button class="flex w-full items-center justify-center gap-2 rounded-full border border-textPrimary/60 px-6 py-3 text-sm font-semibold text-textPrimary transition hover:bg-textPrimary/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('course.leaveCourse')" tabindex="0" @click="handleOpenLeaveDialog">
                    <IconClose class="h-4 w-4" />
                    {{ t('course.leaveCourse') }}
                  </button>
                </template>
              </div>

              <div v-else class="rounded-2xl border border-textPrimary/20 bg-card px-4 py-3 text-sm text-textSecondary text-center">
                {{ t('course.guestAccess') }}
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-textWhite">{{ t('course.lessons') }}</h2>
          </div>

          <ul class="space-y-4">
            <li v-for="(lesson, index) in courseLessons" :key="lesson.id" class="flex flex-col gap-4 rounded-2xl border border-strokePrimary/30 bg-dialogBg p-4 transition hover:border-textPrimary/50 focus-within:border-textPrimary/80" :class="{ 'cursor-pointer hover:bg-cardHover': !lesson.isLocked && isJoined }" @click="handleLessonSelect(lesson)">
              <div class="flex items-center justify-between gap-4">
                <div class="space-y-1">
                  <p class="text-sm font-semibold text-textWhite">
                    <span class="text-textSecondary mr-1">Lekcja {{ lesson.order ?? index }}:</span>
                    {{ lesson.title }}
                  </p>
                </div>
                <div class="flex items-center gap-3">
                  <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="lessonStatusClassMap[getLessonStatus(lesson)]">
                    {{ lessonStatusLabelMap[getLessonStatus(lesson)] }}
                  </span>
                  <span v-if="lesson.isLocked" class="flex h-9 w-9 items-center justify-center rounded-full border border-strokePrimary/40 text-textSecondary" :aria-label="t('course.lessonLocked')" tabindex="0">
                    <IconLock class="h-4 w-4" />
                  </span>
                  <span v-else class="flex h-9 w-9 items-center justify-center rounded-full border border-textPrimary/40 text-textPrimary" :aria-label="t('course.startLesson')" tabindex="0">
                    <IconCheck class="h-4 w-4" />
                  </span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </section>

      <section v-else class="flex flex-col items-center justify-center gap-6 rounded-2xl border border-strokePrimary/30 bg-card p-12 text-center">
        <template v-if="isDashboardLoading">
          <h1 class="text-2xl font-bold">{{ t('common.loading') }}</h1>
          <p class="text-sm text-textSecondary">
            {{ t('common.loading') }}
          </p>
        </template>
        <template v-else>
          <h1 class="text-2xl font-bold">{{ t('common.error') }}</h1>
          <p class="text-sm text-textSecondary">
            {{ t('common.error') }}
          </p>
          <button class="rounded-full border border-textPrimary px-6 py-3 text-sm font-semibold text-textPrimary transition hover:bg-textPrimary hover:text-bgPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('course.backToCourses')" tabindex="0" @click="handleBackToCourses">
            {{ t('course.backToCourses') }}
          </button>
        </template>
      </section>
    </main>
  </div>

  <div v-if="isLeaveDialogOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-8" role="dialog" aria-modal="true" aria-labelledby="leave-course-title" aria-describedby="leave-course-description" tabindex="0" @keydown.esc.prevent="handleCloseLeaveDialog" @click.self="handleCloseLeaveDialog">
    <div class="relative w-full max-w-md space-y-5 rounded-3xl border border-strokePrimary/40 bg-dialogBg p-6 shadow-2xl shadow-black/40" @click.stop>
      <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#3a1a24] text-[#ffb4c8]">
          <IconAlert class="h-5 w-5" />
        </span>
        <div>
          <h3 id="leave-course-title" class="text-lg font-semibold text-textWhite">
            {{ t('leaveDialog.title') }}
          </h3>
          <p id="leave-course-description" class="text-sm text-textSecondary">
            {{ t('leaveDialog.description') }}
          </p>
        </div>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row">
        <button class="flex flex-1 items-center justify-center gap-2 rounded-full border border-textSecondary/40 px-4 py-2.5 text-sm font-semibold text-textSecondary transition hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('leaveDialog.cancel')" tabindex="0" @click="handleCloseLeaveDialog">
          {{ t('leaveDialog.cancel') }}
        </button>
        <button ref="leaveDialogConfirmRef" class="flex flex-1 items-center justify-center gap-2 rounded-full bg-[#ff4d6d] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#ff4d6d]/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0c1322]" :aria-label="t('leaveDialog.confirm')" tabindex="0" @click="handleConfirmLeaveCourse">
          {{ t('leaveDialog.confirm') }}
        </button>
      </div>
    </div>
  </div>
</template>
