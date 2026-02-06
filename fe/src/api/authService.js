import api from './axios'

export const authService = {
  register(formData) {
    return api.post('/auth/register', formData)
  },
  login(email, password) {
    return api.post('/auth/login', { email, password })
  },
  getProfile(id) {
    return api.get(`/users/${id}`)
  },
  updateProfile(id, formData) {
    return api.put(`/users/${id}`, formData)
  },
}

export default authService
