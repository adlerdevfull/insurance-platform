<template>
  <div class="w-full" :class="compact ? 'w-auto' : ''">
    <p
      v-if="!compact"
      class="text-[10px] uppercase tracking-wide text-gray-400 mb-1"
    >
      {{ t('lang.label') }}
    </p>
    <div
      class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5"
      :class="compact ? '' : 'w-full'"
    >
      <button
        v-for="opt in available"
        :key="opt.code"
        type="button"
        @click="setLocale(opt.code)"
        class="px-2.5 py-1.5 text-xs font-bold rounded-md transition"
        :class="[
          compact ? '' : 'flex-1',
          locale === opt.code
            ? 'bg-blue-600 text-white shadow'
            : 'text-gray-500 hover:text-gray-900 hover:bg-white',
        ]"
        :title="t(`lang.${opt.code}`)"
      >
        {{ opt.label }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from '../composables/useI18n'

defineProps<{ compact?: boolean }>()

const { locale, setLocale, available, t } = useI18n()
</script>
