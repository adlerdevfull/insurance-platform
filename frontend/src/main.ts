import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './style.css'

import Login from './pages/Login.vue'
import Dashboard from './pages/Dashboard.vue'
import Policies from './pages/Policies.vue'
import Claims from './pages/Claims.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: Login },
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', component: Dashboard, meta: { auth: true } },
    { path: '/policies', component: Policies, meta: { auth: true } },
    { path: '/claims', component: Claims, meta: { auth: true } },
  ]
})

router.beforeEach((to) => {
  if (to.meta.auth && !localStorage.getItem('token')) return '/login'
})

createApp(App).use(router).mount('#app')
