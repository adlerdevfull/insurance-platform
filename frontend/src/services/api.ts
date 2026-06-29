import axios from 'axios'

const api = axios.create({
  baseURL: '/api/v1',
  headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' }
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

export const auth = {
  login: (email: string, password: string) => api.post('/auth/login', { email, password }),
  me: () => api.get('/auth/me'),
}

export const policies = {
  list: (params = {}) => api.get('/policies', { params }),
  show: (id: number) => api.get(`/policies/${id}`),
  create: (data: object) => api.post('/policies', data),
  transition: (id: number, status: string) => api.patch(`/policies/${id}/transition`, { status }),
}

export const claims = {
  list: (params = {}) => api.get('/claims', { params }),
  show: (id: number) => api.get(`/claims/${id}`),
  create: (data: object) => api.post('/claims', data),
  review: (id: number, data: object) => api.patch(`/claims/${id}/review`, data),
}

export default api
