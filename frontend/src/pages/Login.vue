<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
    <div class="absolute top-4 right-4">
      <LanguageSwitcher compact />
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
      <div class="text-center mb-8">
        <div class="text-5xl mb-3">🛡️</div>
        <h1 class="text-2xl font-bold text-gray-900">{{ t('login.title') }}</h1>
        <p class="text-gray-400 text-sm mt-1">{{ t('login.subtitle') }}</p>
      </div>
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('login.email') }}</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('login.password') }}</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50"
        >
          {{ loading ? t('login.loading') : t('login.submit') }}
        </button>
      </form>
      <p class="text-center text-xs text-gray-400 mt-6">{{ t('login.hint') }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import { useI18n } from '../composables/useI18n'
import LanguageSwitcher from '../components/LanguageSwitcher.vue'

const router = useRouter()
const { login } = useAuth()
const { t } = useI18n()
const email = ref('admin@insurance.test')
const password = ref('password')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await login(email.value, password.value)
    router.push('/dashboard')
  } catch {
    error.value = t('login.error')
  } finally {
    loading.value = false
  }
}
</script>
