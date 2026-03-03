<template>
  <div class="page">
    <div class="header-section">
      <div>
        <h2>Phân công kỹ thuật viên</h2>
        <p class="subtitle">Quản lý và điều phối công việc bảo trì cho tòa nhà</p>
      </div>
      <div class="header-stats">
        <div class="stat-badge warning">
          <i class="fas fa-exclamation-circle"></i>
          <span>{{ pendingRequests.length }} Yêu cầu chờ phân công</span>
        </div>
        <div class="stat-badge info">
          <i class="fas fa-users-cog"></i>
          <span>{{ technicians.length }} Kỹ thuật viên</span>
        </div>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
    </div>

    <div class="content-grid" v-else>
      <!-- Cột Trái: Danh sách yêu cầu chờ xử lý -->
      <div class="panel list-panel">
        <div class="panel-header">
          <h3>Yêu cầu chờ được phân công</h3>
        </div>
        
        <div class="panel-body">
          <div v-if="pendingRequests.length === 0" class="empty-state">
            <i class="fas fa-check-circle"></i>
            <p>Tuyệt vời! Không còn yêu cầu nào chờ phân công.</p>
          </div>
          
          <div v-else class="request-list">
            <div class="request-card" v-for="req in pendingRequests" :key="req.id_yeu_cau">
              <div class="card-header">
                <div>
                  <span class="req-id">#{{ req.id_yeu_cau }}</span>
                  <span class="badge" :class="getPriorityClass(req.thoi_gian_uu_tien)">
                    {{ getPriorityLabel(req.thoi_gian_uu_tien) }}
                  </span>
                </div>
                <span class="req-date">{{ new Date(req.created_at).toLocaleDateString('vi-VN') }}</span>
              </div>
              
              <div class="card-body">
                <h4 class="issue-type"><i class="fas fa-tools"></i> {{ req.loai_su_co?.ten_loai || 'Sự cố chung' }}</h4>
                <p class="issue-desc">{{ req.mo_ta }}</p>
                
                <div class="customer-info">
                  <div class="info-item">
                    <i class="fas fa-user"></i> {{ req.chu_ho?.ten || 'Không rõ' }}
                  </div>
                  <div class="info-item">
                    <i class="fas fa-door-open"></i> P.{{ req.can_ho?.so_can_ho || '---' }}
                  </div>
                </div>
              </div>
              
              <div class="card-footer">
                <button class="btn-primary" @click="openAssignModal(req)">
                  <i class="fas fa-hand-pointer"></i> Phân công ngay
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột Phải: Danh sách Kỹ thuật viên -->
      <div class="panel tech-panel">
        <div class="panel-header">
          <h3>Đội ngũ kỹ thuật viên</h3>
        </div>
        
        <div class="panel-body">
          <div class="tech-list">
            <div class="tech-card" v-for="tech in technicians" :key="tech.id_nguoi_dung">
              <div class="tech-avatar">
                <i class="fas fa-user-cog"></i>
              </div>
              <div class="tech-info">
                <h4>{{ tech.ten }}</h4>
                <p><i class="fas fa-phone-alt"></i> {{ tech.dien_thoai || 'Chưa cập nhật' }}</p>
              </div>
              <div class="tech-actions">
                <button class="btn-icon" title="Xem công việc" @click="viewTechJobs(tech)">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL PHÂN CÔNG -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="custom-modal">
          <div class="modal-header">
            <h3>Phân công công việc #{{ selectedReq?.id_yeu_cau }}</h3>
            <button class="btn-close" @click="closeModal"><i class="fas fa-times"></i></button>
          </div>
          
          <div class="modal-body">
            <div class="req-summary">
              <strong>Sự cố:</strong> {{ selectedReq?.loai_su_co?.ten_loai || 'Khác' }} <br/>
              <strong>Căn hộ:</strong> P.{{ selectedReq?.can_ho?.so_can_ho || '---' }} <br/>
              <strong>Mô tả:</strong> <span class="text-muted">{{ selectedReq?.mo_ta }}</span>
            </div>
            
            <div class="form-group">
              <label>Chọn kỹ thuật viên thực hiện <span class="text-danger">*</span></label>
              <select v-model="assignForm.id_ky_thuat_vien" class="form-control">
                <option value="" disabled>-- Vui lòng chọn kỹ thuật viên --</option>
                <option v-for="t in technicians" :key="t.id_nguoi_dung" :value="t.id_nguoi_dung">
                  {{ t.ten }} (ĐT: {{ t.dien_thoai || '---' }})
                </option>
              </select>
            </div>
            
            <div class="grid-2">
              <div class="form-group">
                <label>Ngày thực hiện <span class="text-danger">*</span></label>
                <input type="date" v-model="assignForm.ngay_phan_cong" class="form-control" />
              </div>
              <div class="form-group">
                <label>Giờ hẹn <span class="text-danger">*</span></label>
                <input type="time" v-model="assignForm.gio_hen" class="form-control" />
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button class="btn-secondary" @click="closeModal">Hủy bỏ</button>
            <button 
              class="btn-success" 
              :disabled="!assignForm.id_ky_thuat_vien || !assignForm.ngay_phan_cong || !assignForm.gio_hen || processing"
              @click="submitAssignment"
            >
              <i v-if="processing" class="fas fa-spinner fa-spin"></i>
              <span v-else><i class="fas fa-check"></i> Xác nhận Phân công</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';

