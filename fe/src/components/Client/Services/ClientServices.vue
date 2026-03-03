<template>
  <div class="services-page">
    <div class="header-section">
      <h1>Dịch vụ & Hỗ trợ</h1>
      <p class="subtitle">Vui lòng chọn loại dịch vụ và mô tả chi tiết yêu cầu của bạn để chúng tôi hỗ trợ tốt nhất.</p>
    </div>

    <div v-if="!userId" class="alert warning">
      <i class="fas fa-exclamation-triangle"></i> Vui lòng đăng nhập để gửi yêu cầu hỗ trợ.
    </div>
    
    <div v-if="userId && !canHoId" class="alert error">
      <i class="fas fa-home"></i> Tài khoản của bạn chưa được liên kết với căn hộ nào. Xin vui lòng liên hệ Ban Quản Lý.
    </div>

    <div class="content-wrapper" :class="{ 'blur-content': !canHoId && userId }">
      <!-- Cột Trái: Danh sách Dịch vụ -->
      <div class="services-list-col">
        <h2 class="section-title">Chọn Nhanh Dịch Vụ</h2>
        <div class="service-grid">
          <div 
            class="service-card" 
            v-for="s in issueTypes" 
            :key="s.id_loai_su_co"
            :class="{ active: selectedIssueId === s.id_loai_su_co }"
            @click="selectIssue(s.id_loai_su_co)"
          >
            <div class="icon-circle">
              <i class="fas fa-tools"></i>
            </div>
            <div class="service-info">
              <h3>{{ s.ten_loai }}</h3>
              <p>{{ s.mo_ta || 'Dịch vụ bảo trì/hỗ trợ' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột Phải: Form Gửi Yêu Cầu -->
      <div class="form-col">
        <div class="form-card">
          <h2 class="section-title">Tạo yều cầu báo cáo sự cố mới</h2>
          
          <div class="form-group">
            <label>Loại sự cố / Dịch vụ <span class="required">*</span></label>
            <div class="select-wrapper">
              <select v-model="selectedIssueId" :disabled="!canHoId || !userId">
                <option disabled value="">-- Vui lòng chọn dịch vụ --</option>
                <option v-for="i in issueTypes" :key="i.id_loai_su_co" :value="i.id_loai_su_co">
                  {{ i.ten_loai }}
                </option>
              </select>
              <i class="fas fa-chevron-down select-icon"></i>
            </div>
          </div>

          <div class="form-group">
            <label>Mô tả chi tiết <span class="required">*</span></label>
            <textarea 
              v-model="description" 
              rows="5" 
              placeholder="Vui lòng cung cấp chi tiết sự cố bạn đang gặp phải..." 
              :disabled="!canHoId || !userId"
            ></textarea>
          </div>

          <div class="form-group">
            <label>Mức độ ưu tiên</label>
            <div class="custom-radio-group">
              <label class="radio-card" :class="{ selected: priority === 'binh_thuong' }">
                <input type="radio" value="binh_thuong" v-model="priority" :disabled="!canHoId || !userId">
                <div class="radio-content">
                  <i class="fas fa-leaf text-success"></i>
                  <span>Bình thường</span>
                </div>
              </label>
              
              <label class="radio-card" :class="{ selected: priority === 'gan' }">
                <input type="radio" value="gan" v-model="priority" :disabled="!canHoId || !userId">
                <div class="radio-content">
                  <i class="fas fa-fire text-danger"></i>
                  <span>Khẩn cấp</span>
                </div>
              </label>
              
              <label class="radio-card" :class="{ selected: priority === 'kho' }">
                <input type="radio" value="kho" v-model="priority" :disabled="!canHoId || !userId">
                <div class="radio-content">
                  <i class="fas fa-clock text-warning"></i>
                  <span>Ưu tiên thấp</span>
                </div>
              </label>
            </div>
          </div>

          <transition name="slide-fade">
            <div v-if="error" class="message-banner error">
              <i class="fas fa-exclamation-circle"></i> {{ error }}
            </div>
          </transition>
          
          <transition name="slide-fade">
            <div v-if="success" class="message-banner success">
              <i class="fas fa-check-circle"></i> {{ success }}
            </div>
          </transition>

          <button 
            class="submit-btn" 
            :disabled="!canHoId || !userId || loading" 
            @click="submitRequest"
            :class="{ 'is-loading': loading }"
          >
            <span v-if="!loading"><i class="fas fa-paper-plane"></i> Gửi Yêu Cầu Bảo Trì</span>
            <span v-else><i class="fas fa-spinner fa-spin"></i> Đang xử lý...</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { user } from '@/services/api'
import api from '@/services/api'

const issueTypes = ref([])
const selectedIssueId = ref('')
const description = ref('')
const priority = ref('binh_thuong')
const loading = ref(false)
const error = ref('')
const success = ref('')

const userId = computed(() => user.value?.id_nguoi_dung || null)
const canHoId = computed(() => user.value?.can_ho?.id_can_ho || null)

function selectIssue(id) {
  if (!canHoId.value || !userId.value) return;
  selectedIssueId.value = id
  success.value = ''
  error.value = ''
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function submitRequest() {
  if (!userId.value || !canHoId.value) return
  if (!selectedIssueId.value || !description.value.trim()) {
    error.value = 'Vui lòng chọn dịch vụ và nhập mô tả chi tiết'
    success.value = ''
    return
  }
  
  loading.value = true
  error.value = ''
  success.value = ''
  
  try {
    const response = await api.post('/yeu_cau', {
      id_cu_dan: userId.value,
      id_can_ho: canHoId.value,
      id_loai_su_co: selectedIssueId.value,
      mo_ta: description.value.trim(),
      thoi_gian_uu_tien: priority.value,
    })
    
    // Reset form after success
    description.value = ''
    selectedIssueId.value = ''
    priority.value = 'binh_thuong'
    success.value = 'Đã gửi yêu cầu thành công! Ban Quản Lý sẽ liên hệ sớm nhất.'
    
    // Hide success message after 5 seconds
    setTimeout(() => {
      success.value = ''
    }, 5000)
    
  } catch (err) {
    error.value = err?.response?.data?.message || err?.error || err?.message || 'Gửi yêu cầu thất bại'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const response = await api.get('/loai-su-co')
    issueTypes.value = response.data || []
  } catch (err) {
    console.error('Không tải được danh sách dịch vụ:', err)
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.services-page {
  font-family: 'Inter', sans-serif;
  max-width: 1300px;
  margin: 0 auto;
  padding: 30px 20px;
  color: #1e293b;
}

/* Header */
.header-section {
  text-align: center;
  margin-bottom: 40px;
}

.header-section h1 {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 12px;
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.subtitle {
  font-size: 16px;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
}

/* Alerts */
.alert {
  padding: 16px 20px;
  border-radius: 12px;
  margin-bottom: 24px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert.warning {
  background: #fffbeb;
  color: #b45309;
  border: 1px solid #fcd34d;
}

.alert.error {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.blur-content {
  opacity: 0.5;
  pointer-events: none;
  filter: grayscale(0.8);
}

/* Layout */
.content-wrapper {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 40px;
  transition: all 0.3s ease;
}

@media (max-width: 992px) {
  .content-wrapper {
    grid-template-columns: 1fr;
  }
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #1e293b;
  position: relative;
  padding-bottom: 10px;
}

.section-title::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 40px;
  height: 3px;
  background: #3b82f6;
  border-radius: 2px;
}

/* Service Cột Trái */
.service-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 16px;
}

.service-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 20px;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.25s ease;
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.service-card:hover {
  border-color: #cbd5e1;
  transform: translateY(-4px);
  box-shadow: 0 12px 25px -5px rgba(0,0,0,0.05);
}

.service-card.active {
  border-color: #3b82f6;
  background: #eff6ff;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.icon-circle {
  width: 48px;
  height: 48px;
  background: #f1f5f9;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  font-size: 20px;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.service-card:hover .icon-circle,
.service-card.active .icon-circle {
  background: #3b82f6;
  color: #ffffff;
}

.service-info h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 6px 0;
  color: #334155;
}

.service-info p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}

/* Form Cột Phải */
.form-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 20px 40px -10px rgba(0,0,0,0.05);
  border: 1px solid #f1f5f9;
}

.form-group {
  margin-bottom: 24px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
}

.required {
  color: #ef4444;
}

/* Select & Input */
.select-wrapper {
  position: relative;
}

.select-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

select, textarea {
  width: 100%;
  padding: 14px 16px;
  font-family: inherit;
  font-size: 15px;
  color: #1e293b;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  transition: all 0.2s;
  appearance: none;
}

select:focus, textarea:focus {
  outline: none;
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

select:disabled, textarea:disabled {
  background: #f1f5f9;
  cursor: not-allowed;
  opacity: 0.7;
}

textarea {
  resize: vertical;
  min-height: 120px;
}

/* Custom Radio */
.custom-radio-group {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

@media (max-width: 500px) {
  .custom-radio-group {
    grid-template-columns: 1fr;
  }
}

.radio-card {
  position: relative;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.radio-card input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

.radio-content {
  padding: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
}

.radio-content i {
  font-size: 20px;
}

.text-success { color: #10b981; }
.text-danger { color: #ef4444; }
.text-warning { color: #f59e0b; }

.radio-card:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.radio-card.selected {
  background: #eff6ff;
  border-color: #3b82f6;
}

.radio-card.selected .radio-content {
  color: #2563eb;
}

/* Messages */
.message-banner {
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 24px;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 10px;
}

.message-banner.error {
  background: #fef2f2;
  color: #dc2626;
  border-left: 4px solid #dc2626;
}

.message-banner.success {
  background: #ecfdf5;
  color: #059669;
  border-left: 4px solid #059669;
}

/* Submit Button */
.submit-btn {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -5px rgba(59, 130, 246, 0.5);
}

.submit-btn:disabled {
  background: #94a3b8;
  box-shadow: none;
  cursor: not-allowed;
  transform: none;
}

/* Transitions */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>
