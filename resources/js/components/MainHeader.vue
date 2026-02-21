<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import type { NavLink } from '@/constants/data'
import { useAuth } from '@/composables/useAuth'
import { useI18n } from '@/composables/useI18n'
import { useTheme } from '@/composables/useTheme'
import IconSettings from '@/icons/IconSettings.vue'
import IconUser from '@/icons/IconUser.vue'
import IconLogout from '@/icons/IconLogout.vue'
import IconUsers from '@/icons/IconUsers.vue'
import IconBook from '@/icons/IconBook.vue'
import IconDocument from '@/icons/IconDocument.vue'
import IconNewspaper from '@/icons/IconNewspaper.vue'
import { formatAvatarUrl } from '@/utils/formatters'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

withDefaults(
  defineProps<{
    navLinks?: NavLink[]
    isAdminMode?: boolean
  }>(),
  {
    navLinks: () => [],
    isAdminMode: false,
  },
)

const router = useRouter()
const route = useRoute()
const { isLoggedIn, user, logout } = useAuth()
const { t } = useI18n()
const { isDark, toggleTheme, initTheme } = useTheme()

const totalXP = computed(() => user.value?.profile?.experience_points ?? 0)
const userLevel = computed(() => Math.floor(totalXP.value / 1000) + 1)
const currentXP = computed(() => totalXP.value % 1000)
const xpProgress = computed(() => (currentXP.value / 1000) * 100)

onMounted(() => {
  initTheme()
})

const isSettingsOpen = ref(false)
const settingsMenuRef = ref<HTMLElement | null>(null)
const settingsButtonRef = ref<HTMLElement | null>(null)

const handleSettingsToggle = () => {
  isSettingsOpen.value = !isSettingsOpen.value
}

const handleThemeToggle = () => {
  toggleTheme()
}

const handleSettingsOption = (action: 'profile-settings' | 'logout' | 'admin-panel') => {
  if (action === 'profile-settings') {
    router.push({ name: 'profile-settings' })
  }

  if (action === 'logout') {
    logout()
  }

  if (action === 'admin-panel') {
    window.location.href = '/admin'
  }

  isSettingsOpen.value = false
}

const handleDocumentClick = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!isSettingsOpen.value) {
    return
  }

  if (settingsMenuRef.value?.contains(target) || settingsButtonRef.value?.contains(target)) {
    return
  }

  isSettingsOpen.value = false
}

onMounted(() => {
  document.addEventListener('click', handleDocumentClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick)
})
</script>