const route = useRoute();

/* ======= STATE ======= */
const pendingRequests = ref([]);
const technicians = ref([]);
const loading = ref(true);
const processing = ref(false);

const showModal = ref(false);
const selectedReq = ref(null);
const assignForm = ref({
  id_ky_thuat_vien: '',
  ngay_phan_cong: '',
  gio_hen: ''
});

/* ======= METHODS ======= */
onMounted(async () => {
  await fetchData();
  const highlightId = route.query.highlight;
  if (highlightId) {
    const target = pendingRequests.value.find(
      r => String(r.id_yeu_cau) === String(highlightId)
    );
    if (target) openAssignModal(target);
  }
});

async function fetchData() {
  loading.value = true;
  try {
    // 1. Fetch approved requests waiting for assignment
    // Use the same endpoint used by other pages to avoid auth:sanctum mismatch.
    let requestRows = [];
    try {
      const resReq = await api.get('/yeu-cau-bao-tri');
      requestRows = resReq.data || [];
    } catch (primaryErr) {
      const fallback = await api.get('/yeu_cau');
      requestRows = fallback.data || [];
    }
    // Hiển thị tất cả yêu cầu chưa hoàn thành (mới, chờ xử lý, đã duyệt but not yet done)
    pendingRequests.value = requestRows.filter(r =>
      ['moi', 'cho_xu_ly', 'da_xac_nhan', 'pending'].includes(r.trang_thai)
    );
    
    // 2. Fetch technicians for assignment
    const resUsers = await api.get('/technicians');
    if (resUsers.data) {
      // Accept all technician role variants
      technicians.value = resUsers.data.filter(u =>
        ['nhan_vien', 'technician', 'ky_thuat_vien'].includes(u.vai_tro)
      );
    }
  } catch (error) {
    console.error('Lỗi lấy dữ liệu:', error);
  } finally {
    loading.value = false;
  }
}

function openAssignModal(req) {
  selectedReq.value = req;
  // Reset form
  const today = new Date().toISOString().split('T')[0];
  assignForm.value = {
    id_ky_thuat_vien: '',
    ngay_phan_cong: today,
    gio_hen: '09:00'
  };
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  selectedReq.value = null;
}

async function submitAssignment() {
  if (!selectedReq.value) return;
  processing.value = true;
  
  try {
    // 0. Nếu yêu cầu chưa được duyệt, tự động duyệt trước khi phân công
    const needsApproval = ['moi', 'cho_xu_ly', 'pending'].includes(selectedReq.value.trang_thai);
    if (needsApproval) {
      await api.post(`/yeu-cau-bao-tri/${selectedReq.value.id_yeu_cau}/status`, {
        status: 'da_xac_nhan'
      });
    }

    // 1. Tạo bản ghi phân công
    await api.post('/phan-cong', {
      id_yeu_cau: selectedReq.value.id_yeu_cau,
      id_ky_thuat_vien: assignForm.value.id_ky_thuat_vien,
      ngay_phan_cong: assignForm.value.ngay_phan_cong,
      gio_hen: assignForm.value.gio_hen,
      trang_thai: 'cho_thuc_hien'
    });
    
    // 2. Chuyển trạng thái yêu cầu sang "Đang xử lý"
    await api.post(`/yeu-cau-bao-tri/${selectedReq.value.id_yeu_cau}/status`, {
      status: 'dang_xu_ly'
    });
    
    // 3. Reload data and close
    await fetchData();
    closeModal();
    alert('✅ Phân công công việc thành công!');
    
  } catch (err) {
    console.error('Lỗi khi phân công:', err);
    alert('Có lỗi xảy ra, vui lòng thử lại.');
  } finally {
    processing.value = false;
  }
}

function viewTechJobs(tech) {
  alert(`Chức năng xem lịch làm việc của ${tech.ten} sẽ được cập nhật sớm.`);
}

/* ======= HELPERS ======= */
function getPriorityLabel(val) {
  const map = {
    gap: 'Khẩn cấp',
    khan_cap: 'Khẩn cấp',
    cao: 'Cao',
    binh_thuong: 'Bình thường',
    trung_binh: 'Bình thường',
    thap: 'Thấp',
    kho: 'Thấp',
  };
  return map[val] || 'Chưa rõ';
}

