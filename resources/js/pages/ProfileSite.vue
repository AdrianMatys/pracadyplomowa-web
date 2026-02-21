<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import ProfileSummaryCard from '@/components/ProfileSummaryCard.vue'
import StatsGrid from '@/components/StatsGrid.vue'
import AchievementsList from '@/components/AchievementsList.vue'
import type { ProfileSummary, UserStat } from '@/constants/data'
import { useDashboardData } from '@/composables/useDashboardData'
import { useProfileData } from '@/composables/useProfileData'
import { useI18n } from '@/composables/useI18n'
import IconArrowRight from '@/icons/IconArrowRight.vue'

const router = useRouter()
const { dashboardData, loadDashboardData } = useDashboardData()
const { profileData, isProfileLoading, loadProfileData } = useProfileData()
const { t } = useI18n()

onMounted(() => {
  loadDashboardData()
  loadProfileData()
})

const fallbackSummary = computed<ProfileSummary>(() => ({
  nickname: t('profile.user'),
  description: t('profile.loadingProfile'),
  progress: 0,
  completed: 0,
  inProgress: 0,
  waitingTasks: 0,
}))

const handleAchievementOpen = (_id: string) => {}

const handleGoToCourse = (courseId: string) => {
  router.push({ name: 'course-detail', params: { courseId } })
}

const navLinksList = computed(() => dashboardData.value?.navLinks ?? [])
const profileSummaryData = computed(() => profileData.value?.summary ?? fallbackSummary.value)
const achievementsList = computed(() => profileData.value?.achievements ?? [])
const completedCourses = computed(() => profileData.value?.completedCourses ?? [])
const enrolledCourses = computed(() => profileData.value?.enrolledCourses ?? [])

