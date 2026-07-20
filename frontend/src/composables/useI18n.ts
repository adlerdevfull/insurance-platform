import { computed, ref, type Ref } from 'vue'
import { messages, type Locale, type MessageTree } from '../i18n/messages'

const STORAGE_KEY = 'locale'

function detectLocale(): Locale {
  const saved = localStorage.getItem(STORAGE_KEY) as Locale | null
  if (saved && saved in messages) return saved
  const nav = (navigator.language || 'en').slice(0, 2).toLowerCase()
  if (nav === 'es' || nav === 'pt') return nav
  return 'en'
}

const locale: Ref<Locale> = ref(detectLocale())

function getByPath(obj: unknown, path: string): string {
  const parts = path.split('.')
  let cur: any = obj
  for (const p of parts) {
    if (cur == null) return path
    cur = cur[p]
  }
  return typeof cur === 'string' ? cur : path
}

export function useI18n() {
  const t = (key: string): string => getByPath(messages[locale.value], key)

  const setLocale = (next: Locale) => {
    locale.value = next
    localStorage.setItem(STORAGE_KEY, next)
    document.documentElement.lang = next
  }

  const available: { code: Locale; label: string }[] = [
    { code: 'en', label: 'EN' },
    { code: 'es', label: 'ES' },
    { code: 'pt', label: 'PT' },
  ]

  const dict = computed(() => messages[locale.value] as MessageTree)

  return { locale, t, setLocale, available, dict }
}
