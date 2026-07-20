<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold">{{ t('policies.title') }}</h2>
      <button
        @click="showForm = !showForm"
        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm"
      >
        {{ t('policies.new') }}
      </button>
    </div>

    <div v-if="showForm" class="bg-white rounded-xl shadow-sm p-6 mb-6">
      <h3 class="font-semibold mb-4">{{ t('policies.newTitle') }}</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('policies.insuredName') }}</label>
          <input v-model="form.insured_name" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('policies.document') }}</label>
          <input v-model="form.insured_document" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('policies.riskType') }}</label>
          <select v-model="form.risk_type" class="w-full border rounded-lg px-3 py-2 text-sm">
            <option value="low">{{ t('policies.riskLow') }}</option>
            <option value="medium">{{ t('policies.riskMedium') }}</option>
            <option value="high">{{ t('policies.riskHigh') }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('policies.basePremium') }}</label>
          <input
            v-model.number="form.base_premium_eur"
            type="number"
            step="0.01"
            class="w-full border rounded-lg px-3 py-2 text-sm"
          />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('policies.startsAt') }}</label>
          <input v-model="form.starts_at" type="date" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('policies.expiresAt') }}</label>
          <input v-model="form.expires_at" type="date" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
      </div>
      <div class="flex gap-2 mt-4">
        <button @click="createPolicy" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
          {{ t('policies.create') }}
        </button>
        <button @click="showForm = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">
          {{ t('policies.cancel') }}
        </button>
      </div>
    </div>

    <div class="space-y-3">
      <div v-for="p in list" :key="p.id" class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-semibold">{{ p.policy_number }}</p>
            <p class="text-sm text-gray-500">{{ p.insured_name }} · {{ p.insured_document }}</p>
          </div>
          <div class="flex items-center gap-3">
            <span :class="`text-xs px-3 py-1 rounded-full font-medium ${statusColor(p.status)}`">{{
              p.status
            }}</span>
            <div class="text-right">
              <p class="font-bold">€{{ p.premium?.toFixed(2) }}</p>
              <p class="text-xs text-gray-400">{{ t('policies.risk') }}: {{ p.risk_type }}</p>
            </div>
          </div>
        </div>
        <div v-if="transitions[p.status]" class="mt-3 pt-3 border-t flex gap-2">
          <button
            v-for="s in transitions[p.status]"
            :key="s"
            @click="doTransition(p.id, s)"
            class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition"
          >
            → {{ s }}
          </button>
        </div>
      </div>
    </div>
    <p v-if="list.length === 0" class="text-center py-12 text-gray-400">{{ t('policies.empty') }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { policies } from '../services/api'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()
const list = ref<any[]>([])
const showForm = ref(false)
const form = ref({
  insured_name: '',
  insured_document: '',
  risk_type: 'low',
  base_premium_eur: 500,
  starts_at: '',
  expires_at: '',
})

const transitions: Record<string, string[]> = {
  draft: ['active', 'cancelled'],
  active: ['suspended', 'cancelled', 'expired'],
  suspended: ['active', 'cancelled'],
}

const statusColor = (s: string) =>
  ({
    draft: 'bg-gray-100 text-gray-700',
    active: 'bg-green-100 text-green-700',
    suspended: 'bg-yellow-100 text-yellow-700',
    cancelled: 'bg-red-100 text-red-700',
    expired: 'bg-gray-100 text-gray-500',
  }[s] || 'bg-gray-100 text-gray-700')

const load = async () => {
  try {
    list.value = (await policies.list()).data.data || []
  } catch {}
}

onMounted(load)

const createPolicy = async () => {
  await policies.create({
    ...form.value,
    base_premium_cents: Math.round(form.value.base_premium_eur * 100),
  })
  showForm.value = false
  load()
}

const doTransition = async (id: number, status: string) => {
  await policies.transition(id, status)
  load()
}
</script>
