<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/services/api'
import { useI18n } from '@/composables/useI18n'

type LogEntry = {
  id: number
  user_id: number
  user_name: string | null
  action: string
  description: string
  metadata: any
  created_at: string
}

type PaginatedResponse = {
  data: LogEntry[]
  current_page: number
  last_page: number
  total: number
}

const logs = ref<LogEntry[]>([])
const isLoading = ref(true)
const selectedLog = ref<LogEntry | null>(null)
const searchQuery = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const perPage = ref(50)
const totalLogs = ref(0)
const error = ref<string | null>(null)
const isClearModalOpen = ref(false)
const adminPassword = ref('')
const isClearing = ref(false)
const clearError = ref<string | null>(null)
const { t } = useI18n()

let searchTimeout: ReturnType<typeof setTimeout>

const fetchLogs = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await api.get('/api/admin/logs', {
      params: {
        page,
        search: searchQuery.value,
        per_page: perPage.value,
      },
    })
    const data = response.data as PaginatedResponse
    logs.value = data.data
    currentPage.value = data.current_page
    lastPage.value = data.last_page
    totalLogs.value = data.total
  } catch (err: any) {
    console.error('Failed to fetch logs:', err)
    error.value = err.message || 'Unknown error'
    logs.value = []
  } finally {
    isLoading.value = false
  }
}

watch([searchQuery, perPage], () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchLogs(1)
  }, 300)
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString()
}

const getActionClass = (action: string) => {
  if (action.includes('error') || action.includes('fail')) return 'text-red-400 bg-red-500/10'
  if (action.includes('warning')) return 'text-amber-400 bg-amber-500/10'
  if (action.includes('login') || action.includes('register')) return 'text-green-400 bg-green-500/10'
  if (action.includes('achievement')) return 'text-purple-400 bg-purple-500/10'
  return 'text-blue-400 bg-blue-500/10'
}

const changePage = (page: number) => {
  if (page >= 1 && page <= lastPage.value) {
    fetchLogs(page)
  }
}

const handleClearLogs = async () => {
  if (!adminPassword.value) {
    clearError.value = 'Hasło jest wymagane.'
    return
  }

  isClearing.value = true
  clearError.value = null

  try {
    await api.delete('/api/admin/logs/clear', {
      data: { password: adminPassword.value },
    })
    isClearModalOpen.value = false
    adminPassword.value = ''
    fetchLogs(1)
  } catch (err: any) {
    console.error('Failed to clear logs:', err)
    clearError.value = err.response?.data?.message || 'Błąd podczas czyszczenia logów.'
  } finally {
    isClearing.value = false
  }
}

const paginationRange = computed(() => {
  const range = []
  const delta = 2
  const left = currentPage.value - delta
  const right = currentPage.value + delta + 1

  for (let i = 1; i <= lastPage.value; i++) {
    if (i === 1 || i === lastPage.value || (i >= left && i < right)) {
      range.push(i)
    } else if (i === left - 1 || i === right) {
      range.push('...')
    }
  }
  return range
})

const handleCancelClearModal = () => {
  isClearModalOpen.value = false
  adminPassword.value = ''
  clearError.value = null
}

onMounted(() => fetchLogs())
</script>

