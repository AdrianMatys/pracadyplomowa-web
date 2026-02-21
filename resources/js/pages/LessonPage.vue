<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import CodeEditor from '@/components/CodeEditor.vue'
import LivePreview from '@/components/LivePreview.vue'
import ConsoleOutput from '@/components/ConsoleOutput.vue'
import HintCharacter from '@/components/HintCharacter.vue'
import type { CourseCard } from '@/constants/data'
import { useDashboardData } from '@/composables/useDashboardData'
import { useProfileData } from '@/composables/useProfileData'
import { useI18n } from '@/composables/useI18n'
import { fetchCourseById } from '@/services/dataService'
import api from '@/services/api'
import { useAuth } from '@/composables/useAuth'
import { createSubmission, getSubmission } from '@/services/judge0'
import { formatError } from '@/utils/formatters'
import IconArrowLeft from '@/icons/IconArrowLeft.vue'
import IconArrowRight from '@/icons/IconArrowRight.vue'
import IconCheck from '@/icons/IconCheck.vue'
import IconLock from '@/icons/IconLock.vue'
import IconSpinner from '@/icons/IconSpinner.vue'

const route = useRoute()
const router = useRouter()
const { dashboardData, isDashboardLoading, loadDashboardData } = useDashboardData()
const { profileData, loadProfileData, markLessonAsCompleted } = useProfileData()
const { t, currentLanguage } = useI18n()
const { isLoggedIn } = useAuth()

const courseId = computed(() => route.params.courseId as string)
const currentLessonOrder = computed(() => Number(route.params.lessonOrder))
const fetchedCourseDetails = ref<CourseCard | null>(null)

const userCode = ref('')
const validationError = ref<string | null>(null)
const isChecking = ref(false)
const executionOutput = ref('')
const isExecutionError = ref(false)

const isSolutionCorrect = ref(false)
const failedAttempts = ref(0)
const guestCompletedLessonIds = ref<string[]>([])

const loadCourseDetails = async () => {
  try {
    const details = await fetchCourseById(courseId.value, currentLanguage.value)
    if (details) {
      fetchedCourseDetails.value = details
    }
  } catch (err) {
    console.error('Error loading course details on LessonPage:', err)
  }
}

onMounted(async () => {
  loadDashboardData()
  loadProfileData()
  await loadCourseDetails()
})

const currentCourse = computed(() => {
  return fetchedCourseDetails.value || dashboardData.value?.courses.find((c) => c.id === courseId.value)
})

watch(currentLanguage, () => {
  loadCourseDetails()
})

const isInformational = computed(() => currentLesson.value?.order === 0)

const characterState = computed<'idle' | 'checking' | 'hint'>(() => {
  if (isChecking.value) return 'checking'

  if (failedAttempts.value >= 5) return 'hint'
  return 'idle'
})

const navLinksList = computed(() => dashboardData.value?.navLinks ?? [])

const userCourseProgress = computed(() => profileData.value?.courseProgress.find((progress) => progress.courseId === courseId.value))

const processedLessons = computed(() => {
  const lessons = currentCourse.value?.lessons ?? []
  const progress = userCourseProgress.value

  if (!progress) {
    return lessons.map((lesson) => {
      const isCompleted = guestCompletedLessonIds.value.includes(lesson.id.toString())
      return {
        ...lesson,
        status: isCompleted ? 'completed' : 'available',
        isCompleted,
        isActive: lesson.order === currentLessonOrder.value,
      }
    })
  }

  return lessons.map((lesson, index) => {
    const lessonIdStr = lesson.id.toString()
    const isCompleted = progress.completedLessonIds.includes(lessonIdStr)

    const isUnlocked = index === 0 || isCompleted || (index > 0 && progress.completedLessonIds.includes(lessons[index - 1].id.toString()))

    let statusValue = 'locked'
    if (isCompleted) statusValue = 'completed'
    else if (isUnlocked) statusValue = 'available'

    return {
      ...lesson,
      status: statusValue,
      isCompleted,
      isActive: lesson.order === currentLessonOrder.value,
    }
  })
})

const currentLesson = computed(() => processedLessons.value.find((l) => l.order === currentLessonOrder.value))

