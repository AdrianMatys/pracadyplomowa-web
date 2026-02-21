<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from '@/composables/useI18n'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import IconEyeOff from '@/icons/IconEyeOff.vue'
import IconEye from '@/icons/IconEye.vue'

const { notify } = useNotifications()
const { t } = useI18n()

type Article = {
  id: number
  title: string
  content: string
  type: 'news' | 'article'
  is_published: boolean
  tags: { id: number; name: string }[]
  created_at: string
  user: {
    profile: {
      nickname: string
    }
    email: string
  }
}

type Tag = {
  id: number
  name: string
}

const articles = ref<Article[]>([])
const tags = ref<Tag[]>([])
const isLoading = ref(true)
const searchQuery = ref('')

const filteredArticles = computed(() => {
  if (!searchQuery.value) return articles.value
  const query = searchQuery.value.toLowerCase()
  return articles.value.filter((article) => article.title.toLowerCase().includes(query))
})

const showDeleteModal = ref(false)
const articleToDelete = ref<Article | null>(null)

const showAddModal = ref(false)
const newArticle = ref({
  title: '',
  content: '',
  type: 'news' as 'news' | 'article',
  tags: [] as number[],
  estimated_time: null as number | null,
})
const isCreating = ref(false)

const toggleTag = (tagId: number) => {
  const index = newArticle.value.tags.indexOf(tagId)
  if (index === -1) {
    newArticle.value.tags.push(tagId)
  } else {
    newArticle.value.tags.splice(index, 1)
  }
}

const fetchArticles = async () => {
  isLoading.value = true
  try {
    const [newsResponse, tagsResponse] = await Promise.all([api.get('/api/admin/news'), api.get('/api/technologies')])
    articles.value = newsResponse.data.data
    tags.value = tagsResponse.data
  } catch {
    notify('error', t('admin.common.error'))
  } finally {
    isLoading.value = false
  }
}

const confirmDelete = (article: Article) => {
  articleToDelete.value = article
  showDeleteModal.value = true
}

const deleteArticle = async () => {
  if (!articleToDelete.value) return

  try {
    await api.delete(`/api/admin/news/${articleToDelete.value.id}`)
    articles.value = articles.value.filter((a) => a.id !== articleToDelete.value!.id)
    notify('success', t('admin.common.notifications.success'))
  } catch {
    notify('error', t('admin.common.error'))
  }
}

const togglePublish = async (article: Article) => {
  try {
    const response = await api.patch(`/api/admin/news/${article.id}/toggle-publish`)
    article.is_published = response.data.is_published
    notify('success', article.is_published ? t('admin.news.published') : t('admin.news.hidden'))
  } catch {
    notify('error', t('admin.common.error'))
  }
}

const createArticle = async () => {
  if (!newArticle.value.title) {
    notify('error', t('admin.news.titleRequired'))
    return
  }

  isCreating.value = true
  try {
    const response = await api.post('/api/admin/news', newArticle.value)
    articles.value.unshift(response.data)
    notify('success', t('admin.common.notifications.success'))
    showAddModal.value = false
    newArticle.value = { title: '', content: '', type: 'news', tags: [], estimated_time: null }
  } catch (error: any) {
    notify('error', error.response?.data?.message || t('admin.common.error'))
  } finally {
    isCreating.value = false
  }
}

