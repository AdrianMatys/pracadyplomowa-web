import { ref, computed } from 'vue'
import { translations, type Language } from '@/i18n/translations'

const currentLanguage = ref<Language>((localStorage.getItem('language') as Language) || 'pl')

export function useI18n() {
  /**
   * Translation function - retrieves text by dot-notation key
   * Example: t('header.login') returns 'Zaloguj się' or 'Log in'
   */
  const t = (key: string, params?: Record<string, string | number>): string => {
    const keys = key.split('.')
    let value: any = translations[currentLanguage.value]

    for (const k of keys) {
      if (value === undefined || value === null) {
        return key
      }
      value = value[k]
    }

    if (typeof value !== 'string') {
      return key
    }

    if (params) {
      Object.keys(params).forEach((key) => {
        value = value.replace(`{${key}}`, String(params[key]))
      })
    }

    return value
  }

  /**
   * Set the current language and persist to localStorage
   */
  const setLanguage = (lang: Language) => {
    currentLanguage.value = lang
    localStorage.setItem('language', lang)

    document.documentElement.lang = lang
  }

  /**
   * Toggle between Polish and English
   */
  const toggleLanguage = () => {
    const newLang = currentLanguage.value === 'pl' ? 'en' : 'pl'
    setLanguage(newLang)
  }

  /**
   * Initialize language from localStorage on mount
   */
  const initLanguage = () => {
    const savedLang = localStorage.getItem('language') as Language | null
    if (savedLang && (savedLang === 'pl' || savedLang === 'en')) {
      currentLanguage.value = savedLang
    }
    document.documentElement.lang = currentLanguage.value
  }

  /**
   * Check if current language is Polish
   */
  const isPolish = computed(() => currentLanguage.value === 'pl')

  /**
   * Check if current language is English
   */
  const isEnglish = computed(() => currentLanguage.value === 'en')

  return {
    currentLanguage,
    t,
    setLanguage,
    toggleLanguage,
    initLanguage,
    isPolish,
    isEnglish,
  }
}
