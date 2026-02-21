<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
import { useDashboardData } from '@/composables/useDashboardData'
import { useProfileData } from '@/composables/useProfileData'
import { useAuth } from '@/composables/useAuth'
import { useI18n } from '@/composables/useI18n'
import { useNotifications } from '@/composables/useNotifications'
import IconArrowLeft from '@/icons/IconArrowLeft.vue'
import IconCheck from '@/icons/IconCheck.vue'
import api from '@/services/api'

const router = useRouter()
const { dashboardData, loadDashboardData } = useDashboardData()
const { profileData, loadProfileData, refreshProfileData } = useProfileData()
const { t } = useI18n()
const { notify } = useNotifications()
const { refreshUser } = useAuth()

const descriptionLimit = 280
const navLinksList = computed(() => dashboardData.value?.navLinks ?? [])
const profileSummary = computed(() => profileData.value?.summary)

const logoUrl = ref('')
const description = ref('')
const nickname = ref('')
const isSaving = ref(false)
const wasInitialized = ref(false)

const logoPreviewSource = computed(() => logoUrl.value.trim())
const descriptionCharactersUsed = computed(() => description.value.length)

const initialAvatarUrl = ref('')

const isLogoUrlValid = computed(() => {
  return true
})

const isDescriptionValid = computed(() => {
  return descriptionCharactersUsed.value <= descriptionLimit
})

const isSaveDisabled = computed(() => {
  if (isSaving.value) {
    return true
  }

  if (!isLogoUrlValid.value) {
    return true
  }

  if (!isDescriptionValid.value) {
    return true
  }

  return false
})

onMounted(() => {
  loadDashboardData()
  loadProfileData()
})

watch(
  profileSummary,
  (summary) => {
    if (!summary || wasInitialized.value) {
      return
    }

    const newNickname = summary.nickname || ''
    const newDescription = summary.description || ''
    const newAvatarUrl = summary.avatarUrl || ''

    // Set current values
    logoUrl.value = newAvatarUrl
    description.value = newDescription
    nickname.value = newNickname
    logoUrl.value = newAvatarUrl
    description.value = newDescription
    nickname.value = newNickname

    // Set initial values for comparison
    initialAvatarUrl.value = newAvatarUrl

    wasInitialized.value = true
  },
  { immediate: true },
)

const handleBackToProfile = () => {
  router.push({ name: 'profile' })
}

const handleLogoUrlInput = (value: string) => {
  logoUrl.value = value
}

const handleClearLogoUrl = () => {
  if (!logoUrl.value) {
    return
  }

  logoUrl.value = ''
}

const handleDescriptionInput = (value: string) => {
  description.value = value
}

const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)

const triggerFileUpload = () => {
  fileInput.value?.click()
}

const handleFileChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  if (input.files && input.files[0]) {
    selectedFile.value = input.files[0]

    const reader = new FileReader()
    reader.onload = (e) => {
      logoUrl.value = e.target?.result as string
    }
    reader.readAsDataURL(input.files[0])
  }
}