const profileStats = computed<UserStat[]>(() => {
  const completedCoursesCount = completedCourses.value.length
  const inProgressCoursesCount = enrolledCourses.value.length

  const completedLessonsCount = completedCourses.value.reduce((sum, course) => sum + course.completedLessons, 0)
  const inProgressLessonsCount = enrolledCourses.value.reduce((sum, course) => sum + course.completedLessons, 0)
  const totalFinishedLessons = completedLessonsCount + inProgressLessonsCount

  const totalEarnedPoints = profileSummaryData.value.xp ?? 0

  return [
    {
      id: 'completed-courses',
      label: t('profile.completedCourses'),
      value: `${completedCoursesCount}`,
      helper: inProgressCoursesCount === 0 ? t('profile.coursesInProgress') + ': 0' : `${inProgressCoursesCount} ${t('profile.coursesInProgress').toLowerCase()}`,
    },
    {
      id: 'tasks',
      label: t('profile.completedLessons'),
      value: `${totalFinishedLessons}`,
      helper: `${completedLessonsCount} • ${inProgressLessonsCount}`,
    },
    {
      id: 'points',
      label: t('profile.earnedPoints'),
      value: `${totalEarnedPoints} PD`,
      helper: t('profile.basedOnCompleted'),
    },
  ]
})
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-8 px-6 py-10">
      <ProfileSummaryCard :summary="profileSummaryData" />
      <StatsGrid :stats="profileStats" />

      <section class="space-y-6 rounded-2xl border border-strokePrimary/30 bg-card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold text-textWhite">
              {{ t('profile.completedCourses') }}
            </h2>
            <p class="text-sm text-textSecondary">{{ t('profile.noCompletedCourses').split('.')[0] }}.</p>
          </div>
          <span class="rounded-full border border-textSecondary/30 px-4 py-1 text-xs uppercase tracking-[0.3em] text-textSecondary"> {{ completedCourses.length }} {{ completedCourses.length === 1 ? 'course' : 'courses' }} </span>
        </div>
        <div v-if="completedCourses.length" class="grid gap-4 md:grid-cols-2">
          <article v-for="course in completedCourses" :key="course.id" class="flex flex-col gap-4 rounded-2xl border border-strokePrimary/30 bg-cardDarker p-5">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div v-if="course.image_path" class="w-12 h-12 rounded-lg bg-card overflow-hidden shrink-0 border border-strokePrimary/20 p-1">
                  <img :src="course.image_path" class="w-full h-full object-contain" :alt="course.title" />
                </div>
                <div>
                  <p class="text-xs uppercase tracking-[0.3em] text-textSecondary">
                    {{ course.level }}
                  </p>
                  <h3 class="text-lg font-semibold text-textWhite">{{ course.title }}</h3>
                </div>
              </div>
              <span class="rounded-full border border-emerald-400/40 bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-200 whitespace-nowrap">
                {{ t('lessonStatus.completed') }}
              </span>
            </div>

            <div>
              <div class="flex items-center justify-between text-xs text-textSecondary">
                <span>{{ course.completedLessons }}/{{ course.totalLessons }}</span>
                <span>100%</span>
              </div>
              <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-cardHover">
                <div class="h-full rounded-full bg-textPrimary" :style="{ width: `${course.progress}%` }"></div>
              </div>
            </div>

            <button class="inline-flex items-center justify-center gap-2 rounded-full border border-textPrimary/60 px-4 py-2 text-sm font-semibold text-textPrimary transition hover:bg-textPrimary/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('profile.goToCourse')" tabindex="0" @click="handleGoToCourse(course.id)">
              {{ t('profile.goToCourse') }}
              <IconArrowRight class="h-4 w-4" />
            </button>
          </article>
        </div>
        <p v-else class="rounded-xl border border-dashed border-strokePrimary/40 p-4 text-sm text-textSecondary">
          {{ isProfileLoading ? t('profile.loadingCourses') : t('profile.noCompletedCourses') }}
        </p>
      </section>

      <section class="space-y-6 rounded-2xl border border-strokePrimary/30 bg-card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold text-textWhite">
              {{ t('profile.coursesInProgress') }}
            </h2>
            <p class="text-sm text-textSecondary">{{ t('profile.continueLesson') }}</p>
          </div>
          <span class="rounded-full border border-textSecondary/30 px-4 py-1 text-xs uppercase tracking-[0.3em] text-textSecondary"> {{ enrolledCourses.length }} {{ enrolledCourses.length === 1 ? 'course' : 'courses' }} </span>
        </div>
        <div v-if="enrolledCourses.length" class="grid gap-4 md:grid-cols-2">
          <article v-for="course in enrolledCourses" :key="course.id" class="flex flex-col gap-4 rounded-2xl border border-strokePrimary/30 bg-cardDarker p-5">
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div v-if="course.image_path" class="w-12 h-12 rounded-lg bg-card overflow-hidden shrink-0 border border-strokePrimary/20 p-1">
                  <img :src="course.image_path" class="w-full h-full object-contain" :alt="course.title" />
                </div>
                <div>
                  <p class="text-xs uppercase tracking-[0.3em] text-textSecondary">
                    {{ course.level }}
                  </p>
                  <h3 class="text-lg font-semibold text-textWhite">{{ course.title }}</h3>
                </div>
              </div>
              <span class="rounded-full border border-amber-300/50 bg-amber-400/15 px-3 py-1 text-xs font-semibold text-amber-200 whitespace-nowrap">
                {{ t('lessonStatus.inProgress') }}
              </span>
            </div>

            <p class="text-sm text-textSecondary">
              {{ t('profile.continueLesson') }}
              <span class="text-textPrimary font-semibold">{{ course.nextLesson ?? 'TBD' }}</span>
            </p>

            <div>
              <div class="flex items-center justify-between text-xs text-textSecondary">
                <span>{{ course.completedLessons }}/{{ course.totalLessons }}</span>
                <span>{{ course.progress }}%</span>
              </div>
              <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-cardHover">
                <div class="h-full rounded-full bg-gradient-to-r from-textPrimary to-[#6dd5ed]" :style="{ width: `${course.progress}%` }"></div>
              </div>
            </div>

            <button class="inline-flex items-center justify-center gap-2 rounded-full bg-textPrimary px-4 py-2 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('profile.goToCourse')" tabindex="0" @click="handleGoToCourse(course.id)">
              {{ t('profile.goToCourse') }}
              <IconArrowRight class="h-4 w-4" />
            </button>
          </article>
        </div>
        <p v-else class="rounded-xl border border-dashed border-strokePrimary/40 p-4 text-sm text-textSecondary">
          {{ isProfileLoading ? t('profile.loadingCourses') : t('profile.noEnrolledCourses') }}
        </p>
      </section>

      <AchievementsList :items="achievementsList" @open="handleAchievementOpen" />
    </main>
  </div>
</template>
