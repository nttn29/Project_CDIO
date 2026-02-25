<template>
  <div class="page">
    <h1>📜 Lịch sử yêu cầu</h1>
    <p class="desc">
      Danh sách các yêu cầu dịch vụ của cư dân (dữ liệu từ backend).
    </p>

    <div v-if="!userId" class="note">
      Vui lòng đăng nhập để xem dữ liệu.
    </div>

    <div v-else>
      <div class="filters">
        <select v-model="typeFilter">
          <option value="">Tất cả dịch vụ</option>
          <option v-for="t in typeOptions" :key="t" :value="t">{{ t }}</option>
        </select>

        <select v-model="statusFilter">
          <option value="">Tất cả trạng thái</option>
          <option v-for="s in statusOptions" :key="s" :value="s">{{ s }}</option>
        </select>
      </div>

      <div v-if="loading" class="note">Đang tải dữ liệu...</div>
      <div v-if="error" class="note error">{{ error }}</div>

      <div v-if="!loading" class="table-box">
        <table>
          <thead>
            <tr>
              <th>Dịch vụ</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in filteredRows" :key="item.id">
              <td>{{ item.service }}</td>
              <td>{{ item.date }}</td>
              <td>
                <span :class="['status', item.statusClass]">
                  {{ item.status }}
                </span>
              </td>
            </tr>
            <tr v-if="filteredRows.length === 0">
              <td colspan="3" class="empty">Chưa có yêu cầu nào.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { user } from '@/services/api'
import api from '@/services/api'
import { formatDate } from './utils'

const loading = ref(false)
const error = ref('')
const requests = ref([])

const typeFilter = ref('')
const statusFilter = ref('')

const userId = computed(() => user.value?.id_nguoi_dung || null)

const statusMap = {
  moi: { label: 'Mới', class: 'new' },
  da_xac_nhan: { label: 'Đã xác nhận', class: 'confirm' },
  dang_xu_ly: { label: 'Đang xử lý', class: 'processing' },
  hoan_thanh: { label: 'Hoàn thành', class: 'done' },
  huy: { label: 'Đã hủy', class: 'cancel' },
}

const rows = computed(() =>
  requests.value.map((r) => {
    const status = statusMap[r.trang_thai] || { label: r.trang_thai, class: 'processing' }
    return {
      id: r.id_yeu_cau,
      service: r.loai_su_co?.ten_loai || 'N/A',
      date: formatDate(r.created_at),
      status: status.label,
      statusClass: status.class,
      rawStatus: r.trang_thai,
    }
  })
)

const typeOptions = computed(() => {
  const set = new Set(rows.value.map((r) => r.service).filter(Boolean))
  return Array.from(set)
})

const statusOptions = computed(() => {
  const set = new Set(rows.value.map((r) => r.status).filter(Boolean))
  return Array.from(set)
})

const filteredRows = computed(() => {
  return rows.value.filter((r) => {
    const okType = typeFilter.value ? r.service === typeFilter.value : true
    const okStatus = statusFilter.value ? r.status === statusFilter.value : true
    return okType && okStatus
  })
})

onMounted(async () => {
  if (!userId.value) return
  loading.value = true
  error.value = ''
  try {
    const response = await api.get('/yeu-cau-bao-tri', { params: { id_cu_dan: userId.value } })
    requests.value = response.data || []
  } catch (err) {
    error.value = err?.error || err?.message || 'Không tải được dữ liệu'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page {
  padding: 24px;
}

.desc {
  color: #64748b;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

select {
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #cbd5e1;
  background: white;
}

.table-box {
  background: white;
  border-radius: 18px;
  padding: 18px;
  box-shadow: 0 18px 40px rgba(0,0,0,.08);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 14px;
  text-align: left;
}

th {
  color: #334155;
  font-weight: 600;
}

tbody tr {
  border-top: 1px solid #e5e7eb;
}

.status {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
}

.new {
  background: #fef9c3;
  color: #854d0e;
}

.confirm {
  background: #e0f2fe;
  color: #0369a1;
}

.done {
  background: #dcfce7;
  color: #166534;
}

.processing {
  background: #dbeafe;
  color: #1e40af;
}

.cancel {
  background: #fee2e2;
  color: #b91c1c;
}

.note {
  margin-top: 14px;
  font-size: 13px;
  color: #94a3b8;
  font-style: italic;
}

.note.error {
  color: #dc2626;
}

.empty {
  text-align: center;
  color: #94a3b8;
}
</style>
