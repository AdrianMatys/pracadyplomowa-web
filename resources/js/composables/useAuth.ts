import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from '@/composables/useI18n'

const isLoggedIn = ref(false)
const user = ref<any>(null)
const isLoading = ref(true)
let authInitPromise: Promise<void> | null = null

export const useAuth = () => {
  const router = useRouter()
  const { notify } = useNotifications()
  const { t } = useI18n()

  const initAuth = async () => {
    if (authInitPromise) {
      return authInitPromise
    }

    if (!isLoading.value) {
      return
    }

    authInitPromise = (async () => {
      try {
        const response = await api.get('/user')
        user.value = response.data
        isLoggedIn.value = true
      } catch {
        user.value = null
        isLoggedIn.value = false
      } finally {
        isLoading.value = false
      }
    })()

    return authInitPromise
  }

  const login = async (credentials: { email: string; password: string; remember?: boolean; recaptcha_token?: string }) => {
    try {
      await api.get('/sanctum/csrf-cookie')
      await api.post('/login', credentials)

      const userResponse = await api.get('/user')
      user.value = userResponse.data
      isLoggedIn.value = true

      notify('success', t('common.notifications.loginSuccess'))
      router.push({ name: 'main' })
    } catch (error) {
      notify('error', t('common.notifications.loginError'))
      throw error
    }
  }

  const register = async (data: { name: string; email: string; password: string; recaptcha_token?: string }) => {
    try {
      await api.get('/sanctum/csrf-cookie')
      await api.post('/register', data)

      notify('success', t('common.notifications.registerSuccess'))
    } catch (error) {
      notify('error', t('common.notifications.registerError'))
      throw error
    }
  }

  const logout = async () => {
    try {
      await api.post('/logout')
      notify('success', t('common.notifications.logoutSuccess'))
    } catch {
      // ignore
    } finally {
      isLoggedIn.value = false
      user.value = null
      router.push({ name: 'main' })
    }
  }

  const refreshUser = async () => {
    try {
      const response = await api.get('/user')
      user.value = response.data
    } catch {
      // ignore
    }
  }

  const setUser = (userData: any) => {
    user.value = userData
  }

  return {
    isLoggedIn,
    user,
    isLoading,
    initAuth,
    login,
    register,
    logout,
    refreshUser,
    setUser,
  }
}
