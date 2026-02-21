import type { NewsResponse } from '@/services/dataService'
import { fetchNewsData } from '@/services/dataService'
import { useAsyncData } from '@/composables/useAsyncData'

const { data: newsData, isLoading: isNewsLoading, error: newsError, load, refresh } = useAsyncData<NewsResponse>(() => fetchNewsData())

export const useNewsData = () => ({
  newsData,
  isNewsLoading,
  newsError,
  loadNewsData: () => load(),
  refreshNewsData: () => refresh(),
})
