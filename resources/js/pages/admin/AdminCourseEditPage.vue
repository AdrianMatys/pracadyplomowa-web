<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from '@/composables/useI18n'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const route = useRoute()
const router = useRouter()
const { notify } = useNotifications()
const { t } = useI18n()

const courseId = route.params.id

type Exercise = {
  id: number
  title: string
  description: string
  initial_code: string
  expected_output: string | null
  judge0_language_id: number | null
  validation_regex: string | null
  test_code: string | null
  hint: string
  hint_2: string | null
  preview_type: string
}

type Lesson = {
  id: number
  title: string
  description: string
  content: string
  order: number
  exercises: Exercise[]
}

type Course = {
  id: number
  title: string
  description: string
  lessons: Lesson[]
  tags: { id: number; name: string }[]
  image_path?: string
}

type Tag = {
  id: number
  name: string
}

const course = ref<Course | null>(null)
const tags = ref<Tag[]>([])
const isLoading = ref(true)

const courseForm = ref({
  title: '',
  description: '',
  duration: 0,
  reward: 0,
  tags: [] as number[],
  image: null as File | null,
})
const isSaving = ref(false)

const toggleTag = (tagId: number) => {
  const index = courseForm.value.tags.indexOf(tagId)
  if (index === -1) {
    courseForm.value.tags.push(tagId)
  } else {
    courseForm.value.tags.splice(index, 1)
  }
}

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    courseForm.value.image = target.files[0]
  }
}

const showLessonModal = ref(false)
const editingLessonId = ref<number | null>(null)
const lessonForm = ref({
  title: '',
  description: '',
  content: '',
  has_exercise: false,
  exercise_title: '',
  exercise_description: '',
  initial_code: '',
  expected_output: '',
  judge0_language_id: null as number | null,
  validation_regex: '',
  test_code: '',
  hint: '',
  hint_2: '',
  preview_type: 'none',
})
const isSavingLesson = ref(false)

const showDeleteLessonModal = ref(false)
const lessonToDelete = ref<Lesson | null>(null)

const isReordering = ref(false)
const draggedItemIndex = ref<number | null>(null)

const resetLessonForm = () => {
  lessonForm.value = {
    title: '',
    description: '',
    content: '',
    has_exercise: false,
    exercise_title: '',
    exercise_description: '',
    initial_code: '',
    expected_output: '',
    judge0_language_id: null,
    validation_regex: '',
    test_code: '',
    hint: '',
    hint_2: '',
    preview_type: 'none',
  }
}

const fetchCourse = async () => {
  isLoading.value = true
  try {
    const [courseResponse, tagsResponse] = await Promise.all([api.get(`/api/admin/courses/${courseId}`), api.get('/api/technologies')])
    course.value = courseResponse.data
    if (course.value && course.value.image_path && !course.value.image_path.startsWith('/') && !course.value.image_path.startsWith('http')) {
      course.value.image_path = '/' + course.value.image_path
    }
    tags.value = tagsResponse.data

    if (course.value && course.value.lessons) {
      course.value.lessons.sort((a, b) => (a.order || 0) - (b.order || 0))
    }

    courseForm.value = {
      title: courseResponse.data.title,
      description: courseResponse.data.description,
      duration: courseResponse.data.duration || 0,
      reward: courseResponse.data.reward || 0,
      tags: courseResponse.data.tags.map((t: Tag) => t.id),
      image: null,
    }
  } catch {
    notify('error', t('admin.courses.notifications.fetchError'))
    router.push('/admin/courses')
  } finally {
    isLoading.value = false
  }
}

const availableImages = ref<string[]>([])
const showImageModal = ref(false)
const selectedExistingImage = ref<string | null>(null)

const fetchImages = async () => {
  try {
    const response = await api.get('/api/admin/courses/images')
    availableImages.value = response.data
    showImageModal.value = true
  } catch {
    notify('error', t('admin.common.error'))
  }
}

const selectExistingImage = (image: string) => {
  selectedExistingImage.value = image
}

const confirmImageSelection = () => {
  if (selectedExistingImage.value) {
    // Set courseForm.image to null because we are using an existing path
    courseForm.value.image = null
    // Update the visual preview immediately
    if (course.value) {
      course.value.image_path = selectedExistingImage.value
    }
    // We need a way to tell the backend we chose an existing image.
    // We can use a new field 'existing_image_path' in the form data
    showImageModal.value = false
  }
}

