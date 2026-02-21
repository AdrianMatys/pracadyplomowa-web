<script setup lang="ts">
import { RouterView } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
</script>

<template>
  <div class="min-h-screen bg-bgPrimary text-textWhite flex flex-col">
    <MainHeader :is-admin-mode="true" />

    <main class="flex-1 container mx-auto p-6 md:p-8">
      <RouterView />
    </main>
  </div>
</template>