const handleSaveSettings = async () => {
  if (isSaveDisabled.value) {
    return
  }

  isSaving.value = true

  try {
    const formData = new FormData()
    formData.append('_method', 'PUT')
    if (nickname.value.trim()) formData.append('nickname', nickname.value.trim())
    formData.append('bio', description.value.trim())

    if (selectedFile.value) {
      formData.append('avatar', selectedFile.value)
    } else if (logoUrl.value && logoUrl.value !== initialAvatarUrl.value) {
      formData.append('avatar_url', logoUrl.value.trim())
    }

    if (selectedFile.value) {
      formData.append('avatar', selectedFile.value)
    } else if (logoUrl.value && logoUrl.value !== initialAvatarUrl.value) {
      formData.append('avatar_url', logoUrl.value.trim())
    }

    await api.post('/api/users/me', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    notify('success', t('settings.saveSuccess'))

    await refreshProfileData()
    await refreshUser()

    if (profileSummary.value) {
      const newNickname = profileSummary.value.nickname || ''
      const newDescription = profileSummary.value.description || ''
      const newAvatarUrl = profileSummary.value.avatarUrl || ''

      initialAvatarUrl.value = newAvatarUrl
      logoUrl.value = newAvatarUrl
      description.value = newDescription
      nickname.value = newNickname
    }

    selectedFile.value = null
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  } catch {
    notify('error', t('common.error'))
  } finally {
    isSaving.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-bgPrimary text-textWhite">
    <MainHeader :nav-links="navLinksList" />
    <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-8 px-6 py-10">
      <section class="flex flex-col gap-4 rounded-2xl border border-strokePrimary/30 bg-card p-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="space-y-2">
          <p class="text-xs uppercase tracking-[0.3em] text-textSecondary">
            {{ t('settings.settingsPanel') }}
          </p>
          <h1 class="text-3xl font-bold text-textWhite">{{ t('settings.title') }}</h1>
          <p class="text-sm text-textSecondary">
            {{ t('settings.description') }}
          </p>
        </div>
        <button class="inline-flex items-center gap-2 rounded-full border border-strokePrimary/40 px-5 py-2 text-sm font-semibold text-textSecondary transition hover:border-textPrimary hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('settings.backToProfile')" tabindex="0" @click="handleBackToProfile">
          <IconArrowLeft class="h-4 w-4" />
          {{ t('settings.backToProfile') }}
        </button>
      </section>

      <section class="grid gap-6 lg:grid-cols-[1fr_1.2fr]">
        <article class="space-y-5 rounded-2xl border border-strokePrimary/30 bg-dialogBg p-6 shadow-2xl shadow-black/20">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold text-textWhite">
                {{ t('settings.profilePreview') }}
              </h2>
              <p class="text-sm text-textSecondary">{{ t('settings.previewDescription') }}</p>
            </div>
            <span class="rounded-full border border-textSecondary/30 px-3 py-1 text-xs uppercase tracking-[0.3em] text-textSecondary">
              {{ t('settings.live') }}
            </span>
          </div>

          <div class="flex flex-col gap-4 rounded-2xl border border-strokePrimary/40 bg-inputBg p-6">
            <div class="flex items-center gap-4">
              <div class="relative flex items-center justify-center">
                <div class="flex aspect-square w-40 items-center justify-center overflow-hidden rounded-full border border-textPrimary/30 bg-gradient-to-b from-textPrimary/20 to-transparent" :aria-label="t('settings.logoPreview')">
                  <img v-if="logoPreviewSource" :src="logoPreviewSource" :alt="t('settings.userLogo')" class="h-full w-full select-none object-cover" draggable="false" />
                  <span v-else class="text-3xl font-bold uppercase text-textPrimary"> ? </span>
                </div>
              </div>
              <div class="space-y-2">
                <p class="text-sm text-textSecondary">{{ t('settings.yourDescription') }}</p>
                <p class="text-base text-textWhite">
                  {{ description || t('settings.descriptionPlaceholder') }}
                </p>
              </div>
            </div>
          </div>
        </article>

        <form class="space-y-6 rounded-2xl border border-strokePrimary/30 bg-dialogBg p-6 shadow-2xl shadow-black/20" :aria-label="t('settings.formLabel')" @submit.prevent="handleSaveSettings">
          <div class="space-y-3">
            <label class="text-sm font-semibold text-textWhite" for="logo-url-input">{{ t('settings.logoUrl') }}</label>
            <input id="logo-url-input" ref="fileInput" class="hidden" type="file" accept="image/*" @change="handleFileChange" />

            <div class="flex gap-2">
              <input
                class="w-full rounded-2xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary"
                type="text"
                :aria-label="t('settings.logoUrl')"
                placeholder="https://imgur.com/..."
                tabindex="0"
                :value="logoUrl"
                @input="handleLogoUrlInput(($event.target as HTMLInputElement).value)"
              />
              <button type="button" class="shrink-0 rounded-2xl bg-cardHover px-4 font-bold text-textPrimary hover:bg-cardHover/80" @click="triggerFileUpload">Upload</button>
            </div>

            <p class="text-xs text-textSecondary">
              {{ t('settings.logoHint') }}
            </p>
            <p v-if="logoUrl && !isLogoUrlValid" class="text-xs text-red-400">
              {{ t('settings.invalidUrl') }}
            </p>
            <button type="button" class="inline-flex items-center gap-2 rounded-full border border-strokePrimary/40 px-4 py-2 text-xs font-semibold text-textSecondary transition hover:border-textPrimary hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('settings.clearUrl')" tabindex="0" @click="handleClearLogoUrl">
              {{ t('settings.clearUrl') }}
            </button>
          </div>

          <div class="space-y-3">
            <label class="text-sm font-semibold text-textWhite" for="nickname-input">{{ t('settings.nickname') }}</label>
            <input
              id="nickname-input"
              class="w-full rounded-2xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary"
              type="text"
              :aria-label="t('settings.nickname')"
              :placeholder="t('settings.nicknamePlaceholder')"
              tabindex="0"
              :value="nickname"
              @input="nickname = ($event.target as HTMLInputElement).value"
            />
            <p class="text-xs text-textSecondary">
              {{ t('settings.nicknameHint') }}
            </p>
          </div>

          <div class="space-y-3">
            <label class="text-sm font-semibold text-textWhite" for="description-input">{{ t('settings.profileDescription') }}</label>
            <textarea
              id="description-input"
              class="h-40 w-full resize-none rounded-2xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary"
              :aria-label="t('settings.profileDescription')"
              :maxlength="descriptionLimit"
              :placeholder="t('settings.descriptionInputPlaceholder')"
              tabindex="0"
              :value="description"
              @input="handleDescriptionInput(($event.target as HTMLTextAreaElement).value)"
            ></textarea>
            <div class="flex items-center justify-between text-xs">
              <span :class="descriptionCharactersUsed > descriptionLimit ? 'text-red-400' : 'text-textSecondary'">
                {{ descriptionCharactersUsed }}/{{ descriptionLimit }}
                {{ t('settings.characters') }}
              </span>
              <span class="text-textSecondary">{{ t('settings.friendlyMessage') }}</span>
            </div>
            <p v-if="!isDescriptionValid" class="text-xs text-red-400">
              {{ t('settings.descriptionError') }}
            </p>
          </div>

          <div class="space-y-3">
            <button
              type="submit"
              class="flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:bg-textPrimary/40 disabled:text-bgPrimary/70"
              :aria-label="t('settings.saveSettings')"
              tabindex="0"
              :disabled="isSaveDisabled"
            >
              <IconCheck class="h-4 w-4" />
              {{ isSaving ? t('settings.saving') : t('settings.saveSettings') }}
            </button>
          </div>
        </form>
      </section>
    </main>
  </div>
</template>
