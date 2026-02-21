<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'
import { useNotifications } from '@/composables/useNotifications'
import { useAuth } from '@/composables/useAuth'
import { useI18n } from '@/composables/useI18n'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import IconDotsVertical from '@/icons/IconDotsVertical.vue'

const { notify } = useNotifications()
const { user: currentUser, refreshUser } = useAuth()
const { t } = useI18n()

const openDropdown = ref<number | null>(null)

const toggleDropdown = (userId: number) => {
  openDropdown.value = openDropdown.value === userId ? null : userId
}

const closeDropdown = () => {
  openDropdown.value = null
}

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('.actions-dropdown')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

type User = {
  id: number
  email: string
  created_at: string
  is_banned: boolean
  isAdmin: boolean
  level: 'junior' | 'mid' | 'senior'
  profile?: {
    nickname: string
    experience_points?: number
  }
}

const users = ref<User[]>([])
const isLoading = ref(true)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)

const editedLevels = ref<Record<number, string>>({})

const editedXP = ref<Record<number, number>>({})

const showDeleteModal = ref(false)
const showBanModal = ref(false)
const userToDelete = ref<User | null>(null)
const userToBan = ref<User | null>(null)

const fetchUsers = async (page = 1) => {
  isLoading.value = true
  try {
    const response = await api.get(`/api/admin/users?page=${page}&search=${search.value}`)

    if (response.data && response.data.data) {
      users.value = response.data.data
      currentPage.value = response.data.current_page
      lastPage.value = response.data.last_page

      editedLevels.value = {}
    } else {
      users.value = []
    }
  } catch {
    notify('error', t('admin.users.notifications.fetchError'))
  } finally {
    isLoading.value = false
  }
}

const onLevelChange = (userId: number, newLevel: string) => {
  editedLevels.value[userId] = newLevel
}

const saveLevel = async (user: User) => {
  const newLevel = editedLevels.value[user.id]
  if (!newLevel) return

  try {
    const response = await api.patch(`/api/admin/users/${user.id}/level`, { level: newLevel })
    user.level = response.data.user.level
    delete editedLevels.value[user.id]
    notify('success', t('admin.users.notifications.levelUpdated'))

    if (user.id === currentUser.value?.id) {
      await refreshUser()
    }
  } catch {
    notify('error', t('admin.users.notifications.levelError'))
  }
}

const hasLevelChanged = (user: User) => {
  return editedLevels.value[user.id] !== undefined && editedLevels.value[user.id] !== user.level
}

const onXPChange = (userId: number, newXP: number) => {
  editedXP.value[userId] = newXP
}

const saveXP = async (user: User) => {
  const newXP = editedXP.value[user.id]
  if (newXP === undefined) return

  try {
    const response = await api.patch(`/api/admin/users/${user.id}/xp`, { experience_points: newXP })
    if (user.profile) {
      user.profile.experience_points = newXP
    }

    if (response.data.user && response.data.user.level) {
      user.level = response.data.user.level
      if (editedLevels.value[user.id] === user.level) {
        delete editedLevels.value[user.id]
      }
    }

    delete editedXP.value[user.id]
    notify('success', 'XP zaktualizowane')

    if (user.id === currentUser.value?.id) {
      await refreshUser()
    }
  } catch {
    notify('error', 'Błąd aktualizacji XP')
  }
}

const hasXPChanged = (user: User) => {
  return editedXP.value[user.id] !== undefined && editedXP.value[user.id] !== (user.profile?.experience_points ?? 0)
}

const confirmBan = (user: User) => {
  if (user.id === currentUser.value?.id) {
    notify('error', t('admin.users.notifications.banSelf'))
    return
  }
  userToBan.value = user
  showBanModal.value = true
}

const toggleBan = async () => {
  if (!userToBan.value) return

  try {
    const response = await api.patch(`/api/admin/users/${userToBan.value.id}/ban`)
    userToBan.value.is_banned = response.data.user.is_banned
    notify('success', userToBan.value.is_banned ? t('admin.users.notifications.bannedSuccess') : t('admin.users.notifications.unbannedSuccess'))
  } catch {
    notify('error', t('admin.users.notifications.statusError'))
  }
}

const confirmDelete = (user: User) => {
  if (user.id === currentUser.value?.id) {
    notify('error', t('admin.users.notifications.deleteSelf'))
    return
  }
  userToDelete.value = user
  showDeleteModal.value = true
}

