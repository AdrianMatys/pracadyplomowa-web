<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import { useAuth } from '@/composables/useAuth'
import { useDashboardData } from '@/composables/useDashboardData'
import { useI18n } from '@/composables/useI18n'
import { useRecaptcha } from '@/composables/useRecaptcha'
import IconSpinner from '@/icons/IconSpinner.vue'
import IconEye from '@/icons/IconEye.vue'
import IconEyeOff from '@/icons/IconEyeOff.vue'

const router = useRouter()
const { register } = useAuth()
const { dashboardData } = useDashboardData()
const { t } = useI18n()
const { getToken, loadScript } = useRecaptcha()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')
const registrationSuccess = ref(false)

const navLinksList = dashboardData.value?.navLinks ?? []

onMounted(() => {
  loadScript()
})

const handleRegisterSubmit = async () => {
  if (!name.value || !email.value || !password.value || !confirmPassword.value) {
    errorMessage.value = t('register.errorEmptyFields')
    return
  }

  if (password.value !== confirmPassword.value) {
    errorMessage.value = t('register.errorPasswordMismatch')
    return
  }

  if (password.value.length < 8) {
    errorMessage.value = t('register.errorPasswordLength')
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const recaptchaToken = await getToken('register')

    await register({
      name: name.value,
      email: email.value,
      password: password.value,
      recaptcha_token: recaptchaToken,
    })
    registrationSuccess.value = true
  } catch {
    errorMessage.value = t('common.error')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />

    <main class="flex flex-1 items-center justify-center px-6 py-10">
      <div class="w-full max-w-md space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-8 shadow-2xl shadow-black/20">
        <template v-if="!registrationSuccess">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-textWhite">{{ t('register.title') }}</h1>
            <p class="mt-2 text-sm text-textSecondary">{{ t('register.subtitle') }}</p>
          </div>

          <form class="space-y-5" @submit.prevent="handleRegisterSubmit">
            <div class="space-y-2">
              <label for="name" class="text-sm font-semibold text-textWhite">{{ t('register.nameLabel') }}</label>
              <input id="name" v-model="name" type="text" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('register.namePlaceholder')" />
            </div>

            <div class="space-y-2">
              <label for="email" class="text-sm font-semibold text-textWhite">{{ t('register.emailLabel') }}</label>
              <input id="email" v-model="email" type="email" required class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('register.emailPlaceholder')" />
            </div>

            <div class="space-y-2">
              <label for="password" class="text-sm font-semibold text-textWhite">{{ t('register.passwordLabel') }}</label>
              <div class="relative">
                <input id="password" v-model="password" :type="isPasswordVisible ? 'text' : 'password'" required minlength="8" class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 pr-12 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary" :placeholder="t('register.passwordPlaceholder')" />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-textSecondary hover:text-textWhite focus:outline-none" @click="isPasswordVisible = !isPasswordVisible">
                  <IconEyeOff v-if="isPasswordVisible" class="h-5 w-5" />
                  <IconEye v-else class="h-5 w-5" />
                </button>
              </div>
            </div>

            <div class="space-y-2">
              <label for="confirmPassword" class="text-sm font-semibold text-textWhite">{{ t('register.confirmPasswordLabel') }}</label>
              <div class="relative">
                <input
                  id="confirmPassword"
                  v-model="confirmPassword"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  required
                  class="w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 pr-12 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary"
                  :placeholder="t('register.confirmPasswordPlaceholder')"
                />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-textSecondary hover:text-textWhite focus:outline-none" @click="isConfirmPasswordVisible = !isConfirmPasswordVisible">
                  <IconEyeOff v-if="isConfirmPasswordVisible" class="h-5 w-5" />
                  <IconEye v-else class="h-5 w-5" />
                </button>
              </div>
            </div>

            <div v-if="errorMessage" class="rounded-lg bg-red-500/10 p-3 text-sm text-red-400">
              {{ errorMessage }}
            </div>

            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70" :disabled="isLoading">
              <IconSpinner v-if="isLoading" class="h-5 w-5 animate-spin text-bgPrimary" />
              {{ isLoading ? t('register.submitting') : t('register.submitButton') }}
            </button>
          </form>

          <div class="text-center text-sm text-textSecondary">
            {{ t('register.hasAccount') }}
            <RouterLink :to="{ name: 'login' }" class="font-semibold text-textPrimary hover:underline">
              {{ t('register.loginLink') }}
            </RouterLink>
          </div>
        </template>
        <template v-else>
          <div class="text-center space-y-6">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-500/20 text-green-500">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-8 w-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-bold tracking-tight text-white mb-2">{{ t('register.successTitle') || 'Sprawdź swoją skrzynkę' }}</h2>
              <p class="text-textSecondary">{{ t('register.successMessage') || 'Wysłaliśmy link weryfikacyjny na Twój adres e-mail. Kliknij go, aby aktywować konto i móc się zalogować.' }}</p>
            </div>
            
            <div class="pt-4 border-t border-strokePrimary/30">
              <RouterLink :to="{ name: 'login' }" class="flex w-full items-center justify-center rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90">
                Przejdź do logowania
              </RouterLink>
            </div>
          </div>
        </template>
      </div>
    </main>
  </div>
</template>
