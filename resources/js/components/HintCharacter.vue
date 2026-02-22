<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from '@/composables/useI18n'

const props = defineProps<{
  failedAttempts: number
  hint1?: string
  hint2?: string
  state?: 'idle' | 'checking' | 'hint'
}>()

const { t } = useI18n()
const isOpen = ref(false)

const characterImage = computed(() => {
  switch (props.state) {
    case 'checking':
      return '/images/char_checking.png'
    case 'hint':
      return '/images/char_hint.png'
    case 'idle':
    default:
      return '/images/char_neutral.png'
  }
})

const hasHint1 = computed(() => !!props.hint1)
const hasHint2 = computed(() => !!props.hint2)

const shouldShowHint1 = computed(() => props.failedAttempts >= 5 && hasHint1.value)
const shouldShowHint2 = computed(() => props.failedAttempts >= 10 && hasHint2.value)

const attemptsToHint1 = computed(() => Math.max(0, 5 - props.failedAttempts))
const attemptsToHint2 = computed(() => Math.max(0, 10 - props.failedAttempts))


const toggleOpen = () => {
  isOpen.value = !isOpen.value
}
</script>

<template>
  <div class="fixed bottom-1/4 right-0 z-50 flex flex-col items-end pointer-events-none">
    <div v-if="isOpen" class="animate-pop-in relative right-4 mb-4 mr-8 max-w-xs rounded-[2rem] border-2 border-black bg-white p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] pointer-events-auto">
      <div class="space-y-3 font-sans text-black">

        <div v-if="shouldShowHint1" class="space-y-1">
          <span class="text-xs font-black uppercase tracking-wider text-blue-600 block mb-1">
            {{ t('hint.hint1') }}
          </span>
          <p class="text-sm font-medium leading-relaxed">{{ hint1 }}</p>
        </div>

        <div v-if="shouldShowHint2" class="space-y-1 pt-3 border-t-2 border-dashed border-gray-200">
          <span class="text-xs font-black uppercase tracking-wider text-purple-600 block mb-1">
            {{ t('hint.hint2') }}
          </span>
          <p class="text-sm font-medium leading-relaxed">{{ hint2 }}</p>
        </div>

        <div v-if="!shouldShowHint1 && !shouldShowHint2" class="space-y-2">
          <p class="text-sm font-bold text-gray-700">💡 Masz problem z zadaniem?</p>
          <p class="text-sm text-gray-500">
            Po <strong>{{ attemptsToHint1 }}</strong> {{ attemptsToHint1 === 1 ? 'błędnej próbie' : 'błędnych próbach' }} odblokuję pierwszą wskazówkę.
          </p>
        </div>

        <p v-if="shouldShowHint1 && !shouldShowHint2 && hasHint2" class="text-xs font-semibold italic text-gray-400 pt-1 border-t border-gray-100">
          {{ t('hint.hint2AvailableIn', { count: attemptsToHint2 }) }}
        </p>
      </div>

      <div class="absolute -bottom-3 right-8 h-6 w-6 rotate-45 border-b-2 border-r-2 border-black bg-white"></div>
    </div>

    <div class="pointer-events-auto relative -mr-20 transition-transform duration-300 cursor-help" @mouseenter="isOpen = true" @mouseleave="isOpen = false">
      <button class="group relative h-56 w-56 focus:outline-none" @click="toggleOpen">
        <div class="h-full w-full" style="transform: scaleX(-1)">
          <img :src="characterImage" alt="Assistant" class="h-full w-full object-contain drop-shadow-xl animate-peek" style="transform-origin: bottom right" />
        </div>

        <span v-if="shouldShowHint1 && !isOpen" class="absolute left-6 top-4 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-red-500 text-sm font-bold text-white shadow-lg animate-bounce"> ! </span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.animate-peek {
  animation: peek 6s ease-in-out infinite;
}

@keyframes peek {
  0%,
  100% {
    transform: rotate(0deg) translateX(0);
  }
  50% {
    transform: rotate(-5deg) translateX(-5px);
  }
}

.animate-pop-in {
  animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popIn {
  from {
    opacity: 0;
    transform: scale(0.8) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
