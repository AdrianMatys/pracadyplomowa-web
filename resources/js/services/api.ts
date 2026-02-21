import axios from 'axios'
import { useNotifications } from '@/composables/useNotifications'
import { useI18n } from '@/composables/useI18n'

const api = axios.create({
  baseURL: '',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true,
  withXSRFToken: true,
})

api.interceptors.request.use((config) => {
  const language = localStorage.getItem('language') || 'pl'
  config.headers['Accept-Language'] = language
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const targetUrl = error.config?.url
    const isAuthCheck = targetUrl === '/user' || targetUrl === '/api/user'

    if (error.response && [401, 403].includes(error.response.status) && !isAuthCheck) {
      if (typeof window !== 'undefined') {
        localStorage.removeItem('user')

        const { notify } = useNotifications()
        const { t } = useI18n()
        notify('error', t('common.notifications.mustBeLoggedIn') || 'Musisz być zalogowany, aby wykonać tę akcję.')
      }
    }
    return Promise.reject(error)
  },
)

export default api
