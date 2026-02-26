import { ref, computed } from 'vue'
import { translations, type Language } from '@/i18n/translations'

const currentLanguage = ref<Language>((localStorage.getItem('language') as Language) || 'pl')

export function useI18n() {
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

  const setLanguage = (lang: Language) => {
    currentLanguage.value = lang
    localStorage.setItem('language', lang)

    document.documentElement.lang = lang
  }

  const toggleLanguage = () => {
    const newLang = currentLanguage.value === 'pl' ? 'en' : 'pl'
    setLanguage(newLang)
  }

  const initLanguage = () => {
    const savedLang = localStorage.getItem('language') as Language | null
    if (savedLang && (savedLang === 'pl' || savedLang === 'en')) {
      currentLanguage.value = savedLang
    }
    document.documentElement.lang = currentLanguage.value
  }

  const isPolish = computed(() => currentLanguage.value === 'pl')

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
