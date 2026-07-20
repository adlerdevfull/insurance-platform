<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">{{ t('dashboard.title') }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div
        v-for="card in cards"
        :key="card.label"
        class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4"
      >
        <div :class="`${card.color} p-3 rounded-lg text-white text-xl`">{{ card.icon }}</div>
        <div>
          <p class="text-sm text-gray-500">{{ card.label }}</p>
          <p class="text-2xl font-bold">{{ card.value }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">{{ t('dashboard.policyFlow') }}</h3>
        <div class="flex flex-wrap gap-2 text-sm">
          <span v-for="(s, i) in policyFlow" :key="s" class="flex items-center gap-1">
            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full">{{ s }}</span>
            <span v-if="i < policyFlow.length - 1" class="text-gray-300">→</span>
          </span>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4">{{ t('dashboard.claimFlow') }}</h3>
        <div class="flex flex-wrap gap-2 text-sm">
          <span v-for="(s, i) in claimFlow" :key="s" class="flex items-center gap-1">
            <span class="px-3 py-1 bg-orange-50 text-orange-700 rounded-full">{{ s }}</span>
            <span v-if="i < claimFlow.length - 1" class="text-gray-300">→</span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { policies, claims } from '../services/api'
import { useI18n } from '../composables/useI18n'

const { t, locale } = useI18n()
const policyList = ref<any[]>([])
const claimList = ref<any[]>([])

onMounted(async () => {
  try {
    policyList.value = (await policies.list({ per_page: 100 })).data.data || []
  } catch {}
  try {
    claimList.value = (await claims.list({ per_page: 100 })).data.data || []
  } catch {}
})

const byStatus = (list: any[], s: string) => list.filter((i) => i.status === s).length

const cards = computed(() => {
  void locale.value
  return [
    {
      label: t('dashboard.activePolicies'),
      value: byStatus(policyList.value, 'active'),
      icon: '📋',
      color: 'bg-blue-500',
    },
    {
      label: t('dashboard.openClaims'),
      value: byStatus(claimList.value, 'reported'),
      icon: '⚠️',
      color: 'bg-orange-500',
    },
    {
      label: t('dashboard.underReview'),
      value: byStatus(claimList.value, 'under_review'),
      icon: '🔍',
      color: 'bg-yellow-500',
    },
    {
      label: t('dashboard.approved'),
      value: byStatus(claimList.value, 'approved'),
      icon: '✅',
      color: 'bg-green-500',
    },
  ]
})

const policyFlow = ['draft', 'active', 'suspended', 'cancelled / expired']
const claimFlow = ['reported', 'under_review', 'approved', 'paid / rejected']
</script>
