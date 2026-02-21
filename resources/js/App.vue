<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useI18n } from '@/composables/useI18n'
import NotificationContainer from '@/components/NotificationContainer.vue'

const { initAuth } = useAuth()
const { initLanguage } = useI18n()

onMounted(() => {
  initAuth()
  initLanguage()
})
</script>

<template>
  <NotificationContainer />
  <RouterView />
</template>
