// Pinia store for user data
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/api/authService'

export const useUserStore = defineStore('user', () => {
  // State
  const storedUser = localStorage.getItem('user')
  const user = ref(storedUser ? JSON.parse(storedUser) : null)
  const token = ref(localStorage.getItem('token') || null)
  const isAuthenticated = computed(() => !!token.value)

  // Actions
  const setUser = (userData) => {
    user.value = userData
    if (userData) {
      localStorage.setItem('user', JSON.stringify(userData))
    }
  }

  const setToken = (authToken) => {
    token.value = authToken
    localStorage.setItem('token', authToken)
  }

  const register = async (formData) => {
    try {
      const response = await authService.register(formData)
      setToken(response.data.token)
      setUser(response.data.user)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }

  const login = async (email, password) => {
    try {
      const response = await authService.login(email, password)
      setToken(response.data.token)
      setUser(response.data.user)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }

  const getProfile = async (id) => {
    try {
      const response = await authService.getProfile(id)
      setUser(response.data)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }

  const updateProfile = async (formData) => {
    try {
      const response = await authService.updateProfile(user.value.id_nguoi_dung, formData)
      setUser(response.data.user)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  return {
    user,
    token,
    isAuthenticated,
    setUser,
    setToken,
    register,
    login,
    getProfile,
    updateProfile,
    logout,
  }
})
