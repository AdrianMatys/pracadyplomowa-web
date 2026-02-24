import { watch } from 'vue'
import api from '@/services/api'
import type { ProfileResponse } from '@/services/dataService'
import { fetchProfileData, completeLesson } from '@/services/dataService'
import { useI18n } from '@/composables/useI18n'
import { useAuth } from '@/composables/useAuth'
import { useAsyncData } from '@/composables/useAsyncData'

let instance: ReturnType<typeof createInstance> | null = null

function createInstance() {
  const { currentLanguage } = useI18n()
  const { refreshUser } = useAuth()
  const completingLessons = new Set<string>()

  const {
    data: profileData,
    isLoading: isProfileLoading,
    error: profileError,
    load,
    refresh,
  } = useAsyncData<ProfileResponse | null>(async () => {
    const { isLoggedIn } = useAuth()
    if (!isLoggedIn.value) return null
    return fetchProfileData(currentLanguage.value)
  })

  const loadProfileData = (forceReload = false) => load(forceReload)
  const refreshProfileData = () => refresh()

  const markLessonAsCompleted = async (courseId: string, lessonId: string, nextLessonId?: string, userCode?: string) => {
    if (!profileData.value) return

    const cacheKey = `${courseId}-${lessonId}`
    if (completingLessons.has(cacheKey)) return
    completingLessons.add(cacheKey)

    const progress = profileData.value.courseProgress.find((p) => p.courseId === courseId)
    if (progress) {
      if (!progress.completedLessonIds.includes(lessonId)) {
        progress.completedLessonIds.push(lessonId)
      }

      if (userCode) {
        progress.savedCode = { ...progress.savedCode, [lessonId]: userCode }
      }
    }

    try {
      const response = await completeLesson(courseId, lessonId, userCode)

      const { setUser } = useAuth()
      if (response && (response as any).user) {
        setUser((response as any).user)
      } else {
        await refreshUser()
      }

      await refreshProfileData()
    } catch (e) {
      console.error('Failed to save progress', e)
    } finally {
      completingLessons.delete(cacheKey)
    }
  }

  const enrollInCourse = async (courseId: string, courseDetails?: { title: string; image_path?: string; level?: string; totalLessons?: number }) => {
    if (profileData.value) {
      const course = profileData.value.courseProgress.find((p) => p.courseId === courseId)
      if (course) {
        course.status = 'enrolled'
      } else {
        profileData.value.courseProgress.push({
          courseId,
          completedLessonIds: [],
          savedCode: {},
          status: 'enrolled',
        })
        profileData.value.summary.inProgress++

        if (courseDetails) {
          profileData.value.enrolledCourses.push({
            id: courseId,
            title: courseDetails.title,
            level: courseDetails.level || 'Junior',
            image_path: courseDetails.image_path,
            progress: 0,
            completedLessons: 0,
            totalLessons: courseDetails.totalLessons || 0,
            accent: 'blue',
            status: 'enrolled',
            updatedAt: new Date().toISOString(),
            nextLesson: undefined,
          })
        }
      }
    }

    try {
      await api.post(`/api/courses/${courseId}/enroll`)
    } finally {
      refreshProfileData()
      refreshUser()
    }
  }

  const leaveCourse = async (courseId: string) => {
    if (profileData.value) {
      const index = profileData.value.courseProgress.findIndex((p) => p.courseId === courseId)
      if (index !== -1) {
        profileData.value.courseProgress.splice(index, 1)
        profileData.value.summary.inProgress = Math.max(0, profileData.value.summary.inProgress - 1)
      }

      const enrolledIndex = profileData.value.enrolledCourses.findIndex((c) => c.id === courseId)
      if (enrolledIndex !== -1) {
        profileData.value.enrolledCourses.splice(enrolledIndex, 1)
      }
    }

    try {
      await api.delete(`/api/courses/${courseId}/enroll`)
    } finally {
      refreshProfileData()
      refreshUser()
    }
  }

  watch(currentLanguage, () => {
    if (profileData.value) {
      refreshProfileData()
    }
  })

  return {
    profileData,
    isProfileLoading,
    profileError,
    loadProfileData,
    refreshProfileData,
    markLessonAsCompleted,
    enrollInCourse,
    leaveCourse,
  }
}

export const useProfileData = () => {
  if (!instance) {
    instance = createInstance()
  }
  return instance
}
