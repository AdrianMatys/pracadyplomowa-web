<script setup lang="ts">
import type { FilterSection } from '@/constants/data'
import { useI18n } from '@/composables/useI18n'
import IconCheck from '@/icons/IconCheck.vue'

const props = defineProps<{
  sections: FilterSection[]
  selected: string[]
}>()

const emit = defineEmits<{
  (e: 'toggle', id: string): void
}>()

const { t } = useI18n()

const handleToggle = (id: string) => {
  emit('toggle', id)
}

const isSelected = (id: string) => props.selected.includes(id)
</script>

<template>
  <section class="w-full rounded-2xl border border-strokePrimary/30 bg-cardHover px-6 py-8 h-fit">
    <h2 class="mb-6 text-lg font-bold text-textPrimary">{{ t('filters.title') }}</h2>

    <div class="flex flex-col gap-8">
      <div v-for="(section, index) in props.sections" :key="section.id" class="flex flex-col">
        <div v-if="index > 0" class="mb-6 h-px w-full bg-strokePrimary/30"></div>

        <h3 class="mb-4 text-sm font-bold text-textWhite">
          {{ t(section.title) || section.title }}
        </h3>

        <div class="flex flex-col gap-3">
          <button v-for="option in section.options" :key="option.id" class="group flex items-center gap-3 text-left focus-visible:outline-none" @click="handleToggle(option.id)">
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-[4px] border transition-colors" :class="isSelected(option.id) ? 'bg-textSecondary border-textSecondary' : 'border-textSecondary/60 bg-transparent group-hover:border-textSecondary'">
              <IconCheck v-if="isSelected(option.id)" class="h-3.5 w-3.5 text-cardHover" />
            </div>

            <span class="text-sm font-bold text-textWhite transition-colors group-hover:text-textWhite/80">
              {{ option.label.includes('.') ? t(option.label) || option.label : option.label }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
