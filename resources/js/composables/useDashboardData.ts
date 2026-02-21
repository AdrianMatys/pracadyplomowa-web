import { watch } from 'vue'
import type { DashboardResponse } from '@/services/dataService'
import { fetchDashboardData } from '@/services/dataService'
import { useI18n } from '@/composables/useI18n'
import { useAsyncData } from '@/composables/useAsyncData'

let instance: ReturnType<typeof createInstance> | null = null

function createInstance() {
  const { currentLanguage } = useI18n()

  const { data: dashboardData, isLoading: isDashboardLoading, error: dashboardError, load, refresh } = useAsyncData<DashboardResponse>(() => fetchDashboardData(currentLanguage.value))

  const loadDashboardData = (forceReload = false) => load(forceReload)
  const refreshDashboardData = () => refresh()

  watch(currentLanguage, () => {
    if (dashboardData.value) {
      refreshDashboardData()
    }
  })

  return {
    dashboardData,
    isDashboardLoading,
    dashboardError,
    loadDashboardData,
    refreshDashboardData,
  }
}

export const useDashboardData = () => {
  if (!instance) {
    instance = createInstance()
  }
  return instance
}