<template>
  <div class="space-y-6 h-full flex flex-col relative">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-textWhite">{{ t('admin.logs.title') }}</h1>
      <div v-if="error" class="text-red-500 text-sm bg-red-500/10 px-4 py-2 rounded">Error: {{ error }}</div>
      <div class="flex gap-3 items-center">
        <div class="flex items-center gap-2 mr-4">
          <span class="text-xs text-textSecondary uppercase font-bold">{{ t('admin.logs.perPage') || 'Logów na stronę' }}</span>
          <select v-model="perPage" class="bg-bgSecondary border border-strokePrimary/30 rounded-lg px-2 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors">
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
            <option :value="250">250</option>
          </select>
        </div>
        <input v-model="searchQuery" type="text" :placeholder="t('admin.logs.searchPlaceholder')" class="bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors w-64" />
        <button class="px-4 py-2 bg-red-500/10 border border-red-500/30 text-red-500 rounded-lg text-sm font-semibold hover:bg-red-500/20 transition-colors" @click="isClearModalOpen = true">
          {{ t('admin.logs.clear') || 'Wyczyść logi' }}
        </button>
        <button class="px-4 py-2 bg-bgSecondary border border-strokePrimary/30 rounded-lg text-sm font-semibold hover:bg-cardHover transition-colors" @click="fetchLogs(currentPage)">
          {{ t('admin.logs.refresh') }}
        </button>
      </div>
    </div>

    <div class="flex-1 rounded-xl border border-strokePrimary/30 bg-card overflow-hidden flex flex-col">
      <div class="px-6 py-3 border-b border-strokePrimary/20 flex justify-between items-center bg-bgSecondary/20">
        <div class="text-xs text-textSecondary uppercase font-bold tracking-wider">
          {{ t('admin.logs.total') || 'Razem logów' }}:
          <span class="text-textWhite">{{ totalLogs }}</span>
        </div>
        <div class="text-xs text-textSecondary italic">{{ t('admin.logs.showingPage') || 'Strona' }} {{ currentPage }} / {{ lastPage }}</div>
      </div>

      <div class="overflow-auto flex-1 custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead class="bg-bgSecondary text-textSecondary uppercase text-xs sticky top-0 z-10">
            <tr>
              <th class="px-6 py-4 font-semibold w-40">{{ t('admin.common.date') }}</th>
              <th class="px-6 py-4 font-semibold w-40">{{ t('admin.logs.table.user') }}</th>
              <th class="px-6 py-4 font-semibold w-32">{{ t('admin.logs.table.action') }}</th>
              <th class="px-6 py-4 font-semibold">{{ t('admin.logs.table.description') }}</th>
              <th class="px-6 py-4 font-semibold w-24 text-right">
                {{ t('admin.common.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-strokePrimary/20 text-sm">
            <tr v-if="isLoading">
              <td colspan="5" class="px-6 py-8 text-center text-textSecondary">
                {{ t('admin.logs.loading') }}
              </td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-textSecondary">
                {{ t('admin.logs.empty') }}
              </td>
            </tr>
            <tr v-for="log in logs" v-else :key="log.id" class="hover:bg-cardHover transition-colors group">
              <td class="px-6 py-3 text-textSecondary text-xs font-mono whitespace-nowrap">
                {{ formatDate(log.created_at) }}
              </td>
              <td class="px-6 py-3 text-textWhite font-medium text-xs">
                <div class="flex flex-col">
                  <span>{{ log.user_name || 'N/A' }}</span>
                  <span class="text-[10px] text-textSecondary">ID: {{ log.user_id }}</span>
                </div>
              </td>
              <td class="px-6 py-3">
                <span :class="['px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider', getActionClass(log.action)]">
                  {{ log.action }}
                </span>
              </td>
              <td class="px-6 py-3 text-textWhite font-medium truncate max-w-xl">
                {{ log.description }}
              </td>
              <td class="px-6 py-3 text-right">
                <button class="px-3 py-1.5 rounded bg-bgSecondary text-textSecondary hover:text-textWhite hover:bg-strokePrimary transition-colors text-xs font-bold" @click="selectedLog = log">
                  {{ t('admin.common.details') }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="lastPage > 1" class="p-4 border-t border-strokePrimary/30 flex justify-center items-center gap-2 bg-bgSecondary/20">
        <button :disabled="currentPage === 1" class="h-8 w-8 flex items-center justify-center rounded bg-bgSecondary text-textSecondary hover:text-textWhite disabled:opacity-50 transition-colors" @click="changePage(currentPage - 1)">&lt;</button>

        <template v-for="page in paginationRange" :key="page">
          <button v-if="page !== '...'" class="h-8 w-8 flex items-center justify-center rounded text-sm font-semibold transition-all" :class="[currentPage === page ? 'bg-textPrimary text-bgPrimary' : 'bg-bgSecondary text-textSecondary hover:text-textWhite']" @click="changePage(page as number)">
            {{ page }}
          </button>
          <span v-else class="px-2 text-textSecondary font-bold">{{ page }}</span>
        </template>

        <button :disabled="currentPage === lastPage" class="h-8 w-8 flex items-center justify-center rounded bg-bgSecondary text-textSecondary hover:text-textWhite disabled:opacity-50 transition-colors" @click="changePage(currentPage + 1)">&gt;</button>
      </div>
    </div>

    <div v-if="selectedLog" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="selectedLog = null">
      <div class="bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-2xl flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-strokePrimary/30 flex justify-between items-center bg-bgSecondary/30">
          <div class="flex items-center gap-3">
            <span :class="['px-2 py-1 rounded text-xs font-bold uppercase', getActionClass(selectedLog.action)]">
              {{ selectedLog.action }}
            </span>
            <span class="text-textSecondary text-sm font-mono">{{ formatDate(selectedLog.created_at) }}</span>
          </div>
          <button class="text-textSecondary hover:text-textWhite transition-colors p-2" @click="selectedLog = null">✕</button>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs text-textSecondary uppercase font-bold block mb-1">User</label>
              <div class="text-textWhite">{{ selectedLog.user_name || 'N/A' }} (ID: {{ selectedLog.user_id }})</div>
            </div>
          </div>

          <div>
            <label class="text-xs text-textSecondary uppercase font-bold block mb-1">Description</label>
            <div class="text-textWhite">{{ selectedLog.description }}</div>
          </div>

          <div v-if="selectedLog.metadata">
            <label class="text-xs text-textSecondary uppercase font-bold block mb-1">Metadata</label>
            <pre class="bg-[#0d1117] p-3 rounded-lg text-xs font-mono text-gray-300 whitespace-pre-wrap">{{ JSON.stringify(selectedLog.metadata, null, 2) }}</pre>
          </div>
        </div>
        <div class="p-4 border-t border-strokePrimary/30 flex justify-end bg-bgSecondary/30">
          <button class="px-6 py-2 bg-textPrimary text-bgPrimary font-bold rounded-lg hover:opacity-90 transition-opacity" @click="selectedLog = null">
            {{ t('admin.common.close') }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isClearModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="isClearModalOpen = false">
      <div class="bg-bgPrimary border border-red-500/30 rounded-2xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-strokePrimary/30 bg-red-500/5">
          <h3 class="text-xl font-bold text-textWhite">
            {{ t('admin.logs.clearModal.title') || 'Wyczyścić logi?' }}
          </h3>
        </div>

        <div class="p-6 space-y-4">
          <p class="text-sm text-textSecondary">
            {{ t('admin.logs.clearModal.description') || 'Tej operacji nie można cofnąć. Wszystkie dotychczasowe logi zostaną usunięte z bazy danych. W celu potwierdzenia wpisz hasło do swojego konta administratora.' }}
          </p>

          <div class="space-y-2">
            <label class="text-xs text-textSecondary uppercase font-bold block">{{ t('login.passwordLabel') }}</label>
            <input v-model="adminPassword" type="password" placeholder="••••••••" class="w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-red-500 focus:outline-none transition-colors" @keyup.enter="handleClearLogs" />
          </div>

          <div v-if="clearError" class="text-xs text-red-500 bg-red-500/10 p-2 rounded">
            {{ clearError }}
          </div>
        </div>

        <div class="p-4 border-t border-strokePrimary/30 flex justify-end gap-3 bg-bgSecondary/30">
          <button class="px-4 py-2 rounded-lg text-sm font-semibold text-textSecondary hover:text-textWhite transition-colors" @click="handleCancelClearModal">
            {{ t('common.cancel') }}
          </button>
          <button class="px-6 py-2 bg-red-500 text-white font-bold rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50 flex items-center gap-2" :disabled="isClearing || !adminPassword" @click="handleClearLogs">
            <span v-if="isClearing" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ isClearing ? t('common.loading') : t('admin.logs.clearConfirm') || 'Potwierdzam i czyszczę' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #30363d;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #404854;
}
</style>
