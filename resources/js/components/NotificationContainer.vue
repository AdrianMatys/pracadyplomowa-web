<script setup lang="ts">
import { useNotifications } from '@/composables/useNotifications'
import NotificationToast from './NotificationToast.vue'

const { notifications, remove } = useNotifications()
</script>

<template>
  <div class="fixed top-4 right-4 z-50 flex w-full max-w-sm flex-col gap-2 pointer-events-none p-4 md:p-0">
    <TransitionGroup enter-active-class="transition duration-300 ease-out" enter-from-class="-translate-y-2 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="translate-x-full opacity-0">
      <NotificationToast v-for="notification in notifications" :key="notification.id" :notification="notification" @close="remove(notification.id)" />
    </TransitionGroup>
  </div>
</template>
