<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from '@/composables/useI18n'
import IconChevronDown from '@/icons/IconChevronDown.vue'

const { currentLanguage, toggleLanguage, isPolish, t } = useI18n()
const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const selectLanguage = () => {
  toggleLanguage()
  isOpen.value = false
}

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div ref="dropdownRef" class="relative">
    <button class="group relative flex items-center gap-2 rounded-full border border-strokePrimary/40 bg-bgSecondary/60 px-3 py-2 text-xs font-semibold transition-all hover:border-textPrimary/50 hover:bg-bgSecondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary" :aria-label="t('language.switchTo')" :aria-expanded="isOpen" @click="toggleDropdown">
      <span class="relative flex items-center justify-center">
        <span v-if="isPolish" class="flex h-5 w-5 items-center justify-center overflow-hidden rounded-full border border-white/20 shadow-sm">
          <span class="absolute inset-0 bg-white"></span>
          <span class="absolute bottom-0 h-1/2 w-full bg-[#DC143C]"></span>
        </span>

        <span v-else class="flex h-5 w-5 items-center justify-center overflow-hidden rounded-full border border-white/20 shadow-sm">
          <span class="absolute inset-0 bg-[#012169]"></span>
          <span class="absolute h-[2px] w-full rotate-45 bg-white"></span>
          <span class="absolute h-[2px] w-full -rotate-45 bg-white"></span>
          <span class="absolute h-[1px] w-full rotate-45 bg-[#C8102E]"></span>
          <span class="absolute h-[1px] w-full -rotate-45 bg-[#C8102E]"></span>
          <span class="absolute h-full w-[3px] bg-white"></span>
          <span class="absolute h-[3px] w-full bg-white"></span>
          <span class="absolute h-full w-[1.5px] bg-[#C8102E]"></span>
          <span class="absolute h-[1.5px] w-full bg-[#C8102E]"></span>
        </span>
      </span>

      <span class="uppercase tracking-wider text-textWhite transition-colors group-hover:text-textPrimary">
        {{ currentLanguage }}
      </span>

      <IconChevronDown class="h-3 w-3 text-textSecondary transition-transform duration-200 group-hover:text-textPrimary" :class="{ 'rotate-180': isOpen }" />
    </button>

    <div v-if="isOpen" class="absolute right-0 top-full z-50 mt-2 w-32 origin-top-right rounded-xl border border-strokePrimary/40 bg-bgPrimary p-1 shadow-xl shadow-black/40 backdrop-blur-sm transition-all">
      <button class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs font-medium text-textWhite transition hover:bg-bgSecondary hover:text-textPrimary" @click="selectLanguage">
        <span class="relative flex items-center justify-center">
          <span v-if="!isPolish" class="flex h-4 w-4 items-center justify-center overflow-hidden rounded-full border border-white/20 shadow-sm">
            <span class="absolute inset-0 bg-white"></span>
            <span class="absolute bottom-0 h-1/2 w-full bg-[#DC143C]"></span>
          </span>

          <span v-else class="flex h-4 w-4 items-center justify-center overflow-hidden rounded-full border border-white/20 shadow-sm">
            <span class="absolute inset-0 bg-[#012169]"></span>
            <span class="absolute h-[2px] w-full rotate-45 bg-white"></span>
            <span class="absolute h-[2px] w-full -rotate-45 bg-white"></span>
            <span class="absolute h-[1px] w-full rotate-45 bg-[#C8102E]"></span>
            <span class="absolute h-[1px] w-full -rotate-45 bg-[#C8102E]"></span>
            <span class="absolute h-full w-[3px] bg-white"></span>
            <span class="absolute h-[3px] w-full bg-white"></span>
            <span class="absolute h-full w-[1.5px] bg-[#C8102E]"></span>
            <span class="absolute h-[1.5px] w-full bg-[#C8102E]"></span>
          </span>
        </span>
        <span class="uppercase">{{ isPolish ? 'en' : 'pl' }}</span>
      </button>
    </div>
  </div>
</template>
