<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import MainHeader from '@/components/MainHeader.vue'
import { useDashboardData } from '@/composables/useDashboardData'
import { useNewsData } from '@/composables/useNewsData'
import { useI18n } from '@/composables/useI18n'
import { formatDate } from '@/utils/formatters'
import IconArrowRight from '@/icons/IconArrowRight.vue'
import IconSearch from '@/icons/IconSearch.vue'

const { dashboardData, loadDashboardData } = useDashboardData()
const { newsData, isNewsLoading, loadNewsData } = useNewsData()
const { t } = useI18n()

const searchQuery = ref('')
const perPage = ref(10)
const currentPage = ref(1)

onMounted(() => {
  loadDashboardData()
  loadNewsData()
})

const navLinksList = computed(() => dashboardData.value?.navLinks ?? [])
const allArticles = computed(() => newsData.value?.articles ?? [])

const filteredArticles = computed(() => {
  let result = allArticles.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter((article) => article.title.toLowerCase().includes(query) || article.excerpt?.toLowerCase().includes(query) || article.author?.toLowerCase().includes(query))
  }

  return result
})

const totalPages = computed(() => Math.ceil(filteredArticles.value.length / perPage.value))

const paginatedArticles = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredArticles.value.slice(start, start + perPage.value)
})

const featuredArticle = computed(() => paginatedArticles.value[0])
const secondArticle = computed(() => paginatedArticles.value[1] || null)
const remainingArticles = computed(() => (paginatedArticles.value.length > 2 ? paginatedArticles.value.slice(2) : []))

watch([searchQuery, perPage], () => {
  currentPage.value = 1
})

import { useRouter } from 'vue-router'

const router = useRouter()

