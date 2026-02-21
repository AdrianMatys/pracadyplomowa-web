<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from '@/composables/useI18n'

const route = useRoute()
const router = useRouter()
const { notify } = useNotifications()
const { t } = useI18n()

const newsId = route.params.id

type Article = {
  id: number
  title: string
  content: string
  type: 'news' | 'article'
  tags: { id: number; name: string }[]
  estimated_time?: number | null
}

type Tag = {
  id: number
  name: string
}

const article = ref<Article | null>(null)
const tags = ref<Tag[]>([])
const isLoading = ref(true)
const isSaving = ref(false)

const form = ref({
  title: '',
  content: '',
  type: 'news' as 'news' | 'article',
  tags: [] as number[],
  estimated_time: null as number | null,
})

const toggleTag = (tagId: number) => {
  const index = form.value.tags.indexOf(tagId)
  if (index === -1) {
    form.value.tags.push(tagId)
  } else {
    form.value.tags.splice(index, 1)
  }
}

const fetchArticle = async () => {
  isLoading.value = true
  try {
    const [articleResponse, tagsResponse] = await Promise.all([api.get(`/api/admin/news/${newsId}`), api.get('/api/technologies')])
    article.value = articleResponse.data
    tags.value = tagsResponse.data

    form.value = {
      title: articleResponse.data.title,
      content: articleResponse.data.content,
      type: articleResponse.data.type,
      tags: articleResponse.data.tags.map((t: Tag) => t.id),
      estimated_time: articleResponse.data.estimated_time || null,
    }
  } catch {
    notify('error', t('admin.common.error'))
    router.push('/admin/news')
  } finally {
    isLoading.value = false
  }
}

const updateArticle = async () => {
  isSaving.value = true
  try {
    await api.put(`/api/admin/news/${newsId}`, form.value)
    notify('success', t('admin.common.notifications.success'))
    fetchArticle()
  } catch (error: any) {
    console.error('Update Error:', error.response?.data)
    let message = t('admin.common.error')
    if (error.response?.data?.errors) {
      message = Object.values(error.response.data.errors).flat().join('\n')
    } else if (error.response?.data?.message) {
      message = error.response.data.message
    }
    notify('error', message)
  } finally {
    isSaving.value = false
  }
}

onMounted(fetchArticle)
</script>

<template>
  <div v-if="isLoading" class="flex justify-center py-12">
    <span class="text-textSecondary">{{ t('admin.common.loading') }}</span>
  </div>

  <div v-else-if="article" class="space-y-8 max-w-4xl mx-auto">
    <div class="flex items-center gap-4">
      <RouterLink to="/admin/news" class="p-2 rounded-lg bg-bgSecondary text-textSecondary hover:text-textWhite transition-colors"> ← {{ t('admin.common.back') }} </RouterLink>
      <h1 class="text-2xl font-bold text-textWhite">{{ t('admin.news.editPage.title') }}: {{ article.title }}</h1>
    </div>

    <div class="bg-card border border-strokePrimary/30 rounded-xl p-6 space-y-4">
      <h2 class="text-lg font-bold text-textWhite mb-4">{{ t('admin.news.editPage.details') }}</h2>

      <div class="grid gap-4">
        <div class="space-y-2 mb-4">
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.articleTitle') }}</label>
          <input v-model="form.title" type="text" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
        </div>

        <div class="space-y-2 mb-4">
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.common.type') }}</label>
          <select v-model="form.type" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none">
            <option value="news">{{ t('admin.news.types.news') }}</option>
            <option value="article">{{ t('admin.news.types.article') }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.tags') }}</label>
          <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-2 bg-bgSecondary rounded-lg border border-strokePrimary/30">
            <button v-for="tag in tags" :key="tag.id" :class="['px-2 py-1 rounded-md text-xs font-bold transition-colors border', form.tags.includes(tag.id) ? 'bg-primary/20 text-primary border-primary' : 'bg-transparent text-textSecondary border-transparent hover:bg-white/5']" @click="toggleTag(tag.id)">
              {{ tag.name }}
            </button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.readingTime') }}</label>
          <input v-model.number="form.estimated_time" type="number" min="1" :placeholder="t('admin.news.addModal.readingTimePlaceholder')" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
        </div>
        <div>
          <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.content') }}</label>
          <textarea v-model="form.content" rows="12" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none font-mono"></textarea>
        </div>
      </div>

      <div class="flex justify-end pt-2">
        <button :disabled="isSaving" class="px-6 py-2 bg-primary text-textWhite font-bold rounded-lg hover:bg-primary/90 transition-all disabled:opacity-50" @click="updateArticle">
          {{ isSaving ? t('admin.common.saving') : t('admin.common.save') }}
        </button>
      </div>
    </div>
  </div>
</template>