watch(
  currentLesson,
  (newLesson) => {
    validationError.value = null
    executionOutput.value = ''
    isExecutionError.value = false
    isChecking.value = false
    failedAttempts.value = 0

    isSolutionCorrect.value = newLesson?.isCompleted || false

    const savedCode = userCourseProgress.value?.savedCode?.[newLesson?.id.toString() ?? '']

    if (savedCode) {
      userCode.value = savedCode
    } else if (newLesson?.initialCode) {
      userCode.value = newLesson.initialCode
    } else {
      userCode.value = ''
    }
  },
  { immediate: true },
)

const nextLesson = computed(() => {
  const index = processedLessons.value.findIndex((l) => l.order === currentLessonOrder.value)
  if (index === -1 || index === processedLessons.value.length - 1) return null
  return processedLessons.value[index + 1]
})

const prevLesson = computed(() => {
  const index = processedLessons.value.findIndex((l) => l.order === currentLessonOrder.value)
  if (index <= 0) return null
  return processedLessons.value[index - 1]
})

const handleLessonChange = (targetLessonOrder: string | number) => {
  const targetOrder = Number(targetLessonOrder)
  const target = processedLessons.value.find((l) => l.order === targetOrder)
  if (target?.status === 'locked') return

  router.push({
    name: 'lesson',
    params: { courseId: courseId.value, lessonOrder: targetOrder },
  })
}

const pollSubmission = async (token: string): Promise<void> => {
  const maxRetries = 10
  let retries = 0

  while (retries < maxRetries) {
    try {
      const result = await getSubmission(token)

      if (result.status.id <= 2) {
        await new Promise((resolve) => setTimeout(resolve, 1000))
        retries++
        continue
      }

      if (result.status.id === 3) {
        executionOutput.value = result.stdout || t('lesson.noOutput')
        isExecutionError.value = false

        if (currentLesson.value?.expectedOutput) {
          if (result.stdout?.trim() === currentLesson.value.expectedOutput.trim()) {
            validationError.value = null
            isSolutionCorrect.value = true
          } else {
            validationError.value = `Expected: "${currentLesson.value.expectedOutput.trim()}", got: "${result.stdout?.trim()}"`
            isExecutionError.value = true
            failedAttempts.value++
          }
        } else {
          isSolutionCorrect.value = true
        }
      } else {
        executionOutput.value = formatError(result.stderr || result.compile_output || result.message || t('lesson.executionError'), t)
        isExecutionError.value = true
        validationError.value = t('lesson.executionError')
        failedAttempts.value++
      }
      return
    } catch (e) {
      console.error(e)
      executionOutput.value = t('lesson.apiError')
      isExecutionError.value = true
      validationError.value = t('lesson.connectionError')
      return
    }
  }

  executionOutput.value = t('lesson.timeoutError')
  isExecutionError.value = true
}

const executeLocalCode = async (code: string): Promise<{ stdout: string; error: string | null }> => {
  return new Promise((resolve) => {
    const logs: string[] = []
    const originalLog = console.log

    console.log = (...args) => {
      logs.push(args.map((arg) => (typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg))).join(' '))
    }

    try {
      const wrappedCode = `
        try {
          ${code}
        } catch (err) {
          throw err;
        }
      `

      new Function(wrappedCode)()

      resolve({ stdout: logs.join('\n'), error: null })
    } catch (err: any) {
      resolve({ stdout: logs.join('\n'), error: err.message || String(err) })
    } finally {
      console.log = originalLog
    }
  })
}

const normalizeOutput = (str: string) => {
  return str.toLowerCase().replace(/\s+/g, ' ').trim()
}

