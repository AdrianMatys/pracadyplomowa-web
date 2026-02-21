<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import { useDashboardData } from '@/composables/useDashboardData'
import { fetchArticleById } from '@/services/dataService'
import type { NewsArticle } from '@/constants/data'
import { useI18n } from '@/composables/useI18n'
import IconArrowLeft from '@/icons/IconArrowLeft.vue'
import { formatDate } from '@/utils/formatters'

const route = useRoute()
const router = useRouter()
const { dashboardData, loadDashboardData } = useDashboardData()
const { t } = useI18n()

const articleId = computed(() => route.params.articleId as string)
const article = ref<NewsArticle | null>(null)
const isLoading = ref(true)

const navLinksList = computed(() => dashboardData.value?.navLinks ?? [])

onMounted(async () => {
  loadDashboardData()
  isLoading.value = true
  article.value = await fetchArticleById(articleId.value)
  isLoading.value = false
})

const handleBack = () => {
  router.push({ name: 'news' })
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />
    <main class="mx-auto flex w-full max-w-4xl flex-1 flex-col gap-8 px-6 py-10">
      <button class="inline-flex w-max items-center gap-2 rounded-full border border-strokePrimary/40 px-5 py-2 text-sm text-textSecondary transition hover:border-textPrimary hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" tabindex="0" @click="handleBack">
        <IconArrowLeft class="h-4 w-4" />
        {{ t('common.back') || 'Back' }}
      </button>

      <div v-if="isLoading" class="flex items-center justify-center p-12">
        <span class="text-textSecondary">{{ t('common.loading') }}</span>
      </div>

      <article v-else-if="article" class="space-y-8">
        <header class="space-y-6">
          <div class="space-y-4">
            <div class="flex flex-wrap gap-3 text-xs text-textSecondary">
              <span class="uppercase tracking-[0.3em] font-bold text-textPrimary">{{ article.category }}</span>
              <span>•</span>
              <span>{{ formatDate(article.date) }}</span>
              <span>•</span>
              <span>{{ article.readTime }}</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-bold leading-tight text-textWhite">
              {{ article.title }}
            </h1>
            <div class="flex items-center gap-3">
              <div v-if="article.authorAvatar" class="h-10 w-10 rounded-full overflow-hidden border border-strokePrimary/30">
                <img :src="article.authorAvatar" :alt="article.author" class="h-full w-full object-cover" />
              </div>
              <div v-else class="h-10 w-10 rounded-full bg-gradient-to-tr from-textPrimary to-purple-500/50 flex items-center justify-center text-xs font-bold text-bgPrimary">
                {{ article.author.substring(0, 1).toUpperCase() }}
              </div>
              <div>
                <p class="text-sm font-semibold text-textWhite">{{ article.author }}</p>
                <p class="text-xs text-textSecondary">{{ t('news.author') }}</p>
              </div>
            </div>
          </div>

          <div class="w-full h-px bg-strokePrimary/30"></div>
        </header>

        <div class="prose prose-invert max-w-none text-textSecondary leading-relaxed">
          <div v-html="article.content"></div>
        </div>

        <footer class="pt-8 border-t border-strokePrimary/30">
          <div class="flex flex-wrap gap-2">
            <span v-for="tag in article.tags" :key="tag" class="rounded-full border border-strokePrimary/50 bg-tagBg px-3 py-1 text-xs text-textSecondary"> #{{ tag }} </span>
          </div>
        </footer>
      </article>

      <div v-else class="flex flex-col items-center justify-center gap-4 p-12 text-center rounded-2xl border border-dashed border-strokePrimary/30">
        <h2 class="text-xl font-semibold">{{ t('common.error') }}</h2>
        <p class="text-textSecondary">{{ t('news.articleNotFound') || 'Article not found' }}</p>
      </div>
    </main>
  </div>
</template>
