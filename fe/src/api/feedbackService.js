import api from './axios'

export const feedbackService = {
  getFeedback(params = {}) {
    return api.get('/phan-hoi', { params })
  },
  submitFeedback(data) {
    return api.post('/phan-hoi', data)
  },
}

export default feedbackService
