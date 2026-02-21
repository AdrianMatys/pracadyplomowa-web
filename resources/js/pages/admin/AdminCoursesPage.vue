<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from '@/composables/useI18n'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const { notify } = useNotifications()
const { t } = useI18n()

type Course = {
  id: number
  title: string
  description: string
  lessons_count: number
  tags: { id: number; name: string }[]
}

type Tag = {
  id: number
  name: string
}

const courses = ref<Course[]>([])
const tags = ref<Tag[]>([])
const isLoading = ref(true)
const searchQuery = ref('')

const filteredCourses = computed(() => {
  if (!searchQuery.value) return courses.value
  const query = searchQuery.value.toLowerCase()
  return courses.value.filter((course) => course.title.toLowerCase().includes(query))
})

const showDeleteModal = ref(false)
const courseToDelete = ref<Course | null>(null)

const showAddModal = ref(false)
const newCourse = ref({
  title: '',
  description: '',
  tags: [] as number[],
  image: null as File | null,
})
const isCreating = ref(false)

const toggleTag = (tagId: number) => {
  const index = newCourse.value.tags.indexOf(tagId)
  if (index === -1) {
    newCourse.value.tags.push(tagId)
  } else {
    newCourse.value.tags.splice(index, 1)
  }
}

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    newCourse.value.image = target.files[0]
  }
}

const fetchCourses = async () => {
  isLoading.value = true
  try {
    const [coursesResponse, tagsResponse] = await Promise.all([api.get('/api/admin/courses'), api.get('/api/technologies')])
    courses.value = coursesResponse.data
    tags.value = tagsResponse.data
  } catch {
    notify('error', t('admin.courses.notifications.fetchError'))
  } finally {
    isLoading.value = false
  }
}

const confirmDelete = (course: Course) => {
  courseToDelete.value = course
  showDeleteModal.value = true
}

const deleteCourse = async () => {
  if (!courseToDelete.value) return

  try {
    await api.delete(`/api/admin/courses/${courseToDelete.value.id}`)
    courses.value = courses.value.filter((c) => c.id !== courseToDelete.value!.id)
    notify('success', t('admin.courses.notifications.deleted'))
  } catch {
    notify('error', t('admin.courses.notifications.deleteError'))
  }
}

