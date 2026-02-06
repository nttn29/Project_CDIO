<template>
  <div class="page">
    <h2>👋 Xin chào, {{ userName || 'Cư dân' }}</h2>

    <div v-if="!userId" class="note">Vui lòng đăng nhập để xem tổng quan.</div>
    <div v-else>
      <div v-if="loading" class="note">Đang tải dữ liệu...</div>
      <div v-if="error" class="note error">{{ error }}</div>

      <div class="stats">
        <div class="card">
          <h3>📦 Tổng yêu cầu</h3>
          <p class="number">{{ total }}</p>
        </div>
        <div class="card">
          <h3>🚨 Đang xử lý</h3>
          <p class="number">{{ processing }}</p>
        </div>
        <div class="card">
          <h3>✅ Hoàn thành</h3>
          <p class="number">{{ done }}</p>
        </div>
        <div class="card">
          <h3>⏳ Mới</h3>
          <p class="number">{{ pending }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRequestStore } from '@/stores/requestStore'
import { useUserStore } from '@/stores/userStore'

const requestStore = useRequestStore()
const userStore = useUserStore()

const loading = ref(false)
const error = ref('')

const userId = computed(() => userStore.user?.id_nguoi_dung || null)
const userName = computed(() => userStore.user?.ten || userStore.user?.name || '')
const requests = computed(() => requestStore.requests || [])

const total = computed(() => requests.value.length)
const processing = computed(() => requests.value.filter((r) => r.trang_thai === 'dang_xu_ly').length)
const done = computed(() => requests.value.filter((r) => r.trang_thai === 'hoan_thanh').length)
const pending = computed(() => requests.value.filter((r) => r.trang_thai === 'moi').length)

onMounted(async () => {
  if (!userId.value) return
  loading.value = true
  error.value = ''
  try {
    await requestStore.getMyRequests(userId.value)
  } catch (err) {
    error.value = err?.error || err?.message || 'Không tải được dữ liệu'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page {
  padding: 20px;
}
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
}
.card {
  background: #fff;
  padding: 16px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.number {
  font-size: 28px;
  font-weight: bold;
  color: #3498db;
}
.note {
  margin-top: 10px;
  color: #64748b;
  font-size: 13px;
}
.note.error {
  color: #dc2626;
}
</style>
