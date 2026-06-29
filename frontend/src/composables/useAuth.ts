import { ref, computed } from 'vue'
import { auth } from '../services/api'

const token = ref<string | null>(localStorage.getItem('token'))
const user  = ref<any>(null)

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value)

  const login = async (email: string, password: string) => {
    const res = await auth.login(email, password)
    const t   = res.data?.token || res.data?.data?.token
    if (!t) throw new Error('Token not found')
    token.value = t
    localStorage.setItem('token', t)
    await fetchUser()
  }

  const fetchUser = async () => {
    try {
      const res = await auth.me()
      user.value = res.data?.data || res.data
    } catch {}
  }

  const logout = () => {
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
  }

  return { isAuthenticated, user, login, logout, fetchUser }
}
