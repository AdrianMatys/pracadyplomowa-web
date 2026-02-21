import { ref, type Ref } from 'vue'

export function useAsyncData<T>(fetchFn: () => Promise<T>, options?: { immediate?: boolean }) {
  const data = ref<T | null>(null) as Ref<T | null>
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const load = async (forceReload = false) => {
    if (!forceReload && data.value) return
    if (isLoading.value) return

    isLoading.value = true
    error.value = null

    try {
      data.value = await fetchFn()
    } catch (e) {
      error.value = e instanceof Error ? e.message : String(e)
    } finally {
      isLoading.value = false
    }
  }

  const refresh = async () => {
    isLoading.value = true
    try {
      data.value = await fetchFn()
      error.value = null
    } catch (e) {
      error.value = e instanceof Error ? e.message : String(e)
    } finally {
      isLoading.value = false
    }
  }

  if (options?.immediate) {
    load()
  }

  return { data, isLoading, error, load, refresh }
}