const deleteUser = async () => {
  if (!userToDelete.value) return

  try {
    await api.delete(`/api/admin/users/${userToDelete.value.id}`)
    notify('success', t('admin.users.notifications.userDeleted'))
    fetchUsers(currentPage.value)
  } catch {
    notify('error', t('admin.users.notifications.deleteError'))
  }
}

onMounted(() => fetchUsers())
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-textWhite">{{ t('admin.users.title') }}</h1>
      <input v-model="search" type="text" :placeholder="t('admin.users.searchPlaceholder')" class="bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors w-64" @input="fetchUsers(1)" />
    </div>

    <div class="rounded-xl border border-strokePrimary/30 bg-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-bgSecondary text-textSecondary uppercase text-xs">
            <tr>
              <th class="px-4 py-4 font-semibold hidden sm:table-cell">
                {{ t('admin.common.id') }}
              </th>
              <th class="px-4 py-4 font-semibold">{{ t('admin.users.table.nickname') }}</th>
              <th class="px-4 py-4 font-semibold hidden lg:table-cell">Email</th>
              <th class="px-4 py-4 font-semibold hidden md:table-cell">
                {{ t('admin.users.table.level') || 'Level' }}
              </th>
              <th class="px-4 py-4 font-semibold hidden md:table-cell">XP</th>
              <th class="px-4 py-4 font-semibold hidden xl:table-cell">
                {{ t('admin.common.createdAt') }}
              </th>
              <th class="px-4 py-4 font-semibold">Status</th>
              <th class="px-4 py-4 font-semibold text-right">{{ t('admin.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-strokePrimary/20 text-sm">
            <tr v-if="isLoading">
              <td colspan="8" class="px-4 py-8 text-center text-textSecondary">
                {{ t('admin.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="users.length === 0">
              <td colspan="8" class="px-4 py-8 text-center text-textSecondary">
                {{ t('admin.users.noUsers') }}
              </td>
            </tr>
            <tr v-for="user in users" v-else :key="user.id" class="hover:bg-cardHover transition-colors">
              <td class="px-4 py-4 text-textSecondary font-mono hidden sm:table-cell">
                {{ user.id }}
              </td>
              <td class="px-4 py-4 font-medium text-textWhite">
                <div class="flex flex-col">
                  <span>
                    {{ user.profile?.nickname || t('admin.users.noNickname') }}
                    <span v-if="user.isAdmin" class="ml-1 px-1.5 py-0.5 bg-primary/20 text-primary text-[10px] rounded uppercase font-bold">Admin</span>
                  </span>
                  <span class="text-xs text-textSecondary lg:hidden">{{ user.email }}</span>
                </div>
              </td>
              <td class="px-4 py-4 text-textSecondary hidden lg:table-cell truncate max-w-[200px]">
                {{ user.email }}
              </td>
              <td class="px-4 py-4 hidden md:table-cell">
                <select :value="editedLevels[user.id] ?? user.level" class="bg-bgSecondary border border-strokePrimary/30 rounded px-2 py-1 text-textWhite text-xs focus:border-primary focus:outline-none w-full max-w-[100px]" @change="(e) => onLevelChange(user.id, (e.target as HTMLSelectElement).value)">
                  <option value="junior">Junior</option>
                  <option value="mid">Mid</option>
                  <option value="senior">Senior</option>
                </select>
              </td>
              <td class="px-4 py-4 hidden md:table-cell">
                <div class="flex items-center gap-1">
                  <input type="number" :value="editedXP[user.id] ?? user.profile?.experience_points ?? 0" class="bg-bgSecondary border border-strokePrimary/30 rounded px-2 py-1 text-textWhite text-xs focus:border-primary focus:outline-none w-16" @input="(e) => onXPChange(user.id, parseInt((e.target as HTMLInputElement).value) || 0)" />
                  <button v-if="hasXPChanged(user)" class="px-2 py-1 rounded bg-green-500/10 text-green-500 hover:bg-green-500/20 transition-colors text-xs font-bold shrink-0" @click="saveXP(user)">✓</button>
                </div>
              </td>
              <td class="px-4 py-4 text-textSecondary hidden xl:table-cell whitespace-nowrap">
                {{ new Date(user.created_at).toLocaleDateString() }}
              </td>
              <td class="px-4 py-4">
                <span :class="['px-2 py-1 rounded text-xs font-bold uppercase whitespace-nowrap', user.is_banned ? 'bg-red-500/10 text-red-500' : 'bg-green-500/10 text-green-500']">
                  {{ user.is_banned ? t('admin.users.banned') : t('admin.users.active') }}
                </span>
              </td>
              <td class="px-4 py-4 text-right">
                <div class="hidden lg:flex items-center justify-end gap-2">
                  <button :disabled="user.id === currentUser?.id" :class="['px-3 py-1.5 rounded transition-colors text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap', user.is_banned ? 'bg-green-500/10 text-green-500 hover:bg-green-500/20' : 'bg-amber-500/10 text-amber-500 hover:bg-amber-500/20']" @click="confirmBan(user)">
                    {{ user.is_banned ? t('admin.users.unban') : t('admin.users.ban') }}
                  </button>
                  <button v-if="hasLevelChanged(user)" class="px-3 py-1.5 rounded bg-blue-500/10 text-blue-500 hover:bg-blue-500/20 transition-colors text-xs font-bold whitespace-nowrap" @click="saveLevel(user)">
                    {{ t('admin.common.save') }}
                  </button>
                  <button :disabled="user.id === currentUser?.id" class="px-3 py-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap" @click="confirmDelete(user)">
                    {{ t('admin.common.delete') }}
                  </button>
                </div>

                <div class="lg:hidden relative actions-dropdown">
                  <button class="p-2 rounded bg-bgSecondary text-textSecondary hover:text-textWhite hover:bg-cardHover transition-colors" @click.stop="toggleDropdown(user.id)">
                    <IconDotsVertical class="h-5 w-5" />
                  </button>
                  <div v-if="openDropdown === user.id" class="absolute right-0 bottom-full mb-1 w-40 bg-bgPrimary border border-strokePrimary/30 rounded-lg shadow-xl z-20 py-1">
                    <button
                      :disabled="user.id === currentUser?.id"
                      :class="['w-full text-left px-4 py-2 text-xs font-bold transition-colors disabled:opacity-50', user.is_banned ? 'text-green-500 hover:bg-green-500/10' : 'text-amber-500 hover:bg-amber-500/10']"
                      @click="confirmBan(user); closeDropdown()"
                    >
                      {{ user.is_banned ? t('admin.users.unban') : t('admin.users.ban') }}
                    </button>
                    <button
                      v-if="hasLevelChanged(user)"
                      class="w-full text-left px-4 py-2 text-xs font-bold text-blue-500 hover:bg-blue-500/10 transition-colors"
                      @click="saveLevel(user); closeDropdown()"
                    >
                      {{ t('admin.common.save') }}
                    </button>
                    <button
                      :disabled="user.id === currentUser?.id"
                      class="w-full text-left px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-500/10 transition-colors disabled:opacity-50"
                      @click="confirmDelete(user); closeDropdown()"
                    >
                      {{ t('admin.common.delete') }}
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="lastPage > 1" class="flex justify-center gap-2 mt-4">
      <button v-for="page in lastPage" :key="page" :class="['w-8 h-8 rounded-lg text-xs font-bold transition-colors', currentPage === page ? 'bg-primary text-textWhite' : 'bg-bgSecondary text-textSecondary hover:bg-cardHover']" @click="fetchUsers(page)">
        {{ page }}
      </button>
    </div>

    <ConfirmationModal
      v-model:is-open="showDeleteModal"
      :title="t('admin.users.deleteModalTitle')"
      :message="
        t('admin.users.deleteModalMessage', {
          name: userToDelete?.profile?.nickname || userToDelete?.email || '',
        })
      "
      :confirm-text="t('admin.common.delete')"
      :cancel-text="t('admin.common.cancel')"
      is-danger
      @confirm="deleteUser"
    />

    <ConfirmationModal
      v-model:is-open="showBanModal"
      :title="userToBan?.is_banned ? t('admin.users.unbanModalTitle') : t('admin.users.banModalTitle')"
      :message="
        userToBan?.is_banned
          ? t('admin.users.unbanModalMessage', {
              name: userToBan?.profile?.nickname || userToBan?.email || '',
            })
          : t('admin.users.banModalMessage', {
              name: userToBan?.profile?.nickname || userToBan?.email || '',
            })
      "
      :confirm-text="userToBan?.is_banned ? t('admin.users.unban') : t('admin.users.ban')"
      :cancel-text="t('admin.common.cancel')"
      :is-danger="!userToBan?.is_banned"
      @confirm="toggleBan"
    />
  </div>
</template>