const checkSolution = async () => {
  validationError.value = null
  isExecutionError.value = false
  isSolutionCorrect.value = false

  if (currentLesson.value?.judge0LanguageId) {
    isChecking.value = true
    executionOutput.value = ''

    try {
      if (currentLesson.value.judge0LanguageId === 63) {
        const sourceCode = userCode.value + '\n' + (currentLesson.value.testCode || '')
        const result = await executeLocalCode(sourceCode)

        executionOutput.value = result.stdout || t('lesson.noOutput')

        if (result.error) {
          isExecutionError.value = true
          executionOutput.value += `\n\nError: ${result.error}`
          validationError.value = t('lesson.executionError')
          failedAttempts.value++
        } else if (currentLesson.value.expectedOutput) {
          const normalizedExpected = normalizeOutput(currentLesson.value.expectedOutput)
          const normalizedActual = normalizeOutput(result.stdout)

          if (normalizedActual.includes(normalizedExpected) || normalizedActual === normalizedExpected) {
            isSolutionCorrect.value = true
            validationError.value = null

            if (courseId.value && currentLesson.value && userCode.value) {
              markLessonAsCompleted(courseId.value, currentLesson.value.id.toString(), undefined, userCode.value)
            }
          } else {
            validationError.value = `Oczekiwano wyniku: "${currentLesson.value.expectedOutput.trim()}"`
            isExecutionError.value = true
            failedAttempts.value++
          }
        } else {
          isSolutionCorrect.value = true

          if (courseId.value && currentLesson.value && userCode.value) {
            if (userCourseProgress.value) {
              markLessonAsCompleted(courseId.value, currentLesson.value.id.toString(), undefined, userCode.value)
            } else {
              if (!guestCompletedLessonIds.value.includes(currentLesson.value.id.toString())) {
                guestCompletedLessonIds.value.push(currentLesson.value.id.toString())
              }
            }
          }
        }
      } else {
        const sourceCode = userCode.value + '\n' + (currentLesson.value.testCode || '')
        const token = await createSubmission({
          source_code: sourceCode,
          language_id: currentLesson.value.judge0LanguageId,
          expected_output: currentLesson.value.expectedOutput,
        })
        await pollSubmission(token)

        if (isSolutionCorrect.value) {
          if (courseId.value && currentLesson.value && userCode.value) {
            if (userCourseProgress.value) {
              markLessonAsCompleted(courseId.value, currentLesson.value.id.toString(), undefined, userCode.value)
            } else {
              if (!guestCompletedLessonIds.value.includes(currentLesson.value.id.toString())) {
                guestCompletedLessonIds.value.push(currentLesson.value.id.toString())
              }
            }
          }
        }
      }
    } catch (e) {
      console.error(e)
      validationError.value = t('lesson.submitError')
    } finally {
      isChecking.value = false
    }
    return
  }

  if (currentLesson.value?.validationRegex) {
    let pattern = currentLesson.value.validationRegex
    let flags = ''
    const delimiterMatch = pattern.match(/^\/(.+)\/([gimsuy]*)$/)
    if (delimiterMatch) {
      pattern = delimiterMatch[1]
      flags = delimiterMatch[2]
    }
    const regex = new RegExp(pattern, flags)
    if (!regex.test(userCode.value)) {
      validationError.value = t('lesson.incorrectSolution')
      failedAttempts.value++
      return
    }
  }

  isSolutionCorrect.value = true

  if (courseId.value && currentLesson.value && userCode.value) {
    if (isLoggedIn.value && currentLesson.value.exerciseId) {
      api.post(`/api/exercises/${currentLesson.value.exerciseId}/submit`, {
        code: userCode.value,
      }).catch(() => {})
    }

    if (userCourseProgress.value) {
      markLessonAsCompleted(courseId.value, currentLesson.value.id.toString(), undefined, userCode.value)
    } else {
      if (!guestCompletedLessonIds.value.includes(currentLesson.value.id.toString())) {
        guestCompletedLessonIds.value.push(currentLesson.value.id.toString())
      }
    }
  }
}

const handleNextLesson = () => {
  if (!currentLesson.value) return

  if (userCourseProgress.value) {
    markLessonAsCompleted(courseId.value, currentLesson.value.id.toString(), nextLesson.value?.id.toString(), userCode.value)
  } else {
    if (!guestCompletedLessonIds.value.includes(currentLesson.value.id.toString())) {
      guestCompletedLessonIds.value.push(currentLesson.value.id.toString())
    }
  }

  if (nextLesson.value) {
    router.push({
      name: 'lesson',
      params: { courseId: courseId.value, lessonOrder: nextLesson.value.order },
    })
  } else {
    router.push({ name: 'course-detail', params: { courseId: courseId.value } })
  }
}

const handleBackToCourse = () => {
  router.push({ name: 'course-detail', params: { courseId: courseId.value } })
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'completed':
      return 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
    case 'current':
      return 'bg-textPrimary/20 text-textPrimary border-textPrimary/50'
    case 'available':
      return 'bg-cardHover text-textWhite border-strokePrimary/30 hover:border-textPrimary/50'
    default:
      return 'bg-cardDarker text-textSecondary border-transparent opacity-50'
  }
}

