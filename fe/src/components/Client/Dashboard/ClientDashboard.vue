<template>
  <div class="page">
    <div class="header-section">
      <div class="greeting">
        <h2>👋 Xin chào, {{ userName || 'Cư dân' }}</h2>
        <p class="subtitle">Quản lý Sự cố & Yêu cầu Bảo trì</p>
      </div>
      <button v-if="userId" class="btn-create" @click="openCreateModal">
        <i class="fas fa-plus"></i> Báo cáo sự cố mới
      </button>
    </div>

    <div v-if="!isUserAuthed" class="note login-prompt">
      <i class="fas fa-lock"></i>
      <p>Vui lòng đăng nhập để xem và gửi yêu cầu bảo trì.</p>
    </div>
    
    <div v-else class="content-wrapper">
      <!-- STATS OVERVIEW -->
      <div class="stats">
        <div class="card stat-card total">
          <div class="stat-icon"><i class="fas fa-box"></i></div>
          <div class="stat-info">
            <h3>Tổng yêu cầu</h3>
            <p class="number">{{ total }}</p>
          </div>
        </div>
        <div class="card stat-card processing">
          <div class="stat-icon"><i class="fas fa-tools"></i></div>
          <div class="stat-info">
            <h3>Đang xử lý</h3>
            <p class="number">{{ processing }}</p>
          </div>
        </div>
        <div class="card stat-card done">
          <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
          <div class="stat-info">
            <h3>Hoàn thành</h3>
            <p class="number">{{ done }}</p>
          </div>
        </div>
        <div class="card stat-card pending">
          <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
          <div class="stat-info">
            <h3>Chờ xử lý</h3>
            <p class="number">{{ pending }}</p>
          </div>
        </div>
      </div>

      <!-- INCIDENT LIST -->
      <div class="incidents-section">
        <div class="section-header">
          <h3><i class="fas fa-list-alt"></i> Danh sách yêu cầu của bạn</h3>
          <button class="btn-refresh" @click="fetchData" :disabled="loading">
            <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
          </button>
        </div>

        <div v-if="error" class="alert error">
          <i class="fas fa-exclamation-circle"></i> {{ error }}
        </div>

        <div v-if="loading && requests.length === 0" class="loading-state">
          <div class="spinner"></div>
          <p>Đang tải dữ liệu...</p>
        </div>

        <div v-else-if="requests.length === 0" class="empty-state">
          <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No requests" class="empty-icon" />
          <p>Bạn chưa có yêu cầu bảo trì nào.</p>
          <button class="btn-outline" @click="openCreateModal">Tạo yêu cầu đầu tiên</button>
        </div>

        <div v-else class="requests-grid">
          <div v-for="req in requests" :key="req.id_yeu_cau" class="request-card">
            <div class="req-header">
              <span class="req-type">{{ req.loai_su_co?.ten_loai || 'Sự cố khác' }}</span>
              <span class="badge" :class="getStatusClass(req.trang_thai)">
                {{ getStatusText(req.trang_thai) }}
              </span>
            </div>
            
            <div class="req-body">
              <p class="req-desc">{{ req.mo_ta }}</p>
              
              <div class="req-meta">
                <div class="meta-item">
                  <i class="far fa-calendar-alt"></i>
                  <span>{{ formatDate(req.created_at) }}</span>
                </div>
                <div class="meta-item priority" v-if="req.thoi_gian_uu_tien">
                  <i class="fas fa-flag"></i>
                  <span :class="getPriorityClass(req.thoi_gian_uu_tien)">
                    {{ getPriorityText(req.thoi_gian_uu_tien) }}
                  </span>
                </div>
                <!-- Assigned Technician Info (if available from backend in future) -->
                <div class="meta-item" v-if="req.phan_cong && req.phan_cong.id_phan_cong">
                  <i class="fas fa-user-cog"></i>
                  <span>Lịch: {{ formatDate(req.phan_cong.ngay_phan_cong, true) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CREATE MODAL -->
    <transition name="modal-fade">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Tạo yêu cầu bảo trì mới</h3>
            <button class="btn-close" @click="closeModal"><i class="fas fa-times"></i></button>
          </div>
          
          <form @submit.prevent="submitRequest" class="modal-body">
            <div class="form-group">
              <label>Loại sự cố <span class="required">*</span></label>
              <select v-model="form.id_loai_su_co" required class="form-control" :disabled="submitting">
                <option value="" disabled>-- Chọn loại sự cố --</option>
                <option v-for="cat in categories" :key="cat.id_loai_su_co" :value="cat.id_loai_su_co">
                  {{ cat.ten_loai }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label>Mức độ ưu tiên</label>
              <select v-model="form.thoi_gian_uu_tien" class="form-control" :disabled="submitting">
                <option value="thap">Thấp</option>
                <option value="trung_binh">Bình thường</option>
                <option value="cao">Cao</option>
                <option value="khan_cap">Khẩn cấp</option>
              </select>
            </div>

            <div class="form-group">
              <label>Mô tả chi tiết <span class="required">*</span></label>
              <textarea 
                v-model="form.mo_ta" 
                required 
                class="form-control textarea" 
                placeholder="Vui lòng mô tả chi tiết sự cố bạn đang gặp phải..."
                rows="4"
                :disabled="submitting"
              ></textarea>
            </div>

            <div v-if="submitError" class="alert error small">
              <i class="fas fa-exclamation-triangle"></i> {{ submitError }}
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="closeModal" :disabled="submitting">Hủy</button>
              <button type="submit" class="btn-submit" :disabled="submitting || !isFormValid">
                <i class="fas fa-spinner fa-spin" v-if="submitting"></i>
                <span v-else>Gửi yêu cầu</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, reactive, watch } from 'vue'
import api from '@/services/api'
import { user, isAuthenticated } from '@/services/api'

// === STATE ===
const loading = ref(false)
const error = ref('')
const requests = ref([])
const categories = ref([])

// Modal State
const showModal = ref(false)
const submitting = ref(false)
const submitError = ref('')
const form = reactive({
  id_loai_su_co: '',
  thoi_gian_uu_tien: 'trung_binh',
  mo_ta: ''
})

// === COMPUTED ===
const isUserAuthed = computed(() => isAuthenticated.value)
const userId = computed(() => user.value?.id_nguoi_dung || user.value?.id || null)
const userName = computed(() => user.value?.ten || user.value?.name || '')
const roomId = computed(() => user.value?.id_can_ho || user.value?.canHo?.id_can_ho || null)

// Stats
const total = computed(() => requests.value.length)
const processing = computed(() => requests.value.filter((r) => r.trang_thai === 'dang_xu_ly').length)
const done = computed(() => requests.value.filter((r) => r.trang_thai === 'hoan_thanh').length)
const pending = computed(() => requests.value.filter((r) => r.trang_thai === 'cho_xu_ly' || r.trang_thai === 'pending').length)

const isFormValid = computed(() => form.id_loai_su_co && form.mo_ta.trim().length > 0)

// === METHODS ===

const fetchData = async () => {
  if (!userId.value) return
  loading.value = true
  error.value = ''
  try {
    const response = await api.get('/yeu-cau-bao-tri', { params: { id_cu_dan: userId.value } })
    requests.value = response.data || []
  } catch (err) {
    console.error('Fetch error:', err)
    error.value = err?.response?.data?.message || err?.message || 'Không tải được dữ liệu'
  } finally {
    loading.value = false
  }
}

watch(userId, (newId) => {
  if (newId) {
    fetchData()
  }
}, { immediate: true })

const fetchCategories = async () => {
  try {
    const res = await api.get('/loai-su-co');
    categories.value = res.data;
  } catch (err) {
    console.error('Failed to load categories', err);
  }
}

const openCreateModal = () => {
  submitError.value = ''
  form.id_loai_su_co = ''
  form.mo_ta = ''
  form.thoi_gian_uu_tien = 'trung_binh'
  
  if (categories.value.length === 0) {
    fetchCategories()
  }
  
  showModal.value = true
}

const closeModal = () => {
  if (!submitting.value) {
    showModal.value = false
  }
}

const submitRequest = async () => {
  if (!isFormValid.value || !userId.value) return
  
  submitting.value = true
  submitError.value = ''
  
  try {
    const payload = {
      id_cu_dan: userId.value,
      id_can_ho: roomId.value, // It's fine if this is null/undefined if BE allows, but ideally we have it
      id_loai_su_co: form.id_loai_su_co,
      mo_ta: form.mo_ta,
      thoi_gian_uu_tien: form.thoi_gian_uu_tien,
      trang_thai: 'cho_xu_ly'
    }
    
    await api.post('/yeu-cau-bao-tri', payload)
    
    // Success! Close modal and refresh list
    closeModal()
    fetchData()
    
    // Optional: Add a success toast here
  } catch (err) {
    console.error('Submit error:', err)
    submitError.value = err?.response?.data?.message || 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.'
  } finally {
    submitting.value = false
  }
}

// FORMATTERS
const formatDate = (dateString, dateOnly = false) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  if (isNaN(date.getTime())) return dateString
  
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' }
  if (!dateOnly) {
    options.hour = '2-digit'
    options.minute = '2-digit'
  }
  return date.toLocaleDateString('vi-VN', options)
}

const getStatusText = (status) => {
  const map = {
    moi: "Mới",
    cho_xu_ly: "Chờ xử lý",
    pending: "Chờ xử lý",
    da_xac_nhan: "Đã duyệt",
    dang_xu_ly: "Đang xử lý",
    approved: "Đã tiếp nhận",
    hoan_thanh: "Hoàn thành",
    tu_choi: "Đã từ chối",
    rejected: "Đã từ chối",
  }
  return map[status] || status || "Không rõ"
}

const getStatusClass = (status) => {
  if (['hoan_thanh'].includes(status)) return 'badge-success'
  if (['da_xac_nhan', 'dang_xu_ly', 'approved'].includes(status)) return 'badge-info'
  if (['tu_choi', 'rejected'].includes(status)) return 'badge-danger'
  return 'badge-warning' // moi, pending, cho_xu_ly
}

const getPriorityText = (priority) => {
  const map = {
    thap: "Thấp",
    trung_binh: "Bình thường",
    cao: "Cao",
    khan_cap: "Khẩn cấp"
  }
  return map[priority] || priority
}

const getPriorityClass = (priority) => {
  if (priority === 'khan_cap' || priority === 'cao') return 'text-danger fw-bold'
  return 'text-muted'
}

// === LIFECYCLE ===
onMounted(() => {
  if (userId.value) {
    fetchData()
  }
  fetchCategories() // Pre-fetch to make modal fast
})
</script>

<style scoped>
:global(:root) {
  --primary: #3b82f6;
  --primary-hover: #2563eb;
  --text-main: #1f2937;
  --text-light: #6b7280;
  --bg-light: #f3f4f6;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --info: #0ea5e9;
  --white: #ffffff;
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.page {
  padding: 30px 20px;
  max-width: 1200px;
  margin: 0 auto;
  min-height: calc(100vh - 72px);
  background: #fafafa;
}

/* HEADER */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
}

.greeting h2 {
  font-size: 28px;
  color: var(--text-main);
  margin: 0;
  font-weight: 700;
}

.greeting .subtitle {
  color: var(--text-light);
  margin: 5px 0 0 0;
  font-size: 15px;
}

.btn-create {
  background: linear-gradient(135deg, var(--primary), #60a5fa);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: var(--radius-md);
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-create:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}

/* STATS */
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: var(--white);
  padding: 24px;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(0,0,0,0.03);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.total .stat-icon { background: #eff6ff; color: var(--primary); }
.processing .stat-icon { background: #f0fdf4; color: var(--success); }
.done .stat-icon { background: #fefce8; color: var(--warning); }
.pending .stat-icon { background: #fef2f2; color: var(--danger); }

.stat-info h3 {
  margin: 0 0 4px 0;
  font-size: 14px;
  color: var(--text-light);
  font-weight: 500;
}

.stat-info .number {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: var(--text-main);
}

/* LIST SECTION */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--bg-light);
}

.section-header h3 {
  margin: 0;
  font-size: 20px;
  color: var(--text-main);
}

.btn-refresh {
  background: var(--white);
  border: 1px solid #e5e7eb;
  color: var(--text-light);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.2s;
}

.btn-refresh:hover:not(:disabled) {
  background: var(--bg-light);
  color: var(--primary);
}

.requests-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}

.request-card {
  background: var(--white);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  padding: 20px;
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.request-card:hover {
  box-shadow: var(--shadow-md);
  border-color: #e5e7eb;
}

.req-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.req-type {
  font-weight: 600;
  font-size: 16px;
  color: var(--text-main);
}

.req-desc {
  color: var(--text-light);
  font-size: 14px;
  line-height: 1.5;
  margin: 0 0 16px 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex-grow: 1;
}

.req-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 13px;
  color: #9ca3af;
  border-top: 1px solid #f3f4f6;
  padding-top: 12px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

/* BADGES */
.badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-info { background: #dbeafe; color: #1e40af; }
.badge-warning { background: #fef9c3; color: #854d0e; }
.badge-danger { background: #fee2e2; color: #991b1b; }

.text-danger { color: #dc2626; }
.text-muted { color: #9ca3af; }
.fw-bold { font-weight: 600; }

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: var(--white);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 500px;
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  animation: modalSlideIn 0.3s ease-out forwards;
}

@keyframes modalSlideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.3s;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: var(--text-main);
}

.btn-close {
  background: transparent;
  border: none;
  font-size: 18px;
  color: var(--text-light);
  cursor: pointer;
  padding: 4px;
}

.btn-close:hover { color: var(--danger); }

.modal-body {
  padding: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--text-main);
  margin-bottom: 8px;
}

.required { color: var(--danger); }

.form-control {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: var(--radius-sm);
  font-size: 14px;
  color: var(--text-main);
  background: var(--white);
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: inherit;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:disabled {
  background: #f3f4f6;
  cursor: not-allowed;
}

.textarea {
  resize: vertical;
  min-height: 100px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel, .btn-submit {
  padding: 10px 20px;
  border-radius: var(--radius-sm);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  color: var(--text-main);
}

.btn-cancel:hover:not(:disabled) { background: #e5e7eb; }

.btn-submit {
  background: var(--primary);
  border: 1px solid var(--primary);
  color: var(--white);
  min-width: 120px;
}

.btn-submit:hover:not(:disabled) {
  background: var(--primary-hover);
  border-color: var(--primary-hover);
}

.btn-submit:disabled, .btn-cancel:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* UTILS */
.alert {
  padding: 12px 16px;
  border-radius: var(--radius-sm);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.alert.error {
  background: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}
.alert.small {
  padding: 8px 12px;
  font-size: 13px;
  margin-bottom: 0;
  margin-top: 10px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: var(--white);
  border-radius: var(--radius-md);
  border: 1px dashed #d1d5db;
}
.empty-icon {
  width: 80px;
  height: 80px;
  margin-bottom: 16px;
  opacity: 0.6;
}
.empty-state p {
  color: var(--text-light);
  margin-bottom: 20px;
}

.btn-outline {
  background: transparent;
  border: 2px solid var(--primary);
  color: var(--primary);
  padding: 8px 20px;
  border-radius: var(--radius-sm);
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.btn-outline:hover {
  background: var(--primary);
  color: var(--white);
}

.loading-state {
  text-align: center;
  padding: 50px;
  color: var(--text-light);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.login-prompt {
  background: #fff;
  padding: 40px;
  border-radius: var(--radius-md);
  text-align: center;
  color: var(--text-light);
  box-shadow: var(--shadow-sm);
}

.login-prompt i {
  font-size: 40px;
  color: #cbd5e1;
  margin-bottom: 15px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .page { padding: 20px 15px; }
  .header-section { flex-direction: column; align-items: flex-start; }
  .btn-create { width: 100%; justify-content: center; }
  .modal-content { height: 100vh; max-width: 100%; border-radius: 0; display: flex; flex-direction: column; }
  .modal-body { flex-grow: 1; overflow-y: auto; }
}
</style>
