import { ref } from 'vue'
import api from '@/services/api'
import { useAuth } from '@/composables/useAuth'

const isDark = ref(true)

export function useTheme() {
  const toggleTheme = async () => {
    isDark.value = !isDark.value
    updateTheme()

    // Zapis w bazie danych, jeśli użytkownik jest zalogowany
    const { isLoggedIn } = useAuth()
    if (isLoggedIn.value) {
      try {
        await api.put('/api/users/me', {
          theme: isDark.value ? 'dark' : 'light',
        })
      } catch (e) {
        console.error('Błąd podczas zapisu motywu w bazie danych', e)
      }
    }
  }

  const updateTheme = () => {
    const html = document.documentElement
    if (isDark.value) {
      html.classList.remove('light')
      localStorage.setItem('theme', 'dark')
    } else {
      html.classList.add('light')
      localStorage.setItem('theme', 'light')
    }
  }

  const initTheme = () => {
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme) {
      isDark.value = savedTheme === 'dark'
    } else {
      isDark.value = true
    }
    updateTheme()
  }

  return {
    isDark,
    toggleTheme,
    initTheme,
  }
}