onMounted(fetchArticles)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-textWhite">{{ t('admin.news.title') }}</h1>
      <div class="flex gap-3">
        <input v-model="searchQuery" type="text" :placeholder="t('admin.common.search')" class="bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors w-64" />
        <button class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-500 transition-colors shadow-lg shadow-blue-500/20" @click="showAddModal = true">+ {{ t('admin.news.addArticle') }}</button>
      </div>
    </div>

    <div class="rounded-xl border border-strokePrimary/30 bg-card">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-bgSecondary text-textSecondary uppercase text-xs">
            <tr>
              <th class="px-4 py-4 font-semibold hidden sm:table-cell">
                {{ t('admin.common.id') }}
              </th>
              <th class="px-4 py-4 font-semibold">{{ t('admin.news.table.title') }}</th>
              <th class="px-4 py-4 font-semibold hidden md:table-cell">
                {{ t('admin.news.table.tags') }}
              </th>
              <th class="px-4 py-4 font-semibold hidden lg:table-cell">
                {{ t('admin.news.table.author') }}
              </th>
              <th class="px-4 py-4 font-semibold hidden xl:table-cell">
                {{ t('admin.news.table.date') }}
              </th>
              <th class="px-4 py-4 font-semibold text-right">{{ t('admin.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-strokePrimary/20 text-sm">
            <tr v-if="isLoading">
              <td colspan="6" class="px-4 py-8 text-center text-textSecondary">
                {{ t('admin.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="filteredArticles.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-textSecondary">
                {{ t('admin.news.noNews') }}
              </td>
            </tr>
            <tr v-for="article in filteredArticles" v-else :key="article.id" class="hover:bg-cardHover transition-colors">
              <td class="px-4 py-4 text-textSecondary font-mono hidden sm:table-cell">
                {{ article.id }}
              </td>
              <td class="px-4 py-4 font-medium text-textWhite">
                <div class="flex flex-col">
                  <span>{{ article.title }}</span>
                  <span class="text-xs text-textSecondary lg:hidden">
                    {{ article.user.profile?.nickname || article.user.email }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-4 hidden md:table-cell">
                <div class="flex flex-wrap gap-1">
                  <span v-for="tag in article.tags" :key="tag.id" class="px-2 py-0.5 rounded bg-gray-500/10 text-gray-400 text-[10px] font-bold uppercase">
                    {{ tag.name }}
                  </span>
                  <span v-if="!article.tags?.length" class="text-textSecondary text-xs">-</span>
                </div>
              </td>
              <td class="px-4 py-4 text-textSecondary hidden lg:table-cell">
                {{ article.user.profile?.nickname || article.user.email }}
              </td>
              <td class="px-4 py-4 text-textSecondary hidden xl:table-cell whitespace-nowrap">
                {{ new Date(article.created_at).toLocaleDateString() }}
              </td>
              <td class="px-4 py-4 text-right">
                <div class="hidden md:flex items-center justify-end gap-2">
                  <a :href="`/artykul/${article.id}`" target="_blank" class="px-3 py-1.5 rounded bg-bgSecondary text-textWhite hover:bg-strokePrimary/50 transition-colors text-xs font-bold whitespace-nowrap">
                    {{ t('admin.common.preview') }}
                  </a>
                  <RouterLink :to="`/admin/news/${article.id}`" class="px-3 py-1.5 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors text-xs font-bold whitespace-nowrap">
                    {{ t('admin.common.edit') }}
                  </RouterLink>
                  <button :class="['px-3 py-1.5 rounded text-xs font-bold transition-colors whitespace-nowrap', article.is_published ? 'bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/20' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20']" @click="togglePublish(article)">
                    {{ article.is_published ? t('admin.news.hide') : t('admin.news.publish') }}
                  </button>
                  <button class="px-3 py-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors text-xs font-bold whitespace-nowrap" @click="confirmDelete(article)">
                    {{ t('admin.common.delete') }}
                  </button>
                </div>

                <div class="md:hidden flex items-center justify-end gap-2">
                  <RouterLink :to="`/admin/news/${article.id}`" class="px-3 py-1.5 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors text-xs font-bold">
                    {{ t('admin.common.edit') }}
                  </RouterLink>
                  <button :class="['px-2 py-1.5 rounded text-xs font-bold transition-colors flex items-center justify-center', article.is_published ? 'bg-yellow-500/10 text-yellow-400' : 'bg-emerald-500/10 text-emerald-400']" @click="togglePublish(article)">
                    <IconEyeOff v-if="article.is_published" class="h-4 w-4" />
                    <IconEye v-else class="h-4 w-4" />
                  </button>
                  <button class="px-2 py-1.5 rounded bg-red-500/10 text-red-500 transition-colors text-xs font-bold" @click="confirmDelete(article)">✕</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showAddModal = false">
      <div class="bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-md shadow-2xl overflow-hidden p-6 space-y-4">
        <h3 class="text-xl font-bold text-textWhite">{{ t('admin.news.addModal.title') }}</h3>

        <div class="space-y-4">
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.articleTitle') }}</label>
            <input v-model="newArticle.title" type="text" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.tags') }}</label>
            <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-2 bg-bgSecondary rounded-lg border border-strokePrimary/30">
              <button v-for="tag in tags" :key="tag.id" :class="['px-2 py-1 rounded-md text-xs font-bold transition-colors border', newArticle.tags.includes(tag.id) ? 'bg-primary/20 text-primary border-primary' : 'bg-transparent text-textSecondary border-transparent hover:bg-white/5']" @click="toggleTag(tag.id)">
                {{ tag.name }}
              </button>
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.readingTime') }}</label>
            <input v-model.number="newArticle.estimated_time" type="number" min="1" :placeholder="t('admin.news.addModal.readingTimePlaceholder')" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none" />
          </div>
          <div>
            <label class="block text-xs font-bold text-textSecondary uppercase mb-1">{{ t('admin.news.addModal.content') }}</label>
            <textarea v-model="newArticle.content" rows="5" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button class="px-4 py-2 rounded-lg text-textSecondary hover:text-textWhite font-semibold transition-colors" @click="showAddModal = false">
            {{ t('admin.common.cancel') }}
          </button>
          <button :disabled="isCreating" class="px-4 py-2 rounded-lg bg-primary text-textWhite font-bold hover:bg-primary/90 transition-all disabled:opacity-50" @click="createArticle">
            {{ isCreating ? t('admin.news.addModal.creating') : t('admin.news.addModal.create') }}
          </button>
        </div>
      </div>
    </div>

    <ConfirmationModal v-model:is-open="showDeleteModal" :title="t('admin.news.deleteModal.title')" :message="t('admin.news.deleteModal.message', { title: articleToDelete?.title || '' })" :confirm-text="t('admin.common.delete')" :cancel-text="t('admin.common.cancel')" is-danger @confirm="deleteArticle" />
  </div>
</template>
