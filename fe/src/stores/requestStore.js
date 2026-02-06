// Pinia store for maintenance requests
import { defineStore } from 'pinia'
import { ref } from 'vue'
import * as requestService from '@/api/requestService'

export const useRequestStore = defineStore('request', () => {
  // State
  const requests = ref([])
  const currentRequest = ref(null)
  const loading = ref(false)
  const error = ref(null)

  // Actions
  const getMyRequests = async (userId, filters = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.getMyRequests(userId, filters)
      requests.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const getRequestDetail = async (requestId) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.getRequestDetail(requestId)
      currentRequest.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const createRequest = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.createRequest(data)
      requests.value.push(response.data.data)
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateRequest = async (requestId, data) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.updateRequest(requestId, data)
      // Update in local state
      const index = requests.value.findIndex((r) => r.id_yeu_cau === requestId)
      if (index !== -1) {
        requests.value[index] = response.data.data
      }
      if (currentRequest.value?.id_yeu_cau === requestId) {
        currentRequest.value = response.data.data
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteRequest = async (requestId) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.deleteRequest(requestId)
      requests.value = requests.value.filter((r) => r.id_yeu_cau !== requestId)
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const confirmRequest = async (requestId) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.confirmRequest(requestId)
      const index = requests.value.findIndex((r) => r.id_yeu_cau === requestId)
      if (index !== -1) {
        requests.value[index].trang_thai = 'da_xac_nhan'
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const uploadImage = async (requestId, file) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.uploadImage(requestId, file)
      if (currentRequest.value?.id_yeu_cau === requestId) {
        currentRequest.value.hinh_anh.push(response.data.data)
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteImage = async (imageId) => {
    loading.value = true
    error.value = null
    try {
      const response = await requestService.deleteImage(imageId)
      if (currentRequest.value?.hinh_anh) {
        currentRequest.value.hinh_anh = currentRequest.value.hinh_anh.filter(
          (img) => img.id_hinh_anh !== imageId
        )
      }
      return response.data
    } catch (err) {
      error.value = err.response?.data || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    requests,
    currentRequest,
    loading,
    error,
    getMyRequests,
    getRequestDetail,
    createRequest,
    updateRequest,
    deleteRequest,
    confirmRequest,
    uploadImage,
    deleteImage,
  }
})
