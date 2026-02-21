<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from '@/composables/useI18n'

const { t, currentLanguage } = useI18n()

const props = defineProps<{
  code: string
  previewType?: 'css' | 'js' | 'none'
}>()

const noPreviewMessage = computed(() => {
  return currentLanguage.value === 'pl' ? 'Podgląd wizualny nie jest dostępny dla tego zadania.<br>Sprawdź rozwiązanie używając przycisku "Sprawdź".' : 'Visual preview is not available for this task.<br>Check your solution using the "Check" button.'
})

const srcDoc = computed(() => {
  if (props.previewType === 'none' || !props.previewType) {
    return `
      <!DOCTYPE html>
      <html>
        <body style="margin:0;display:flex;justify-content:center;align-items:center;height:100vh;background-color:transparent;color:#64748b;font-family:sans-serif;text-align:center;font-size:0.875rem;">
          <p>${noPreviewMessage.value}</p>
        </body>
      </html>
    `
  }

  const styles = props.previewType === 'css' ? props.code : ''
  const scripts = props.previewType === 'js' ? props.code : ''

  return `
  <!DOCTYPE html>
  <html>
    <head>
      <style>
        body {
          margin: 0;
          padding: 1rem;
          font-family: sans-serif;
          color: #ffffff;
          background-color: transparent;
          display: flex;
          justify-content: center;
          align-items: center;
          min-height: 100vh;
        }
        .container {
          display: flex;
          gap: 1rem;
          background: rgba(255,255,255,0.1);
          padding: 1rem;
          border-radius: 0.5rem;
          width: 100%;
          max-width: 300px;
          height: 150px;
        }
        .item {
          width: 50px;
          height: 50px;
          background: #02B2D0;
          border-radius: 0.25rem;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: bold;
          transition: all 0.3s ease;
        }
      </style>
      <style>
        ${styles}
      </style>
    </head>
    <body>
      <div class="container">
        <div class="item">1</div>
        <div class="item">2</div>
        <div class="item">3</div>
      </div>
      <script>
        window.onload = function() {
          try {
            ${scripts}
          } catch (e) {
            console.error(e);
          }
        }
      
      <\/script>
    </body>
  </html>
`
})
</script>

<template>
  <div class="flex h-full flex-col overflow-hidden rounded-xl border border-strokePrimary/30 bg-card">
    <div class="border-b border-strokePrimary/30 px-4 py-2">
      <span class="text-xs font-semibold uppercase tracking-wider text-textSecondary">{{ t('lesson.preview') }}</span>
    </div>
    <iframe class="h-full w-full border-0 bg-transparent" :srcdoc="srcDoc" sandbox="allow-scripts" :title="t('lesson.preview')"></iframe>
  </div>
</template>