const updateCourse = async () => {
  isSaving.value = true
  const formData = new FormData()
  formData.append('title', courseForm.value.title)
  if (courseForm.value.description) formData.append('description', courseForm.value.description)
  if (courseForm.value.duration) formData.append('duration', courseForm.value.duration.toString())
  if (courseForm.value.reward) formData.append('reward', courseForm.value.reward.toString())
  courseForm.value.tags.forEach((tagId) => formData.append('tags[]', tagId.toString()))

  if (courseForm.value.image) {
    formData.append('image', courseForm.value.image)
  } else if (selectedExistingImage.value) {
    formData.append('existing_image_path', selectedExistingImage.value)
  }

  formData.append('_method', 'PUT')

  try {
    await api.post(`/api/admin/courses/${courseId}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    notify('success', t('admin.courses.notifications.updated'))
    fetchCourse()
  } catch {
    notify('error', t('admin.courses.notifications.updateError'))
  } finally {
    isSaving.value = false
    selectedExistingImage.value = null // Reset after save
  }
}

const openAddLessonModal = () => {
  editingLessonId.value = null
  resetLessonForm()
  showLessonModal.value = true
}

const openEditLessonModal = (lesson: Lesson) => {
  editingLessonId.value = lesson.id
  const exercise = lesson.exercises?.[0]
  lessonForm.value = {
    title: lesson.title,
    description: lesson.description || '',
    content: lesson.content || '',
    has_exercise: !!exercise,
    exercise_title: exercise?.title || '',
    exercise_description: exercise?.description || '',
    initial_code: exercise?.initial_code || '',
    expected_output: exercise?.expected_output || '',
    judge0_language_id: exercise?.judge0_language_id || null,
    validation_regex: exercise?.validation_regex || '',
    test_code: exercise?.test_code || '',
    hint: exercise?.hint || '',
    hint_2: exercise?.hint_2 || '',
    preview_type: exercise?.preview_type || 'none',
  }
  showLessonModal.value = true
}

const saveLesson = async () => {
  if (!lessonForm.value.title) {
    notify('error', t('admin.courses.notifications.lessonTitleRequired'))
    return
  }

  isSavingLesson.value = true
  try {
    if (editingLessonId.value) {
      const response = await api.put(`/api/admin/lessons/${editingLessonId.value}`, lessonForm.value)

      if (course.value) {
        const index = course.value.lessons.findIndex((l) => l.id === editingLessonId.value)
        if (index !== -1) {
          course.value.lessons[index] = { ...course.value.lessons[index], ...response.data }
        }
      }
      notify('success', t('admin.courses.notifications.lessonUpdated'))
    } else {
      await api.post(`/api/admin/courses/${courseId}/lessons`, lessonForm.value)
      notify('success', t('admin.courses.notifications.lessonAdded'))
      fetchCourse()
    }
    showLessonModal.value = false
  } catch {
    notify('error', editingLessonId.value ? t('admin.courses.notifications.lessonUpdateError') : t('admin.courses.notifications.lessonAddError'))
  } finally {
    isSavingLesson.value = false
  }
}

const confirmDeleteLesson = (lesson: Lesson) => {
  lessonToDelete.value = lesson
  showDeleteLessonModal.value = true
}

const deleteLesson = async () => {
  if (!lessonToDelete.value) return

  try {
    await api.delete(`/api/admin/lessons/${lessonToDelete.value.id}`)
    notify('success', t('admin.courses.notifications.lessonDeleted'))
    if (course.value) {
      course.value.lessons = course.value.lessons.filter((l) => l.id !== lessonToDelete.value!.id)
    }
  } catch {
    notify('error', t('admin.courses.notifications.lessonDeleteError'))
  }
}

const handleDragStart = (index: number) => {
  draggedItemIndex.value = index
}

const handleDragOver = (event: DragEvent) => {
  event.preventDefault()
}

const handleDrop = (index: number) => {
  if (draggedItemIndex.value === null) return

  const newLessons = [...(course.value?.lessons || [])]
  const draggedItem = newLessons[draggedItemIndex.value]

  newLessons.splice(draggedItemIndex.value, 1)

  newLessons.splice(index, 0, draggedItem)

  if (course.value) {
    course.value.lessons = newLessons
  }
  draggedItemIndex.value = null
}

const saveOrder = async () => {
  if (!course.value) return
  isReordering.value = true

  try {
    const lessonsWithOrder = course.value.lessons.map((lesson, index) => ({
      id: lesson.id,
      order: index + 1,
    }))

    await api.post(`/api/admin/courses/${courseId}/reorder`, { lessons: lessonsWithOrder })
    notify('success', t('admin.courses.notifications.orderSaved'))
  } catch {
    notify('error', t('admin.courses.notifications.orderError'))
  } finally {
    isReordering.value = false
  }
}

const searchQuery = ref('')

const filteredLessons = computed(() => {
  if (!course.value) return []
  if (!searchQuery.value) return course.value.lessons

  const query = searchQuery.value.toLowerCase()
  return course.value.lessons.filter((lesson) => lesson.title.toLowerCase().includes(query) || lesson.description?.toLowerCase().includes(query))
})

onMounted(fetchCourse)
</script>

<template>
  <div v-if="isLoading" class="flex justify-center py-12">
    <span class="text-textSecondary">{{ t('admin.common.loading') }}</span>
  </div>

  <div v-else-if="course" class="space-y-8 max-w-4xl mx-auto">
    <div class="flex items-center gap-4">
      <RouterLink to="/admin/courses" class="p-2 rounded-lg bg-bgSecondary text-textSecondary hover:text-textWhite transition-colors"> ← {{ t('admin.common.back') }} </RouterLink>
      <h1 class="text-2xl font-bold text-textWhite">{{ t('admin.courses.editPage.title') }}: {{ course.title }}</h1>
    </div>

    <div class="bg-card border border-strokePrimary/30 rounded-xl p-6 space-y-4">
      <h2 class="text-lg font-bold text-textWhite mb-4">
        {{ t('admin.courses.editPage.details') }}
      </h2>

      <div class="grid gap-4">
        <div v-if="course.image_path" class="flex justify-center mb-4">
          <img :src="course.image_path" alt="Course Logo" class="h-32 w-32 object-cover rounded-xl border border-strokePrimary/30" />
        </div>

        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Obrazek kursu</label>
          <div class="flex gap-2">
            <input type="file" accept="image/*" class="w-full text-sm text-textSecondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/20 file:text-primary hover:file:bg-primary/30 transition-colors" @change="handleImageUpload" />
            <button type="button" class="px-3 py-2 bg-bgSecondary text-textSecondary hover:text-textWhite font-bold rounded-lg border border-strokePrimary/30 transition-colors text-xs whitespace-nowrap" @click="fetchImages">Wybierz z biblioteki</button>
          </div>
        </div>

        <!-- Image Selection Modal -->
        <div v-if="showImageModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @click.self="showImageModal = false">
          <div class="bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-4xl shadow-2xl p-6 flex flex-col max-h-[90vh]">
            <h3 class="text-xl font-bold text-textWhite mb-4">Wybierz obraz z biblioteki</h3>
            <div class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 p-2">
              <div v-for="img in availableImages" :key="img" class="relative group cursor-pointer rounded-lg overflow-hidden border-2 transition-all" :class="selectedExistingImage === img ? 'border-primary' : 'border-transparent hover:border-strokePrimary'" @click="selectExistingImage(img)">
                <img :src="img" alt="Gallery Image" class="w-full h-32 object-cover" />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                <div v-if="selectedExistingImage === img" class="absolute top-2 right-2 bg-primary text-white p-1 rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </div>
              </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-strokePrimary/20 mt-4">
              <button class="px-4 py-2 rounded-lg text-textSecondary hover:text-textWhite font-bold transition-colors" @click="showImageModal = false">Anuluj</button>
              <button class="px-6 py-2 bg-primary text-textWhite font-bold rounded-lg hover:bg-primary/90 transition-all disabled:opacity-50" :disabled="!selectedExistingImage" @click="confirmImageSelection">Wybierz</button>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.addModal.courseTitle') }}</label>
          <input v-model="courseForm.title" type="text" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
        </div>
        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.addModal.description') }}</label>
          <textarea v-model="courseForm.description" rows="4" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Czas (min)</label>
            <input v-model.number="courseForm.duration" type="number" min="0" placeholder="np. 120" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Punkty (PD)</label>
            <input v-model.number="courseForm.reward" type="number" min="0" placeholder="np. 50" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.table.tags') }}</label>
          <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-2 bg-bgSecondary rounded-lg border border-strokePrimary/30">
            <button v-for="tag in tags" :key="tag.id" :class="['px-2 py-1 rounded-md text-xs font-bold transition-colors border', courseForm.tags.includes(tag.id) ? 'bg-primary/20 text-primary border-primary' : 'bg-transparent text-textSecondary border-transparent hover:bg-white/5']" @click="toggleTag(tag.id)">
              {{ tag.name }}
            </button>
          </div>
        </div>
      </div>

      <div class="flex justify-end pt-2">
        <button :disabled="isSaving" class="px-6 py-2 bg-primary text-textWhite font-bold rounded-lg hover:bg-primary/90 transition-all disabled:opacity-50" @click="updateCourse">
          {{ isSaving ? t('admin.common.saving') : t('admin.common.save') }}
        </button>
      </div>
    </div>

    <div class="bg-card border border-strokePrimary/30 rounded-xl p-6">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
          <h2 class="text-lg font-bold text-textWhite">{{ t('admin.courses.editPage.lessons') }} ({{ course.lessons.length }})</h2>
          <input v-model="searchQuery" type="text" :placeholder="t('admin.courses.editPage.searchLessons')" class="bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-1.5 text-textWhite text-xs focus:border-primary focus:outline-none transition-colors w-48" />
        </div>
        <div class="flex gap-3">
          <button :disabled="isReordering" class="px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-500 transition-colors shadow-lg shadow-green-500/20 disabled:opacity-50" @click="saveOrder">
            {{ isReordering ? t('admin.common.saving') : t('admin.courses.editPage.saveOrder') }}
          </button>
          <button class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-500 transition-colors shadow-lg shadow-blue-500/20" @click="openAddLessonModal">+ {{ t('admin.courses.editPage.addLesson') }}</button>
        </div>
      </div>

      <div v-if="course.lessons.length === 0" class="text-center py-8 text-textSecondary text-sm">
        {{ t('admin.courses.editPage.noLessons') }}
      </div>

      <ul v-else class="space-y-2">
        <li v-for="(lesson, index) in filteredLessons" :key="lesson.id" class="flex items-center justify-between p-3 rounded-lg bg-bgPrimary border border-strokePrimary/20 hover:border-strokePrimary/50 transition-all group cursor-move" draggable="true" :class="{ 'opacity-50': draggedItemIndex === index }" @dragstart="handleDragStart(index)" @dragover="handleDragOver" @drop="handleDrop(index)">
          <div class="flex items-center gap-4">
            <span class="w-8 h-8 flex items-center justify-center bg-bgSecondary rounded-full text-xs font-mono text-textSecondary select-none">
              {{ index + 1 }}
            </span>
            <div>
              <div class="text-sm font-medium text-textWhite select-none">
                <span class="text-primary font-bold">{{ t('admin.courses.editPage.lesson') }} {{ index + 1 }}:</span>
                {{ lesson.title }}
                <span v-if="lesson.exercises && lesson.exercises.length > 0" class="ml-2 px-1.5 py-0.5 text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 rounded">Zadanie</span>
              </div>
              <div class="text-xs text-textSecondary truncate max-w-md select-none">
                {{ lesson.description }}
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="px-2 py-1 text-xs font-bold text-blue-400 bg-blue-500/10 rounded hover:bg-blue-500/20 transition-colors" @click="openEditLessonModal(lesson)">
              {{ t('admin.common.edit') }}
            </button>
            <button class="px-2 py-1 text-xs font-bold text-red-500 bg-red-500/10 rounded hover:bg-red-500/20 transition-colors" @click="confirmDeleteLesson(lesson)">
              {{ t('admin.common.delete') }}
            </button>
          </div>
        </li>
      </ul>
    </div>

    <div v-if="showLessonModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showLessonModal = false">
      <div class="bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden p-6 space-y-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-textWhite">
          {{ editingLessonId ? t('admin.courses.editPage.editLesson') : t('admin.courses.editPage.addLessonTitle') }}
        </h3>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.editPage.lessonTitle') }}</label>
            <input v-model="lessonForm.title" type="text" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
          </div>

          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.editPage.lessonDescription') }}</label>
            <textarea v-model="lessonForm.description" rows="2" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none"></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Treść lekcji (teoria / markdown)</label>
            <textarea v-model="lessonForm.content" rows="4" placeholder="Treść lekcji wyświetlana użytkownikowi. Obsługuje bloki kodu: ```javascript ... ```" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm font-mono focus:border-primary focus:outline-none"></textarea>
          </div>

          <div class="flex items-center gap-3 py-2 border-t border-strokePrimary/20">
            <label class="relative inline-flex items-center cursor-pointer">
              <input v-model="lessonForm.has_exercise" type="checkbox" class="sr-only peer" />
              <div class="w-10 h-5 bg-bgSecondary peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600 peer-checked:after:bg-white"></div>
            </label>
            <span class="text-sm font-bold text-textWhite">Lekcja zawiera zadanie do rozwiązania</span>
          </div>

          <div v-if="lessonForm.has_exercise" class="space-y-4 bg-bgSecondary/50 rounded-xl p-4 border border-emerald-500/20">
            <h4 class="text-sm font-bold text-emerald-400 uppercase tracking-wider">Konfiguracja zadania</h4>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Tytuł zadania</label>
                <input v-model="lessonForm.exercise_title" type="text" placeholder="Domyślnie: tytuł lekcji" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
              </div>

              <div>
                <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Język (Judge0 ID)</label>
                <select v-model="lessonForm.judge0_language_id" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm focus:border-primary focus:outline-none">
                  <option :value="null">Brak (walidacja regex)</option>
                  <option :value="63">JavaScript (63)</option>
                  <option :value="68">PHP (68)</option>
                  <option :value="71">Python (71)</option>
                  <option :value="62">Java (62)</option>
                  <option :value="54">C++ (54)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Opis zadania (treść polecenia)</label>
              <textarea v-model="lessonForm.exercise_description" rows="2" placeholder="Domyślnie: opis lekcji" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm focus:border-primary focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Kod początkowy</label>
              <textarea v-model="lessonForm.initial_code" rows="4" placeholder="Kod wyświetlany w edytorze na start" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm font-mono focus:border-primary focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Oczekiwany wynik (output)</label>
              <textarea v-model="lessonForm.expected_output" rows="2" placeholder="Wynik console.log / echo / print" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm font-mono focus:border-primary focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Kod testowy (dołączany do kodu użytkownika)</label>
              <textarea v-model="lessonForm.test_code" rows="3" placeholder="Np. try { console.log(myFunction()); } catch(e) { ... }" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm font-mono focus:border-primary focus:outline-none"></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Validation Regex (zamiast Judge0)</label>
              <input v-model="lessonForm.validation_regex" type="text" placeholder="np. /SELECT\s+name\s+FROM\s+products/i" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm font-mono focus:border-primary focus:outline-none" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Podpowiedź 1</label>
                <input v-model="lessonForm.hint" type="text" placeholder="Wyświetlana po 5 błędach" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
              </div>

              <div>
                <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Podpowiedź 2</label>
                <input v-model="lessonForm.hint_2" type="text" placeholder="Dodatkowa podpowiedź" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Typ podglądu</label>
              <select v-model="lessonForm.preview_type" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-3 py-2 text-textWhite text-sm focus:border-primary focus:outline-none">
                <option value="none">Brak (konsola)</option>
                <option value="css">CSS Preview</option>
                <option value="html">HTML Preview</option>
              </select>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button class="px-4 py-2 rounded-lg text-textSecondary hover:text-textWhite font-semibold transition-colors" @click="showLessonModal = false">
            {{ t('admin.common.cancel') }}
          </button>
          <button :disabled="isSavingLesson" class="px-4 py-2 rounded-lg bg-primary text-textWhite font-bold hover:bg-primary/90 transition-all disabled:opacity-50" @click="saveLesson">
            {{ isSavingLesson ? t('admin.common.saving') : editingLessonId ? t('admin.common.save') : t('admin.courses.editPage.addLesson') }}
          </button>
        </div>
      </div>
    </div>

    <ConfirmationModal v-model:is-open="showDeleteLessonModal" :title="t('admin.courses.deleteLessonModal.title')" :message="t('admin.courses.deleteLessonModal.message', { title: lessonToDelete?.title || '' })" :confirm-text="t('admin.common.delete')" :cancel-text="t('admin.common.cancel')" is-danger @confirm="deleteLesson" />
  </div>
</template>