function getPriorityClass(val) {
  if (['gap', 'khan_cap', 'cao'].includes(val)) return 'badge-danger';
  if (['binh_thuong', 'trung_binh'].includes(val)) return 'badge-success';
  if (['thap', 'kho'].includes(val)) return 'badge-warning';
  return 'badge-secondary';
}
</script>

<style scoped>
/* GENERAL STYLES */
.page {
  padding: 30px;
  background: #f8fafc;
  min-height: calc(100vh - 70px);
  font-family: 'Segoe UI', system-ui, sans-serif;
  color: #334155;
}

.text-danger { color: #ef4444; }
.text-muted { color: #64748b; }

/* HEADER SECTION */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 30px;
}

.header-section h2 {
  font-size: 26px;
  color: #0f172a;
  margin: 0 0 8px;
  font-weight: 700;
}

.subtitle {
  color: #64748b;
  margin: 0;
  font-size: 15px;
}

.header-stats {
  display: flex;
  gap: 15px;
}

.stat-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 14px;
}

.stat-badge.warning {
  background: #fffbeb;
  color: #d97706;
  border: 1px solid #fde68a;
}

.stat-badge.info {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
}

/* CONTENT GRID */
.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
}

@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

.panel {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.03);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.panel-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.panel-header h3 {
  margin: 0;
  font-size: 18px;
  color: #1e293b;
  font-weight: 600;
}

.panel-body {
  padding: 24px;
  flex: 1;
}

/* LIST OF REQUESTS */
.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #10b981;
}
.empty-state i {
  font-size: 40px;
  margin-bottom: 15px;
}

.request-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.request-card {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px;
  transition: all 0.2s;
}

.request-card:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px dashed #e2e8f0;
}

.req-id {
  font-weight: 700;
  color: #475569;
  margin-right: 10px;
  font-size: 15px;
}

.badge {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
}
.badge-danger { background: #fee2e2; color: #b91c1c; }
.badge-success { background: #dcfce7; color: #16a34a; }
.badge-warning { background: #fef9c3; color: #a16207; }

.req-date {
  font-size: 13px;
  color: #94a3b8;
}

.issue-type {
  margin: 0 0 8px;
  font-size: 16px;
  color: #0f172a;
}
.issue-type i { color: #3b82f6; margin-right: 6px; }

.issue-desc {
  font-size: 14px;
  color: #475569;
  line-height: 1.5;
  margin: 0 0 16px;
}

.customer-info {
  display: flex;
  gap: 20px;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 16px;
}
.customer-info i { color: #94a3b8; }

.card-footer {
  text-align: right;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.btn-primary:hover { background: #2563eb; }

/* TECHNICIAN LIST */
.tech-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.tech-card {
  display: flex;
  align-items: center;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid #f1f5f9;
  background: #f8fafc;
  transition: 0.2s;
}
.tech-card:hover { background: #f1f5f9; }

.tech-avatar {
  width: 40px;
  height: 40px;
  background: #e2e8f0;
  color: #475569;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-right: 15px;
}

.tech-info {
  flex: 1;
}
.tech-info h4 { margin: 0 0 4px; font-size: 14px; color: #1e293b; }
.tech-info p { margin: 0; font-size: 12px; color: #64748b; }

.btn-icon {
  background: white;
  color: #64748b;
  border: 1px solid #e2e8f0;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-icon:hover { background: #e2e8f0; color: #3b82f6; }

/* MODAL OVERLAY */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
}

.custom-modal {
  background: white;
  width: 100%;
  max-width: 500px;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
  overflow: hidden;
  animation: modalIn 0.3s ease-out;
}

@keyframes modalIn {
  from { opacity: 0; transform: translateY(20px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h3 { margin: 0; font-size: 18px; color: #0f172a; }
.btn-close {
  background: transparent;
  border: none;
  font-size: 20px;
  color: #94a3b8;
  cursor: pointer;
}
.btn-close:hover { color: #ef4444; }

.modal-body { padding: 24px; }

.req-summary {
  background: #f8fafc;
  padding: 16px;
  border-radius: 10px;
  font-size: 14px;
  line-height: 1.6;
  margin-bottom: 24px;
  border: 1px solid #e2e8f0;
}

.form-group { margin-bottom: 16px; }
.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #334155;
}
.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-family: inherit;
  font-size: 14px;
}
.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.modal-footer {
  padding: 16px 24px;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn-secondary {
  background: white;
  border: 1px solid #cbd5e1;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}
.btn-secondary:hover { background: #f1f5f9; }

.btn-success {
  background: #10b981;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}
.btn-success:hover:not(:disabled) { background: #059669; }
.btn-success:disabled { background: #94a3b8; cursor: not-allowed; }
</style>
