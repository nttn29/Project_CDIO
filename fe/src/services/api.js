import axios from 'axios'
import { ref, computed } from 'vue'

// In dev we use Vite proxy: call '/api' so proxy forwards to backend.
// In production use VITE_API_BASE_URL if provided, otherwise default to '/DKMN_BE/public/api'.
const getBaseURL = () => {
  if (import.meta.env.DEV) return '/api'
  return import.meta.env.VITE_API_BASE_URL || '/DKMN_BE/public/api'
}

// Reactive auth state exposed to components
export const user = ref(localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null)
export const token = ref(localStorage.getItem('token') || null)
export const isAuthenticated = computed(() => !!user.value || !!token.value)

export function setUser(u) {
  user.value = u
  if (u) localStorage.setItem('user', JSON.stringify(u))
  else localStorage.removeItem('user')
}

export function setToken(t) {
  token.value = t
  if (t) localStorage.setItem('token', t)
  else localStorage.removeItem('token')
}

const api = axios.create({
  baseURL: getBaseURL(),
  headers: {
    'Content-Type': 'application/json',
  },
})

// Attach token automatically from reactive ref
api.interceptors.request.use((config) => {
  const t = token.value
  if (t) {
    config.headers = config.headers || {}
    config.headers.Authorization = `Bearer ${t}`
  }
  return config
})

// Convenience auth helpers
export async function login(email, password) {
  const response = await api.post('/login', { email, mat_khau: password })
  const data = response.data
  if (data.token) setToken(data.token)
  if (data.user) setUser(data.user)
  return data
}

export async function register(formData) {
  const response = await api.post('/register', formData)
  const data = response.data
  if (data.token) setToken(data.token)
  if (data.user) setUser(data.user)
  return data
}

export function logout() {
  setUser(null)
  setToken(null)
}

export default api

