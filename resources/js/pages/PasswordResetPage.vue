<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import { useDashboardData } from '@/composables/useDashboardData'
import { useI18n } from '@/composables/useI18n'
import IconSpinner from '@/icons/IconSpinner.vue'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const { dashboardData } = useDashboardData()
const { t } = useI18n()

const isLoading = ref(false)
const isTokenLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const email = ref('')

const password = ref('')
const passwordConfirmation = ref('')
const token = ref('')
const tokenEmail = ref('')
const tokenValid = ref(false)

const navLinksList = dashboardData.value?.navLinks ?? []

const hasToken = computed(() => !!token.value)

onMounted(async () => {
  token.value = (route.params.token as string) || ''

  if (token.value) {
    isTokenLoading.value = true
    try {
      const response = await api.get(`/api/auth/reset-token/${token.value}`)
      tokenEmail.value = response.data.email
      tokenValid.value = true
    } catch (error: any) {
      if (error.response?.data?.message) {
        errorMessage.value = error.response.data.message
      } else {
        errorMessage.value = t('resetPassword.invalidLink')
      }
      tokenValid.value = false
    } finally {
      isTokenLoading.value = false
    }
  }
})

const handleForgotSubmit = async () => {
  if (!email.value) {
    errorMessage.value = t('forgotPassword.errorEmptyEmail')
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await api.post('/api/auth/forgot-password', {
      email: email.value,
    })
    successMessage.value = response.data.message
  } catch (error: any) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = t('common.error')
    }
  } finally {
    isLoading.value = false
  }
}

const handleResetSubmit = async () => {
  if (!password.value || !passwordConfirmation.value) {
    errorMessage.value = t('resetPassword.errorEmptyFields')
    return
  }

  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = t('resetPassword.errorPasswordMismatch')
    return
  }

  if (password.value.length < 8) {
    errorMessage.value = t('resetPassword.errorPasswordTooShort')
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await api.post('/api/auth/reset-password', {
      token: token.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    successMessage.value = response.data.message
  } catch (error: any) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const firstKey = Object.keys(errors)[0]
      errorMessage.value = errors[firstKey][0]
    } else {
      errorMessage.value = t('common.error')
    }
  } finally {
    isLoading.value = false
  }
}

const goToLogin = () => {
  router.push({ name: 'login' })
}

const requestNewLink = () => {
  token.value = ''
  tokenEmail.value = ''
  successMessage.value = ''
  errorMessage.value = ''
  router.push({ name: 'reset-password' })
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />

    <main class="flex flex-1 items-center justify-center px-6 py-10">
      <div class="w-full max-w-md space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-8 shadow-2xl shadow-black/20">
        <template v-if="!hasToken">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-textWhite">{{ t('forgotPassword.title') }}</h1>
            <p class="mt-2 text-sm text-textSecondary">{{ t('forgotPassword.subtitle') }}</p>
          </div>

          <form v-if="!successMessage" class="space-y-6" @submit.prevent="handleForgotSubmit">
            <div class="space-y-2">
              <label for="email" class="text-sm font-semibold text-textWhite">{{ t('forgotPassword.emailLabel') }}</label>
              <input id="email" v-model="email" type="email" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('forgotPassword.emailPlaceholder')" />
            </div>

            <div v-if="errorMessage" class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400">
              {{ errorMessage }}
            </div>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70" :disabled="isLoading">
              <IconSpinner v-if="isLoading" class="h-5 w-5 animate-spin text-bgPrimary" />
              {{ isLoading ? t('forgotPassword.submitting') : t('forgotPassword.submitButton') }}
            </button>
          </form>

          <div v-else class="space-y-6">
            <div class="rounded-lg bg-green-500/10 p-4 text-sm text-green-400">
              {{ successMessage }}
            </div>
            <p class="text-center text-sm text-textSecondary">
              {{ t('forgotPassword.checkEmail') }}
            </p>
          </div>

          <div class="text-center text-sm text-textSecondary">
            {{ t('forgotPassword.rememberPassword') }}
            <RouterLink :to="{ name: 'login' }" class="font-semibold text-textPrimary hover:underline">{{ t('forgotPassword.loginLink') }}</RouterLink>
          </div>
        </template>

        <template v-else>
          <div v-if="isTokenLoading" class="text-center py-8">
            <IconSpinner class="h-8 w-8 animate-spin text-textPrimary mx-auto" />
            <p class="mt-4 text-sm text-textSecondary">Weryfikacja linku...</p>
          </div>

          <div v-else-if="!tokenValid && errorMessage" class="space-y-6">
            <div class="rounded-lg bg-red-500/10 p-4 text-sm text-red-400">
              {{ errorMessage }}
            </div>
            <RouterLink :to="{ name: 'reset-password' }" class="block text-center font-semibold text-textPrimary hover:underline">
              {{ t('resetPassword.requestNewLink') }}
            </RouterLink>
          </div>

          <template v-else-if="tokenValid">
            <div class="text-center">
              <h1 class="text-3xl font-bold text-textWhite">{{ t('resetPassword.title') }}</h1>
              <p class="mt-2 text-sm text-textSecondary">{{ t('resetPassword.subtitle') }}</p>
            </div>

            <form v-if="!successMessage" class="space-y-6" @submit.prevent="handleResetSubmit">
              <div class="space-y-4">
                <div class="space-y-2">
                  <label for="password" class="text-sm font-semibold text-textWhite">{{ t('resetPassword.passwordLabel') }}</label>
                  <input id="password" v-model="password" type="password" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('resetPassword.passwordPlaceholder')" />
                </div>

                <div class="space-y-2">
                  <label for="password_confirmation" class="text-sm font-semibold text-textWhite">{{ t('resetPassword.confirmPasswordLabel') }}</label>
                  <input id="password_confirmation" v-model="passwordConfirmation" type="password" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('resetPassword.confirmPasswordPlaceholder')" />
                </div>
              </div>

              <div v-if="errorMessage" class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400">
                {{ errorMessage }}
              </div>

              <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70" :disabled="isLoading">
                <IconSpinner v-if="isLoading" class="h-5 w-5 animate-spin text-bgPrimary" />
                {{ isLoading ? t('resetPassword.submitting') : t('resetPassword.submitButton') }}
              </button>
            </form>

            <div v-else class="space-y-6">
              <div class="rounded-lg bg-green-500/10 p-4 text-sm text-green-400">
                {{ successMessage }}
              </div>
              <button class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90" @click="goToLogin">
                {{ t('resetPassword.goToLogin') }}
              </button>
            </div>

            <div v-if="errorMessage && !successMessage" class="text-center">
              <button class="font-semibold text-textPrimary hover:underline text-sm" @click="requestNewLink">
                {{ t('resetPassword.requestNewLink') }}
              </button>
            </div>
          </template>
        </template>
      </div>
    </main>
  </div>
</template>
