<template>
  <router-view v-if="$route.path === '/login'" />
  <div v-else class="flex h-screen bg-gray-50">
    <aside class="w-64 bg-white shadow-sm flex flex-col">
      <div class="p-6 border-b">
        <h1 class="text-xl font-bold text-blue-700">🛡️ {{ t('appName') }}</h1>
        <p class="text-xs text-gray-400 mt-1">{{ t('appTagline') }}</p>
      </div>
      <nav class="flex-1 p-4 space-y-1">
        <router-link
          v-for="item in nav"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition"
          active-class="bg-blue-50 text-blue-700 font-medium"
        >
          <span>{{ item.icon }}</span>{{ item.label }}
        </router-link>
      </nav>
      <div class="p-4 border-t space-y-3">
        <LanguageSwitcher />
        <button
          @click="logout"
          class="w-full text-left text-sm text-gray-400 hover:text-red-500 transition"
        >
          🚪 {{ t('nav.logout') }}
        </button>
      </div>
    </aside>
    <main class="flex-1 overflow-auto p-8">
      <div class="flex justify-end mb-4 md:hidden">
        <LanguageSwitcher compact />
      </div>
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from './composables/useAuth'
import { useI18n } from './composables/useI18n'
import LanguageSwitcher from './components/LanguageSwitcher.vue'

const router = useRouter()
const { logout: doLogout } = useAuth()
const { t, locale } = useI18n()

// recompute labels when locale changes
const nav = computed(() => {
  void locale.value
  return [
    { to: '/dashboard', icon: '📊', label: t('nav.dashboard') },
    { to: '/policies', icon: '📋', label: t('nav.policies') },
    { to: '/claims', icon: '⚠️', label: t('nav.claims') },
  ]
})

const logout = () => {
  doLogout()
  router.push('/login')
}
</script>
