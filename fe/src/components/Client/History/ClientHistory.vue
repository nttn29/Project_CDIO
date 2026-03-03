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
              <th>Sự cố</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
              <th></th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in filteredRows" :key="item.id">
              <td>
                <div class="issue-title">{{ item.service }}</div>
                <div class="issue-desc">{{ item.description }}</div>
              </td>
              <td>{{ item.date }}</td>
              <td>
                <span :class="['status', item.statusClass]">
                  {{ item.status }}
                </span>
              </td>
              <td class="actions-col">
                <button class="detail-btn" @click="openDetails(item)">Xem chi tiết</button>
              </td>
            </tr>
            <tr v-if="filteredRows.length === 0">
              <td colspan="4" class="empty">Chưa có yêu cầu nào.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="selectedRow" class="overlay" @click.self="closeDetails">
      <div class="detail-modal">
        <div class="detail-header">
          <h3>Chi tiết yêu cầu #{{ selectedRow.id }}</h3>
          <button class="close-btn" @click="closeDetails">×</button>
        </div>

        <div class="detail-grid">
          <div><strong>Sự cố:</strong> {{ selectedRow.service }}</div>
          <div><strong>Ngày gửi:</strong> {{ selectedRow.date }}</div>
          <div class="full-row"><strong>Mô tả:</strong> {{ selectedRow.description }}</div>
          <div><strong>Mức ưu tiên:</strong> {{ selectedRow.priorityLabel }}</div>
          <div><strong>Trạng thái:</strong> {{ selectedRow.status }}</div>
          <div>
            <strong>Đã duyệt:</strong>
            <span :class="['confirm-tag', selectedRow.confirmed ? 'yes' : 'no']">
              {{ selectedRow.confirmed ? 'Đã duyệt' : 'Chưa duyệt' }}
            </span>
          </div>
          <div><strong>Thời gian sửa dự kiến:</strong> {{ selectedRow.scheduledAt }}</div>
          <div><strong>Thời gian xong dự kiến:</strong> {{ selectedRow.estimatedDoneAt }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { user } from '@/services/api'
import api from '@/services/api'
import { formatDate } from './utils'

const loading = ref(false)
const error = ref('')
const requests = ref([])
const selectedRow = ref(null)

const typeFilter = ref('')
const statusFilter = ref('')
let refreshTimer = null

const userId = computed(() => user.value?.id_nguoi_dung || null)

const statusMap = {
  moi: { label: 'Mới', class: 'new' },
  cho_xu_ly: { label: 'Chờ xử lý', class: 'new' },
  da_xac_nhan: { label: 'Đã duyệt', class: 'confirm' },
  dang_xu_ly: { label: 'Đang xử lý', class: 'processing' },
  hoan_thanh: { label: 'Hoàn thành', class: 'done' },
  huy: { label: 'Đã hủy', class: 'cancel' },
}

function toRow(r) {
  const status = statusMap[r.trang_thai] || { label: r.trang_thai, class: 'processing' }
  const scheduleDate = buildScheduleDate(r.phan_cong?.ngay_phan_cong, r.phan_cong?.gio_hen)
  const scheduleAt = scheduleDate ? formatDate(scheduleDate.toISOString()) : 'Chưa có lịch'
  const estimatedDoneAt = estimateDoneTime(scheduleDate, r.thoi_gian_uu_tien)
  return {
    id: r.id_yeu_cau,
    service: r.loai_su_co?.ten_loai || 'N/A',
    description: r.mo_ta || 'Chưa có mô tả chi tiết',
    date: formatDate(r.created_at) || 'Chưa có',
    status: status.label,
    statusClass: status.class,
    rawStatus: r.trang_thai,
    confirmed: !!r.da_xac_nhan,
    priorityLabel: priorityLabel(r.thoi_gian_uu_tien),
    scheduledAt: scheduleAt,
    estimatedDoneAt,
  }
}

const rows = computed(() => requests.value.map(toRow))

const typeOptions = ref([])

const statusOptions = computed(() => {
  return ['Mới', 'Chờ xử lý', 'Đã duyệt', 'Đang xử lý', 'Hoàn thành', 'Đã hủy']
})

const filteredRows = computed(() => {
  return rows.value.filter((r) => {
    const okType = typeFilter.value ? r.service === typeFilter.value : true
    const okStatus = statusFilter.value ? r.status === statusFilter.value : true
    return okType && okStatus
  })
})

function priorityLabel(value) {
  if (value === 'gan') return 'Gấp'
  if (value === 'binh_thuong') return 'Bình thường'
  if (value === 'kho') return 'Khó'
  return 'Chưa xác định'
}

function buildScheduleDate(date, time) {
  if (!date) return null
  const raw = time ? `${date}T${time}` : `${date}T00:00:00`
  const parsed = new Date(raw)
  return Number.isNaN(parsed.getTime()) ? null : parsed
}

function estimateDoneTime(scheduleDate, priority) {
  if (!scheduleDate) return 'Chưa có dự kiến'

  const extraHours = priority === 'gan' ? 2 : priority === 'binh_thuong' ? 8 : 24
  const start = new Date(scheduleDate.getTime())
  start.setHours(start.getHours() + extraHours)
  return start.toLocaleString()
}

function openDetails(item) {
  selectedRow.value = item
}

function closeDetails() {
  selectedRow.value = null
}

async function fetchRequests() {
  if (!userId.value) return
  loading.value = true
  error.value = ''
  try {
    const response = await api.get('/yeu-cau-bao-tri', { params: { id_cu_dan: userId.value } })
    requests.value = response.data || []
    if (selectedRow.value?.id) {
      const latest = requests.value.find((r) => r.id_yeu_cau === selectedRow.value.id)
      if (latest) selectedRow.value = toRow(latest)
    }
  } catch (err) {
    error.value = err?.response?.data?.message || err?.message || 'Không tải được dữ liệu'
  } finally {
    loading.value = false
  }
}

function handleVisibilityOrFocus() {
  if (!document.hidden) {
    fetchRequests()
  }
}

onMounted(async () => {
  await fetchRequests()

  try {
    const resTypes = await api.get('/loai-su-co')
    if (resTypes.data && resTypes.data.length > 0) {
      typeOptions.value = resTypes.data.map(i => i.ten_loai).filter(Boolean)
    }
  } catch (err) {
    console.error('Không tải được danh mục sự cố:', err)
  }

  window.addEventListener('focus', handleVisibilityOrFocus)
  document.addEventListener('visibilitychange', handleVisibilityOrFocus)
  refreshTimer = setInterval(fetchRequests, 15000)
})

onBeforeUnmount(() => {
  window.removeEventListener('focus', handleVisibilityOrFocus)
  document.removeEventListener('visibilitychange', handleVisibilityOrFocus)
  if (refreshTimer) clearInterval(refreshTimer)
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

.issue-title {
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 4px;
}

.issue-desc {
  font-size: 13px;
  color: #64748b;
}

.actions-col {
  text-align: right;
}

.detail-btn {
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  border-radius: 10px;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 13px;
}

.detail-btn:hover {
  background: #eef2ff;
}

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.detail-modal {
  width: min(760px, 92vw);
  background: #fff;
  border-radius: 16px;
  padding: 18px;
}

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.detail-header h3 {
  margin: 0;
  color: #0f172a;
}

.close-btn {
  border: none;
  background: transparent;
  font-size: 24px;
  cursor: pointer;
  line-height: 1;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px 20px;
  color: #334155;
}

.full-row {
  grid-column: 1 / -1;
}

.confirm-tag {
  margin-left: 8px;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.confirm-tag.yes {
  background: #dcfce7;
  color: #166534;
}

.confirm-tag.no {
  background: #fee2e2;
  color: #b91c1c;
}
</style>
