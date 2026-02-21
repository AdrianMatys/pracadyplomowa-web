import { ref } from 'vue'

declare global {
  interface Window {
    AppConfig: {
      recaptchaSiteKey: string
    }
    grecaptcha: {
      ready: (callback: () => void) => void
      execute: (siteKey: string, options: { action: string }) => Promise<string>
    }
  }
}

const SITE_KEY = window.AppConfig?.recaptchaSiteKey || import.meta.env.VITE_RECAPTCHA_SITE_KEY

const isLoaded = ref(false)
const isLoading = ref(false)

export function useRecaptcha() {
  const loadScript = (): Promise<void> => {
    if (isLoaded.value) return Promise.resolve()

    if (document.querySelector('script[src^="https://www.google.com/recaptcha/api.js"]')) {
      isLoading.value = true
      return new Promise((resolve) => {
        const check = setInterval(() => {
          if (window.grecaptcha && window.grecaptcha.ready) {
            clearInterval(check)
            window.grecaptcha.ready(() => {
              isLoaded.value = true
              isLoading.value = false
              resolve()
            })
          }
        }, 100)
      })
    }

    if (isLoading.value) {
      return new Promise((resolve) => {
        const check = setInterval(() => {
          if (isLoaded.value) {
            clearInterval(check)
            resolve()
          }
        }, 100)
      })
    }

    isLoading.value = true

    return new Promise((resolve, reject) => {
      const script = document.createElement('script')
      script.src = `https://www.google.com/recaptcha/api.js?render=${SITE_KEY}`
      script.async = true
      script.defer = true

      script.onload = () => {
        window.grecaptcha.ready(() => {
          isLoaded.value = true
          isLoading.value = false
          resolve()
        })
      }

      script.onerror = () => {
        isLoading.value = false
        reject(new Error('Failed to load reCAPTCHA script'))
      }

      document.head.appendChild(script)
    })
  }

  const getToken = async (action: string): Promise<string> => {
    await loadScript()
    return window.grecaptcha.execute(SITE_KEY, { action })
  }

  return {
    getToken,
    loadScript,
    isLoaded,
  }
}
