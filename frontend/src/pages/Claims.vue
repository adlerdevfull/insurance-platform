<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold">{{ t('claims.title') }}</h2>
      <button
        @click="showForm = !showForm"
        class="flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition text-sm"
      >
        {{ t('claims.report') }}
      </button>
    </div>

    <div v-if="showForm" class="bg-white rounded-xl shadow-sm p-6 mb-6">
      <h3 class="font-semibold mb-4">{{ t('claims.reportTitle') }}</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('claims.policyId') }}</label>
          <input v-model.number="form.policy_id" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('claims.claimedAmount') }}</label>
          <input
            v-model.number="form.claimed_eur"
            type="number"
            step="0.01"
            class="w-full border rounded-lg px-3 py-2 text-sm"
          />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ t('claims.occurredAt') }}</label>
          <input v-model="form.occurred_at" type="date" class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>
        <div class="col-span-2">
          <label class="block text-xs text-gray-500 mb-1">{{ t('claims.description') }}</label>
          <textarea v-model="form.description" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
        </div>
      </div>
      <div v-if="formError" class="mt-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
        ⚠️ {{ formError }}
      </div>
      <div class="flex gap-2 mt-4">
        <button @click="reportClaim" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm">
          {{ t('claims.submit') }}
        </button>
        <button
          @click="closeForm"
          class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm"
        >
          {{ t('claims.cancel') }}
        </button>
      </div>
    </div>

    <div class="space-y-3">
      <div v-for="c in list" :key="c.id" class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-semibold">{{ c.claim_number }}</p>
            <p class="text-sm text-gray-500">
              {{ t('claims.policy') }} #{{ c.policy_id }} · {{ c.occurred_at }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ c.description }}</p>
          </div>
          <div class="flex items-center gap-3">
            <span :class="`text-xs px-3 py-1 rounded-full font-medium ${statusColor(c.status)}`">{{
              c.status
            }}</span>
            <div class="text-right">
              <p class="font-bold">€{{ c.claimed_amount?.toFixed(2) }}</p>
              <p v-if="c.approved_amount" class="text-xs text-green-600">
                {{ t('claims.approved') }}: €{{ c.approved_amount?.toFixed(2) }}
              </p>
            </div>
          </div>
        </div>
        <div v-if="c.status === 'under_review'" class="mt-3 pt-3 border-t flex gap-2">
          <button
            @click="reviewClaim(c.id, 'approve', c.claimed_amount)"
            class="text-xs bg-green-50 text-green-700 px-3 py-1.5 rounded-lg hover:bg-green-100 transition"
          >
            ✅ {{ t('claims.approve') }}
          </button>
          <button
            @click="reviewClaim(c.id, 'reject')"
            class="text-xs bg-red-50 text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-100 transition"
          >
            ❌ {{ t('claims.reject') }}
          </button>
        </div>
        <div v-if="c.status === 'reported'" class="mt-3 pt-3 border-t">
          <button
            @click="startReview(c.id)"
            class="text-xs bg-yellow-50 text-yellow-700 px-3 py-1.5 rounded-lg hover:bg-yellow-100 transition"
          >
            🔍 {{ t('claims.startReview') }}
          </button>
        </div>
        <p v-if="c.rejection_reason" class="mt-2 text-xs text-red-500">
          {{ t('claims.reason') }}: {{ c.rejection_reason }}
        </p>
      </div>
    </div>
    <p v-if="list.length === 0" class="text-center py-12 text-gray-400">{{ t('claims.empty') }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { claims } from '../services/api'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()
const list = ref<any[]>([])
const showForm = ref(false)
const formError = ref<string | null>(null)
const form = ref({ policy_id: 0, description: '', claimed_eur: 0, occurred_at: '' })

const statusColor = (s: string) =>
  ({
    reported: 'bg-orange-100 text-orange-700',
    under_review: 'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    paid: 'bg-blue-100 text-blue-700',
    rejected: 'bg-red-100 text-red-700',
  }[s] || 'bg-gray-100 text-gray-700')

const load = async () => {
  try {
    list.value = (await claims.list()).data.data || []
  } catch {}
}

onMounted(load)

const closeForm = () => {
  showForm.value = false
  formError.value = null
}

const reportClaim = async () => {
  formError.value = null
  try {
    await claims.create({
      ...form.value,
      claimed_amount_cents: Math.round(form.value.claimed_eur * 100),
    })
    showForm.value = false
    load()
  } catch (e: any) {
    formError.value = e.response?.data?.error ?? t('claims.errorDefault')
  }
}

const startReview = async (id: number) => {
  await claims.review(id, { decision: 'start_review' }).catch(() => {})
  load()
}

const reviewClaim = async (id: number, decision: string, amount?: number) => {
  await claims.review(id, {
    decision,
    approved_amount_cents: decision === 'approve' ? Math.round((amount ?? 0) * 100) : undefined,
    rejection_reason: decision === 'reject' ? 'Coverage requirements not met' : undefined,
  })
  load()
}
</script>