const createCourse = async () => {
  if (!newCourse.value.title) {
    notify('error', t('admin.courses.notifications.titleRequired'))
    return
  }

  isCreating.value = true
  try {
    const formData = new FormData()
    formData.append('title', newCourse.value.title)
    if (newCourse.value.description) formData.append('description', newCourse.value.description)
    newCourse.value.tags.forEach((tagId) => formData.append('tags[]', tagId.toString()))
    if (newCourse.value.image) formData.append('image', newCourse.value.image)

    const response = await api.post('/api/admin/courses', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    courses.value.unshift(response.data)
    notify('success', t('admin.courses.notifications.created'))
    showAddModal.value = false
    newCourse.value = { title: '', description: '', tags: [], image: null }
  } catch (error: any) {
    notify('error', error.response?.data?.message || t('admin.courses.notifications.createError'))
  } finally {
    isCreating.value = false
  }
}

onMounted(fetchCourses)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-textWhite">{{ t('admin.courses.title') }}</h1>
      <div class="flex gap-3">
        <input v-model="searchQuery" type="text" :placeholder="t('admin.courses.searchPlaceholder')" class="bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors w-64" />
        <button class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-500 transition-colors shadow-lg shadow-blue-500/20" @click="showAddModal = true">+ {{ t('admin.courses.addCourse') }}</button>
      </div>
    </div>

    <div class="rounded-xl border border-strokePrimary/30 bg-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-bgSecondary text-textSecondary uppercase text-xs">
            <tr>
              <th class="px-4 py-4 font-semibold hidden sm:table-cell">
                {{ t('admin.common.id') }}
              </th>
              <th class="px-4 py-4 font-semibold">{{ t('admin.courses.table.title') }}</th>
              <th class="px-4 py-4 font-semibold hidden md:table-cell">
                {{ t('admin.courses.table.tags') }}
              </th>
              <th class="px-4 py-4 font-semibold hidden lg:table-cell">
                {{ t('admin.courses.table.lessonsCount') }}
              </th>
              <th class="px-4 py-4 font-semibold text-right">{{ t('admin.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-strokePrimary/20 text-sm">
            <tr v-if="isLoading">
              <td colspan="5" class="px-4 py-8 text-center text-textSecondary">
                {{ t('admin.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="filteredCourses.length === 0">
              <td colspan="5" class="px-4 py-8 text-center text-textSecondary">
                {{ t('admin.courses.noCoursesFiltered') }}
              </td>
            </tr>
            <tr v-for="course in filteredCourses" v-else :key="course.id" class="hover:bg-cardHover transition-colors">
              <td class="px-4 py-4 text-textSecondary font-mono hidden sm:table-cell">
                {{ course.id }}
              </td>
              <td class="px-4 py-4 font-medium text-textWhite">
                <div class="flex flex-col">
                  <span>{{ course.title }}</span>
                  <span class="text-xs text-textSecondary md:hidden mt-1">
                    {{ course.tags?.map((t) => t.name).join(', ') || '-' }}
                  </span>
                  <span class="text-xs text-textSecondary lg:hidden">
                    {{ course.lessons_count || '0' }}
                    {{ t('admin.courses.table.lessonsCount').toLowerCase() }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-4 hidden md:table-cell">
                <div class="flex flex-wrap gap-1">
                  <span v-for="tag in course.tags" :key="tag.id" class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 text-[10px] font-bold uppercase">
                    {{ tag.name }}
                  </span>
                  <span v-if="!course.tags?.length" class="text-textSecondary text-xs">-</span>
                </div>
              </td>
              <td class="px-4 py-4 text-textSecondary hidden lg:table-cell">
                {{ course.lessons_count || '0' }}
              </td>
              <td class="px-4 py-4 text-right">
                <div class="hidden sm:flex items-center justify-end gap-2">
                  <RouterLink :to="`/kurs/${course.id}`" target="_blank" class="px-3 py-1.5 rounded bg-bgSecondary text-textWhite hover:bg-strokePrimary/50 transition-colors text-xs font-bold inline-block whitespace-nowrap">
                    {{ t('admin.common.preview') }}
                  </RouterLink>
                  <RouterLink :to="`/admin/courses/${course.id}`" class="px-3 py-1.5 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors text-xs font-bold inline-block whitespace-nowrap">
                    {{ t('admin.common.edit') }}
                  </RouterLink>
                  <button class="px-3 py-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors text-xs font-bold whitespace-nowrap" @click="confirmDelete(course)">
                    {{ t('admin.common.delete') }}
                  </button>
                </div>

                <div class="sm:hidden flex items-center justify-end gap-2">
                  <RouterLink :to="`/admin/courses/${course.id}`" class="px-3 py-1.5 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors text-xs font-bold">
                    {{ t('admin.common.edit') }}
                  </RouterLink>
                  <button class="px-2 py-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors text-xs font-bold" @click="confirmDelete(course)">✕</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showAddModal = false">
      <div class="bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6 space-y-4">
        <h3 class="text-xl font-bold text-textWhite">{{ t('admin.courses.addModal.title') }}</h3>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.addModal.courseTitle') }}</label>
            <input v-model="newCourse.title" type="text" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.addModal.description') }}</label>
            <textarea v-model="newCourse.description" rows="3" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none"></textarea>
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.courses.table.tags') }}</label>
            <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-2 bg-bgSecondary rounded-lg border border-strokePrimary/30">
              <button v-for="tag in tags" :key="tag.id" :class="['px-2 py-1 rounded-md text-xs font-bold transition-colors border', newCourse.tags.includes(tag.id) ? 'bg-primary/20 text-primary border-primary' : 'bg-transparent text-textSecondary border-transparent hover:bg-white/5']" @click="toggleTag(tag.id)">
                {{ tag.name }}
              </button>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">Obrazek kursu (opcjonalnie)</label>
            <input type="file" accept="image/*" class="w-full text-sm text-textSecondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/20 file:text-primary hover:file:bg-primary/30 transition-colors" @change="handleImageUpload" />
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button class="px-4 py-2 rounded-lg text-textSecondary hover:text-textWhite font-semibold transition-colors" @click="showAddModal = false">
            {{ t('admin.common.cancel') }}
          </button>
          <button :disabled="isCreating" class="px-4 py-2 rounded-lg bg-primary text-textWhite font-bold hover:bg-primary/90 transition-all disabled:opacity-50" @click="createCourse">
            {{ isCreating ? t('admin.courses.addModal.creating') : t('admin.courses.addModal.create') }}
          </button>
        </div>
      </div>
    </div>

    <ConfirmationModal v-model:is-open="showDeleteModal" :title="t('admin.courses.deleteModal.title')" :message="t('admin.courses.deleteModal.message', { title: courseToDelete?.title || '' })" :confirm-text="t('admin.common.delete')" :cancel-text="t('admin.common.cancel')" is-danger @confirm="deleteCourse" />
  </div>
</template>
