<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import { useAuth } from '@/composables/useAuth'
import { useDashboardData } from '@/composables/useDashboardData'
import { useI18n } from '@/composables/useI18n'
import { useRecaptcha } from '@/composables/useRecaptcha'
import IconSpinner from '@/icons/IconSpinner.vue'
import IconEye from '@/icons/IconEye.vue'
import IconEyeOff from '@/icons/IconEyeOff.vue'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const { login } = useAuth()
const { dashboardData } = useDashboardData()
const { t } = useI18n()
const { getToken, loadScript } = useRecaptcha()

const email = ref('')
const password = ref('')
const isPasswordVisible = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')
const emailUnverified = ref(false)
const emailVerified = ref(false)
const resendLoading = ref(false)
const resendSent = ref(false)

const navLinksList = dashboardData.value?.navLinks ?? []

onMounted(() => {
  loadScript()
  if (route.query.verified === '1') {
    emailVerified.value = true
  }
})

const handleLoginSubmit = async () => {
  if (!email.value || !password.value) {
    errorMessage.value = t('login.errorEmptyFields')
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  emailUnverified.value = false
  resendSent.value = false

  try {
    const recaptchaToken = await getToken('login')

    await login({
      email: email.value,
      password: password.value,
      recaptcha_token: recaptchaToken,
    })

    const redirectPath = (route.query.redirect as string) || '/'
    router.push(redirectPath)
  } catch (error: any) {
    if (error.response?.status === 403 && error.response.data?.email_unverified) {
      emailUnverified.value = true
      errorMessage.value = t('login.emailUnverifiedError')
    } else if (error.response?.status === 422) {
      const errors = error.response.data.errors
      if (errors) {
        const firstKey = Object.keys(errors)[0]
        errorMessage.value = errors[firstKey][0]
      } else {
        errorMessage.value = error.response.data.message || t('login.errorEmptyFields')
      }
    } else if (error.code === 'ERR_NETWORK') {
      errorMessage.value = t('common.connectionError') || 'Błąd połączenia z serwerem'
    } else {
      errorMessage.value = t('common.error') || 'Wystąpił błąd logowania'
    }
  } finally {
    isLoading.value = false
  }
}

const resendVerification = async () => {
  resendLoading.value = true
  try {
    await api.post('/api/auth/resend-verification', {
      email: email.value,
      password: password.value,
    })
    resendSent.value = true
    errorMessage.value = ''
    emailUnverified.value = false
  } catch {
    errorMessage.value = t('login.resendError')
  } finally {
    resendLoading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />

    <main class="flex flex-1 items-center justify-center px-6 py-10">
      <div class="w-full max-w-md space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-8 shadow-2xl shadow-black/20">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-textWhite">{{ t('login.title') }}</h1>
          <p class="mt-2 text-sm text-textSecondary">{{ t('login.subtitle') }}</p>
        </div>

        <form class="space-y-6" @submit.prevent="handleLoginSubmit">
          <div class="space-y-4">
            <div class="space-y-2">
              <label for="email" class="text-sm font-semibold text-textWhite">{{ t('login.emailLabel') }}</label>
              <input id="email" v-model="email" type="email" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('login.emailPlaceholder')" />
            </div>

            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-semibold text-textWhite">{{ t('login.passwordLabel') }}</label>
                <RouterLink :to="{ name: 'forgot-password' }" class="text-xs text-textPrimary hover:underline">{{ t('login.forgotPassword') }}</RouterLink>
              </div>
              <div class="relative">
                <input id="password" v-model="password" :type="isPasswordVisible ? 'text' : 'password'" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 pr-12 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('login.passwordPlaceholder')" />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-textSecondary hover:text-textWhite focus:outline-none" @click="isPasswordVisible = !isPasswordVisible">
                  <IconEyeOff v-if="isPasswordVisible" class="h-5 w-5" />
                  <IconEye v-else class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>

          <div v-if="emailVerified" class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 p-4 text-sm space-y-1">
            <p class="font-semibold text-emerald-400">{{ t('register.verifiedTitle') }}</p>
            <p class="text-emerald-300/80">{{ t('register.verifiedMessage') }}</p>
          </div>

          <div v-if="resendSent" class="rounded-lg bg-green-500/10 p-3 text-sm text-green-400">
            ✓ {{ t('login.resendSuccess') }}
          </div>

          <div v-else-if="errorMessage" class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400 space-y-2">
            <p>{{ errorMessage }}</p>
            <button
              v-if="emailUnverified"
              type="button"
              class="mt-1 flex items-center gap-2 text-xs font-semibold text-amber-400 hover:text-amber-300 transition-colors"
              :disabled="resendLoading"
              @click="resendVerification"
            >
               <IconSpinner v-if="resendLoading" class="h-3 w-3 animate-spin" />
              {{ resendLoading ? t('common.submitting') : t('login.resendLink') }}
            </button>
          </div>

          <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70" :disabled="isLoading">
            <IconSpinner v-if="isLoading" class="h-5 w-5 animate-spin text-bgPrimary" />
            {{ isLoading ? t('login.submitting') : t('login.submitButton') }}
          </button>
        </form>

        <div class="text-center text-sm text-textSecondary">
          {{ t('login.noAccount') }}
          <RouterLink :to="{ name: 'register' }" class="font-semibold text-textPrimary hover:underline">{{ t('login.registerLink') }}</RouterLink>
        </div>
      </div>
    </main>
  </div>
</template>
