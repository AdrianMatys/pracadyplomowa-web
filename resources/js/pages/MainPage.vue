<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import FiltersPanel from '@/components/FiltersPanel.vue'
import WelcomeBanner from '@/components/WelcomeBanner.vue'
import CoursesGrid from '@/components/CoursesGrid.vue'
import { useDashboardData } from '@/composables/useDashboardData'
import { useI18n } from '@/composables/useI18n'
import IconArrowRight from '@/icons/IconArrowRight.vue'
import { useAuth } from '@/composables/useAuth'
import { useProfileData } from '@/composables/useProfileData'

const router = useRouter()
const selectedFilters = ref<string[]>([])
const { dashboardData, isDashboardLoading, loadDashboardData } = useDashboardData()
const { t } = useI18n()
const { isLoggedIn } = useAuth()
const { profileData, loadProfileData } = useProfileData()

const userLevel = computed(() => {
  if (!profileData.value?.summary) return 'Użytkowniku'

  return profileData.value.summary.level || 'Junior'
})

const parseNumberFromText = (text: string | number | undefined | null) => {
  if (text === undefined || text === null) return 0
  const stringVal = String(text)
  const numeric = parseInt(stringVal.replace(/[^0-9]/g, '') || '0', 10)
  return Number.isNaN(numeric) ? 0 : numeric
}

onMounted(() => {
  loadDashboardData()
  if (isLoggedIn.value) {
    loadProfileData()
  }
})

const availableNavLinks = computed(() => dashboardData.value?.navLinks ?? [])
const availableFilters = computed(() => dashboardData.value?.filterSections ?? [])
const availableCourses = computed(() => dashboardData.value?.courses ?? [])

const bannerStats = computed(() => {
  const totalCourses = availableCourses.value.length
  const totalTasks = availableCourses.value.reduce((sum, course) => sum + parseNumberFromText(course.tasks), 0)
  const totalPoints = availableCourses.value.reduce((sum, course) => sum + parseNumberFromText(course.reward), 0)

  return [
    { label: t('banner.coursesCount'), value: `${totalCourses}` },
    { label: t('banner.tasksCount'), value: `${totalTasks}` },
    { label: t('banner.pointsTotal'), value: `${totalPoints}` },
  ]
})

const visibleCourses = computed(() => {
  const coursesList = availableCourses.value
  if (selectedFilters.value.length === 0) {
    return coursesList
  }

  return coursesList.filter((course) => {
    const matchesLevel = selectedFilters.value.includes(course.level.toLowerCase())

    const courseTags = course.tags || []
    const matchesTag = courseTags.some((tag) => selectedFilters.value.includes(tag.toLowerCase()))

    return matchesLevel || matchesTag
  })
})

const handleFilterToggle = (id: string) => {
  const index = selectedFilters.value.indexOf(id)
  if (index === -1) {
    selectedFilters.value.push(id)
  } else {
    selectedFilters.value.splice(index, 1)
  }
}

const handleBannerPrimary = () => {}

const handleBannerSecondary = () => {}

const handleCourseOpen = (id: string) => {
  router.push({ name: 'course-detail', params: { courseId: id } })
}

const handleGoToNews = () => {
  router.push({ name: 'news' })
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="availableNavLinks" />
    <main class="flex w-full flex-1 flex-col gap-8 px-6 py-10 md:flex-row">
      <aside class="md:w-72 lg:w-80 flex-shrink-0 self-stretch">
        <div class="flex flex-col gap-6 md:sticky md:top-0">
          <button class="flex w-full items-center justify-center gap-2 rounded-2xl border border-textPrimary/60 px-4 py-3 text-sm font-semibold text-textPrimary transition hover:bg-textPrimary/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('main.newsAndArticles')" tabindex="0" @click="handleGoToNews">
            {{ t('main.newsAndArticles') }}
            <IconArrowRight class="h-4 w-4" />
          </button>
          <FiltersPanel v-if="availableFilters.length" :sections="availableFilters" :selected="selectedFilters" @toggle="handleFilterToggle" />
          <p v-else class="rounded-2xl border border-dashed border-strokePrimary/40 bg-[#111b2c] p-4 text-sm text-textSecondary">
            {{ isDashboardLoading ? t('main.loadingFilters') : t('main.noFilters') }}
          </p>
        </div>
      </aside>
      <section class="min-w-0 flex-1 space-y-8">
        <WelcomeBanner :stats="bannerStats" :is-logged-in="isLoggedIn" :user-level="userLevel" @primary="handleBannerPrimary" @secondary="handleBannerSecondary" @login="router.push({ name: 'login' })" />
        <CoursesGrid v-if="visibleCourses.length" :courses="visibleCourses" @open="handleCourseOpen" />
        <p v-else class="rounded-2xl border border-dashed border-strokePrimary/40 bg-[#111b2c] p-6 text-center text-sm text-textSecondary">
          {{ isDashboardLoading ? t('main.loadingCourses') : t('main.noCoursesMatch') }}
        </p>
      </section>
    </main>
  </div>
</template>
