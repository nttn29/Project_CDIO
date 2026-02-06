import api from './axios'

export async function getMyRequests(userId, filters = {}) {
  return api.get('/yeu-cau', { params: { id_cu_dan: userId, ...filters } })
}

export async function getRequestDetail(requestId) {
  return api.get(`/yeu-cau/${requestId}`)
}

export async function createRequest(data) {
  return api.post('/yeu-cau', data)
}

export async function updateRequest(requestId, data) {
  return api.put(`/yeu-cau/${requestId}`, data)
}

export async function deleteRequest(requestId) {
  return api.delete(`/yeu-cau/${requestId}`)
}

export async function confirmRequest(requestId) {
  return api.post(`/yeu-cau/${requestId}/confirm`)
}

export async function uploadImage(requestId, file) {
  const form = new FormData()
  form.append('file', file)
  return api.post(`/yeu-cau/${requestId}/hinh-anh`, form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
}

export async function deleteImage(imageId) {
  return api.delete(`/hinh-anh/${imageId}`)
}

export default {
  getMyRequests,
  getRequestDetail,
  createRequest,
  updateRequest,
  deleteRequest,
  confirmRequest,
  uploadImage,
  deleteImage,
}