const handleReadArticle = (articleId: string) => {
  router.push({ name: 'article-detail', params: { articleId } })
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-10 px-6 py-10">
      <section v-if="featuredArticle" class="grid gap-8 rounded-3xl border border-strokePrimary/30 bg-card p-8 md:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-5">
          <span class="text-xs uppercase tracking-[0.3em] text-textSecondary">{{ t('news.featuredArticle') }}</span>
          <h1 class="text-3xl font-bold leading-tight text-textWhite">
            {{ featuredArticle.title }}
          </h1>
          <p class="text-sm text-textSecondary">
            {{ featuredArticle.excerpt }}
          </p>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <div v-if="featuredArticle.authorAvatar" class="h-8 w-8 rounded-full overflow-hidden border border-white/10">
                <img :src="featuredArticle.authorAvatar" :alt="featuredArticle.author" class="h-full w-full object-cover" />
              </div>
              <div v-else class="h-8 w-8 rounded-full bg-gradient-to-tr from-textPrimary to-purple-500/50 flex items-center justify-center text-[10px] font-bold text-bgPrimary">
                {{ featuredArticle.author.substring(0, 1).toUpperCase() }}
              </div>
              <span class="text-xs text-textSecondary">{{ featuredArticle.author }}</span>
            </div>
            <div class="flex gap-3 text-xs text-textSecondary">
              <span class="rounded-full border border-strokePrimary/60 px-3 py-1">{{ formatDate(featuredArticle.date) }}</span>
              <span class="rounded-full border border-strokePrimary/60 px-3 py-1">{{ featuredArticle.readTime }}</span>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <span v-for="tag in featuredArticle.tags" :key="tag" class="rounded-full border border-textPrimary/40 bg-tagBg px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-textPrimary">
              {{ tag }}
            </span>
          </div>

          <button class="inline-flex items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('news.readArticle')" tabindex="0" @click="handleReadArticle(featuredArticle.id)">
            {{ t('news.readArticle') }}
            <IconArrowRight class="h-4 w-4" />
          </button>
        </div>

        <div v-if="secondArticle" class="relative overflow-hidden rounded-3xl border border-strokePrimary/30 bg-cardDarker cursor-pointer transition-transform hover:scale-[1.02]" @click="handleReadArticle(secondArticle.id)">
          <div
            class="absolute inset-0 opacity-40"
            :style="{
              background: `radial-gradient(circle at top, ${secondArticle.accent}, transparent 50%)`,
            }"
          ></div>
          <div class="relative flex h-full flex-col justify-between p-6">
            <div>
              <p class="text-xs uppercase tracking-[0.4em] text-textSecondary">
                {{ t('news.platformNews') }}
              </p>
              <p class="mt-3 text-lg font-semibold text-textWhite">
                {{ secondArticle.title }}
              </p>
            </div>
            <div class="space-y-3 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
              <div class="flex justify-between text-xs text-textSecondary">
                <span>{{ t('news.type') }}</span>
                <span>{{ secondArticle.category }}</span>
              </div>
              <div class="flex justify-between items-center text-xs text-textSecondary">
                <span>{{ t('news.author') }}</span>
                <div class="flex items-center gap-2">
                  <div v-if="secondArticle.authorAvatar" class="h-6 w-6 rounded-full overflow-hidden border border-white/10">
                    <img :src="secondArticle.authorAvatar" :alt="secondArticle.author" class="h-full w-full object-cover" />
                  </div>
                  <span>{{ secondArticle.author }}</span>
                </div>
              </div>
              <div class="flex justify-between text-xs text-textSecondary">
                <span>{{ t('news.readTime') }}</span>
                <span>{{ secondArticle.readTime }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-textSecondary">
              {{ t('news.newsAndArticles') }}
            </p>
            <h2 class="text-2xl font-semibold text-textWhite">{{ t('news.stayUpdated') }}</h2>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-4">
          <div class="relative flex-1 max-w-md">
            <IconSearch class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-textSecondary" />
            <input v-model="searchQuery" type="text" :placeholder="t('news.searchPlaceholder') || 'Szukaj artykułów...'" class="w-full rounded-full border border-strokePrimary/30 bg-cardHover py-2.5 pl-12 pr-4 text-sm text-textWhite placeholder:text-textSecondary/70 focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" />
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-textSecondary">{{ t('news.show') }}</span>
            <select v-model="perPage" class="rounded-lg border border-strokePrimary/30 bg-cardHover px-3 py-2 text-sm text-textWhite focus:border-textPrimary focus:outline-none">
              <option :value="10">10</option>
              <option :value="20">20</option>
              <option :value="50">50</option>
              <option :value="100">{{ t('news.all') }}</option>
            </select>
          </div>
        </div>

        <p class="text-sm text-textSecondary">
          {{
            t('news.showingResults', {
              current: paginatedArticles.length,
              total: filteredArticles.length,
            })
          }}
        </p>

        <div v-if="remainingArticles.length" class="grid gap-5 md:grid-cols-2">
          <article v-for="article in remainingArticles" :key="article.id" class="flex h-full flex-col justify-between gap-4 rounded-2xl border border-strokePrimary/30 bg-card p-5 transition hover:bg-cardHover cursor-pointer" @click="handleReadArticle(article.id)">
            <div class="space-y-3">
              <div class="flex items-center justify-between text-xs text-textSecondary">
                <span class="uppercase tracking-[0.3em] text-textSecondary">{{ article.category }}</span>
                <span>{{ article.readTime }}</span>
              </div>
              <h3 class="text-xl font-semibold text-textWhite">
                {{ article.title }}
              </h3>
              <p class="text-sm text-textSecondary">
                {{ article.excerpt }}
              </p>
              <div class="flex flex-wrap items-center gap-3 text-[11px] text-textSecondary">
                <span class="rounded-full border border-strokePrimary/40 px-3 py-1">{{ formatDate(article.date) }}</span>
                <div class="flex items-center gap-2">
                  <div v-if="article.authorAvatar" class="h-5 w-5 rounded-full overflow-hidden border border-white/10">
                    <img :src="article.authorAvatar" :alt="article.author" class="h-full w-full object-cover" />
                  </div>
                  <span>{{ article.author }}</span>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap gap-2">
              <span v-for="tag in article.tags" :key="tag" class="rounded-full border border-strokePrimary/50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-textSecondary">
                {{ tag }}
              </span>
            </div>
          </article>
        </div>
        <p v-else class="rounded-2xl border border-dashed border-strokePrimary/40 bg-card p-6 text-center text-sm text-textSecondary">
          {{ isNewsLoading ? t('news.loadingArticles') : t('news.noArticles') }}
        </p>

        <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 pt-6">
          <button :disabled="currentPage === 1" class="px-4 py-2 rounded-lg border border-strokePrimary/30 text-sm font-semibold text-textSecondary hover:bg-cardHover disabled:opacity-50 disabled:cursor-not-allowed transition" @click="currentPage--">
            {{ t('news.previous') }}
          </button>
          <span class="px-4 py-2 text-sm text-textSecondary">
            {{ t('news.pageOf', { current: currentPage, total: totalPages }) }}
          </span>
          <button :disabled="currentPage === totalPages" class="px-4 py-2 rounded-lg border border-strokePrimary/30 text-sm font-semibold text-textSecondary hover:bg-cardHover disabled:opacity-50 disabled:cursor-not-allowed transition" @click="currentPage++">
            {{ t('news.next') }}
          </button>
        </div>
      </section>
    </main>
  </div>
</template>