<template>
  <header class="bg-bgPrimary border-b border-strokePrimary/20">
    <div class="mx-auto flex h-16 sm:h-20 max-w-[1440px] items-center justify-between gap-3 sm:gap-8 px-4 sm:px-6">
      <RouterLink to="/" class="flex items-center gap-2 sm:gap-3 shrink-0 transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary rounded-lg px-1" :aria-label="t('header.goToHomepage')">
        <div class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded bg-textPrimary text-[10px] sm:text-xs font-black text-bgPrimary">MUC</div>
        <span class="text-lg font-bold text-textPrimary tracking-wide">MasterUrCode</span>
      </RouterLink>

      <div v-if="isAdminMode" class="hidden flex-1 min-[950px]:flex min-[950px]:items-center min-[950px]:gap-8 lg:gap-12">
        <nav class="flex items-center gap-6">
          <RouterLink to="/admin/users" active-class="text-textPrimary font-bold" class="text-textSecondary hover:text-textPrimary transition-colors font-medium relative group">
            {{ t('header.adminUsers') }}
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-textPrimary transition-all group-hover:w-full"></span>
          </RouterLink>
          <RouterLink to="/admin/courses" active-class="text-textPrimary font-bold" class="text-textSecondary hover:text-textPrimary transition-colors font-medium relative group">
            {{ t('header.adminCourses') }}
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-textPrimary transition-all group-hover:w-full"></span>
          </RouterLink>
          <RouterLink to="/admin/logs" active-class="text-textPrimary font-bold" class="text-textSecondary hover:text-textPrimary transition-colors font-medium relative group">
            {{ t('header.adminLogs') }}
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-textPrimary transition-all group-hover:w-full"></span>
          </RouterLink>
          <RouterLink to="/admin/news" active-class="text-textPrimary font-bold" class="text-textSecondary hover:text-textPrimary transition-colors font-medium relative group">
            {{ t('header.adminNews') }}
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-textPrimary transition-all group-hover:w-full"></span>
          </RouterLink>
        </nav>
      </div>

      <div v-if="isAdminMode" class="flex-1 min-[950px]:hidden"></div>

      <div v-else class="hidden flex-1 min-[950px]:flex max-w-2xl">
        <div class="w-full"></div>
      </div>

      <div class="flex items-center gap-2 sm:gap-4 shrink-0">
        <LanguageSwitcher />

        <template v-if="isLoggedIn">
          <RouterLink to="/profil" class="group flex items-center gap-2 sm:gap-3 transition hover:opacity-80">
            <div class="relative">
              <div class="h-9 w-9 sm:h-12 sm:w-12 overflow-hidden rounded-full border border-strokePrimary bg-bgSecondary">
                <img v-if="user?.profile?.avatar_url" :src="formatAvatarUrl(user.profile.avatar_url)" :alt="user?.profile?.nickname || 'Avatar'" class="h-full w-full object-cover" />
                <div v-else class="flex h-full w-full items-center justify-center text-textSecondary text-lg sm:text-xl font-bold group-hover:text-textWhite transition-colors">?</div>
              </div>
              <div class="absolute -bottom-1 -right-1 flex h-4 sm:h-5 min-w-4 sm:min-w-5 items-center justify-center rounded-full bg-textPrimary border-2 border-bgPrimary text-[8px] sm:text-[10px] font-bold text-black z-10 px-0.5 sm:px-1">
                {{ userLevel }}
              </div>
            </div>

            <div class="hidden flex-col sm:flex">
              <span class="text-sm font-bold text-textWhite group-hover:text-textPrimary transition-colors">{{ user?.profile?.nickname || user?.name || 'User' }}</span>
              <span class="text-xs text-textSecondary">{{ currentXP }}/1000 PD</span>
              <div class="mt-1 h-1.5 w-28 overflow-hidden rounded-full bg-bgSecondary">
                <div class="h-full bg-textPrimary transition-all" :style="{ width: `${xpProgress}%` }"></div>
              </div>
            </div>
          </RouterLink>

          <div class="relative">
            <button ref="settingsButtonRef" class="text-textPrimary hover:text-white transition-colors rounded-full border border-transparent px-2 py-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('header.openSettingsMenu')" :aria-expanded="isSettingsOpen" tabindex="0" @click="handleSettingsToggle">
              <IconSettings class="h-6 w-6" />
            </button>

            <div v-if="isSettingsOpen" ref="settingsMenuRef" class="absolute right-0 top-full z-50 mt-3 w-56 rounded-2xl border border-strokePrimary/40 bg-bgPrimary p-2 shadow-2xl shadow-black/40" role="menu" :aria-label="t('header.settingsMenuLabel')">
              <button class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-textWhite transition hover:bg-cardHover" role="menuitem" tabindex="0" @click="handleSettingsOption('profile-settings')">
                <IconUser class="h-4 w-4" />
                {{ t('header.profileSettings') }}
              </button>

              <div class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold text-textWhite transition hover:bg-cardHover cursor-pointer" role="menuitem" tabindex="0" @click="handleThemeToggle">
                <div class="flex items-center gap-2">
                  <div class="h-4 w-4 rounded-full border border-current flex items-center justify-center text-[10px]">
                    {{ isDark ? '?' : '?' }}
                  </div>
                  {{ t('header.theme') }}
                </div>

                <div class="relative h-6 w-11 rounded-full bg-bgSecondary border border-strokePrimary/30 transition-colors">
                  <div class="absolute top-1 left-1 h-4 w-4 rounded-full bg-textPrimary shadow-sm transition-transform duration-200 ease-in-out" :class="{ 'translate-x-5': !isDark, 'translate-x-0': isDark }"></div>
                </div>
              </div>

              <div v-if="user?.role === 'admin'" class="my-2 h-px bg-strokePrimary/30"></div>

              <button v-if="user?.role === 'admin'" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-textWhite transition hover:bg-cardHover" role="menuitem" tabindex="0" @click="handleSettingsOption('admin-panel')">
                <IconSettings class="h-4 w-4" />
                {{ t('header.adminPanel') }}
              </button>

              <div class="my-2 h-px bg-strokePrimary/30"></div>

              <button class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-red-300 transition hover:bg-red-500/10" role="menuitem" tabindex="0" @click="handleSettingsOption('logout')">
                <IconLogout class="h-4 w-4" />
                {{ t('header.logout') }}
              </button>
            </div>
          </div>
        </template>
        <template v-else>
          <button class="text-sm font-semibold text-textWhite transition hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary rounded-full px-3 py-2" :aria-label="t('header.login')" @click="router.push({ name: 'login' })">
            {{ t('header.login') }}
          </button>
          <button class="rounded-full bg-textPrimary px-5 py-2 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('header.register')" @click="router.push({ name: 'register' })">
            {{ t('header.register') }}
          </button>
        </template>
      </div>
    </div>
  </header>

  <nav v-if="isAdminMode" class="min-[950px]:hidden fixed bottom-0 left-0 right-0 bg-bgPrimary border-t border-strokePrimary/30 z-50">
    <div class="flex items-center justify-around h-16 px-2">
      <RouterLink to="/admin/users" class="flex flex-col items-center justify-center gap-1 flex-1 py-2 transition-colors" active-class="text-textPrimary" :class="{ 'text-textSecondary': route.path !== '/admin/users' }">
        <IconUsers class="h-6 w-6 mb-1" />
        <span class="text-[10px] font-medium">{{ t('header.adminUsers') }}</span>
      </RouterLink>
      <RouterLink to="/admin/courses" class="flex flex-col items-center justify-center gap-1 flex-1 py-2 transition-colors" active-class="text-textPrimary" :class="{ 'text-textSecondary': !route.path.startsWith('/admin/courses') }">
        <IconBook class="h-6 w-6 mb-1" />
        <span class="text-[10px] font-medium">{{ t('header.adminCourses') }}</span>
      </RouterLink>
      <RouterLink to="/admin/logs" class="flex flex-col items-center justify-center gap-1 flex-1 py-2 transition-colors" active-class="text-textPrimary" :class="{ 'text-textSecondary': route.path !== '/admin/logs' }">
        <IconDocument class="h-6 w-6 mb-1" />
        <span class="text-[10px] font-medium">{{ t('header.adminLogs') }}</span>
      </RouterLink>
      <RouterLink to="/admin/news" class="flex flex-col items-center justify-center gap-1 flex-1 py-2 transition-colors" active-class="text-textPrimary" :class="{ 'text-textSecondary': !route.path.startsWith('/admin/news') }">
        <IconNewspaper class="h-6 w-6 mb-1" />
        <span class="text-[10px] font-medium">{{ t('header.adminNews') }}</span>
      </RouterLink>
    </div>
  </nav>
</template>