const parsedContentParts = computed(() => {
  const content = currentLesson.value?.taskContent || currentLesson.value?.content || t('lesson.noTaskDescription') || ''
  const parts = []
  const regex = /```(\w+)?\n([\s\S]*?)```/g

  let lastIndex = 0
  let match

  while ((match = regex.exec(content)) !== null) {
    if (match.index > lastIndex) {
      parts.push({
        type: 'text',
        content: content.slice(lastIndex, match.index),
      })
    }

    parts.push({
      type: 'code',
      language: match[1] || 'text',
      content: match[2].trim(),
    })

    lastIndex = regex.lastIndex
  }

  if (lastIndex < content.length) {
    parts.push({
      type: 'text',
      content: content.slice(lastIndex),
    })
  }

  return parts
})
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />

    <main class="mx-auto grid w-full max-w-[1600px] flex-1 grid-cols-1 gap-6 px-6 py-8 lg:grid-cols-[350px_1fr]">
      <aside class="flex flex-col gap-6 lg:order-first">
        <div class="rounded-2xl border border-strokePrimary/30 bg-card p-6">
          <div class="mb-6">
            <button class="mb-4 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-textSecondary transition hover:text-textPrimary" @click="handleBackToCourse">
              <IconArrowLeft class="h-4 w-4" />
              {{ t('lesson.backToCourse') }}
            </button>
            <h2 class="text-lg font-bold text-textWhite">{{ currentCourse?.title }}</h2>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-cardHover">
              <div class="h-full bg-textPrimary" style="width: 45%"></div>
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <button
              v-for="(lesson, index) in processedLessons"
              :key="lesson.id"
              class="flex w-full items-center justify-between rounded-xl border p-3 text-left transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary"
              :class="[getStatusColor(lesson.status), lesson.isActive ? 'ring-2 ring-textPrimary border-textPrimary/50 shadow-[0_0_15px_rgba(59,130,246,0.2)]' : '']"
              :disabled="lesson.status === 'locked'"
              @click="handleLessonChange(lesson.order ?? index)"
            >
              <div class="flex items-center gap-3">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold" :class="lesson.status === 'completed' ? 'bg-emerald-500 text-bgPrimary' : 'bg-cardHover'">
                  <IconCheck v-if="lesson.status === 'completed'" class="h-3.5 w-3.5" />
                  <span v-else>{{ lesson.order ?? index }}</span>
                </div>
                <span class="text-sm font-medium line-clamp-1">{{ lesson.title }}</span>
              </div>
              <IconLock v-if="lesson.status === 'locked'" class="h-4 w-4 shrink-0 opacity-50" />
            </button>
          </div>
        </div>
      </aside>

      <section class="flex flex-col gap-6">
        <div v-if="currentLesson" class="flex flex-col gap-6 rounded-2xl border border-strokePrimary/30 bg-card p-6 shadow-2xl shadow-black/20 h-full">
          <div v-if="isInformational" class="flex flex-col h-full relative">
            <div class="mb-8 space-y-6">
              <div class="flex items-start justify-between gap-4 border-b border-strokePrimary/20 pb-6">
                <div>
                  <h1 class="text-3xl font-bold text-textWhite mb-2">{{ currentLesson.title }}</h1>
                  <p class="text-textSecondary">
                    {{ t('lesson.informational') || 'Lesson Informational' }}
                  </p>
                </div>
              </div>

              <div class="prose prose-invert max-w-none text-textSecondary text-lg leading-relaxed">
                <div v-for="(part, i) in parsedContentParts" :key="i">
                  <p v-if="part.type === 'text'" class="whitespace-pre-line mb-4">
                    {{ part.content }}
                  </p>
                  <div v-else class="my-6 rounded-lg bg-[#111111] border border-strokePrimary/30 overflow-hidden shadow-lg">
                    <div class="flex items-center justify-between px-4 py-2 bg-[#1a1a1a] border-b border-strokePrimary/20">
                      <span class="text-xs font-mono text-blue-400 font-bold uppercase">{{ part.language }}</span>
                      <div class="flex gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500/20"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/20"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500/20"></div>
                      </div>
                    </div>
                    <pre class="p-4 overflow-x-auto text-sm font-mono text-gray-300 leading-relaxed"><code>{{ part.content }}</code></pre>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-auto flex justify-end border-t border-strokePrimary/20 pt-8">
              <div class="flex items-center gap-4">
                <button class="flex items-center gap-2 rounded-full border border-strokePrimary/40 px-6 py-3 text-sm font-semibold text-textSecondary transition hover:bg-cardHover hover:text-textWhite disabled:opacity-50" :disabled="!prevLesson" @click="prevLesson && handleLessonChange(prevLesson.order ?? 0)">
                  <IconArrowLeft class="h-4 w-4" />
                  {{ t('lesson.previous') }}
                </button>

                <button class="flex items-center gap-2 rounded-full bg-emerald-600 px-8 py-3 text-base font-bold text-white transition hover:bg-emerald-700 hover:scale-105 active:scale-95 shadow-lg shadow-emerald-600/20" @click="handleNextLesson">
                  {{ t('lesson.next') }}
                  <IconArrowRight class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>

          <div v-else class="flex flex-col h-full gap-6">
            <div class="mb-4 space-y-4">
              <div class="flex items-start justify-between gap-4">
                <h1 class="text-2xl font-bold text-textWhite">{{ currentLesson.title }}</h1>
              </div>
              <div class="prose prose-invert max-w-none text-textSecondary">
                <div v-for="(part, i) in parsedContentParts" :key="i">
                  <p v-if="part.type === 'text'" class="whitespace-pre-line mb-4">
                    {{ part.content }}
                  </p>
                  <div v-else class="my-4 rounded-lg bg-[#111111] border border-strokePrimary/30 overflow-hidden">
                    <div class="px-4 py-2 bg-[#1a1a1a] text-xs text-blue-400 font-mono border-b border-strokePrimary/20 uppercase font-bold">
                      {{ part.language }}
                    </div>
                    <pre class="p-4 overflow-x-auto text-sm font-mono text-gray-300"><code>{{ part.content }}</code></pre>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <h2 class="text-lg font-bold text-textWhite">{{ t('lesson.editorLabel') }}</h2>

              <div class="flex items-center gap-4">
                <button v-if="!isSolutionCorrect" class="flex items-center gap-2 rounded-full bg-blue-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:opacity-70 disabled:cursor-not-allowed" :disabled="isChecking" @click="checkSolution">
                  <IconSpinner v-if="isChecking" class="h-4 w-4 animate-spin" />
                  <span>{{ isChecking ? t('lesson.checking') : t('lesson.check') }}</span>
                </button>
                <span v-if="validationError" class="text-xs font-semibold text-red-500 max-w-[200px] leading-tight">
                  {{ validationError }}
                </span>
                <div v-if="isSolutionCorrect" class="flex items-center gap-4">
                  <span class="text-emerald-400 font-bold flex items-center gap-2">
                    <IconCheck class="w-5 h-5" />
                    {{ t('lesson.correct') }}
                  </span>
                  <button class="flex items-center gap-2 rounded-full bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700" @click="handleNextLesson">
                    {{ t('lesson.next') }}
                    <IconArrowRight class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </div>

            <div class="grid flex-1 grid-cols-1 gap-6 lg:grid-cols-2 min-h-[500px]">
              <CodeEditor v-model="userCode" :language="currentLesson.editorLabel || (currentLesson.judge0LanguageId ? 'JS/Code' : 'CSS/HTML')" />

              <ConsoleOutput v-if="currentLesson.judge0LanguageId" :output="executionOutput" :is-error="isExecutionError" :is-loading="isChecking" />
              <LivePreview v-else :code="userCode" :preview-type="currentLesson.previewType || 'css'" />
            </div>

            <div class="flex items-center justify-between border-t border-strokePrimary/20 pt-6">
              <button class="flex items-center gap-2 rounded-full border border-strokePrimary/40 px-5 py-2.5 text-sm font-semibold text-textSecondary transition hover:bg-cardHover hover:text-textWhite disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-textSecondary" :disabled="!prevLesson" @click="prevLesson && handleLessonChange(prevLesson.order ?? 0)">
                <IconArrowLeft class="h-4 w-4" />
                {{ t('lesson.previous') }}
              </button>
            </div>
          </div>
        </div>

        <div v-else class="flex h-64 items-center justify-center rounded-2xl border border-dashed border-strokePrimary/30 text-textSecondary">
          {{ t('lesson.selectLesson') }}
        </div>
      </section>
    </main>
  </div>
  <HintCharacter v-if="currentLesson" :failed-attempts="failedAttempts" :hint1="currentLesson.hint" :hint2="currentLesson.hint2" :state="characterState" />
</template>
