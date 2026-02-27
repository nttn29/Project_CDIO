<template>
  <div class="page">
    <div class="header-section">
      <div>
        <h2>Hệ thống & Cấu hình</h2>
        <p class="subtitle">Quản lý các danh mục cấu hình, loại sự cố bảo trì</p>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-tools"></i> Danh mục loại sự cố</h3>
        <button class="btn-primary" @click="openCreateModal">
          <i class="fas fa-plus"></i> Thêm mới
        </button>
      </div>
      
      <div class="card-body">
        <div v-if="loading" class="text-center">
          <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
        </div>
        
        <table v-else class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên sự cố</th>
              <th>Mức ưu tiên (1-4)</th>
              <th>Mô tả</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in issueTypes" :key="item.id_loai_su_co">
              <td>{{ item.id_loai_su_co }}</td>
              <td><strong>{{ item.ten_loai }}</strong></td>
              <td>
                <span class="priority-badge" :class="getPriorityClass(item.muc_uu_tien)">
                  {{ item.muc_uu_tien }}
                </span>
              </td>
              <td>{{ item.mo_ta || 'Không có mô tả' }}</td>
              <td class="actions">
                <button class="btn-icon text-primary" @click="openEditModal(item)">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon text-danger" @click="confirmDelete(item.id_loai_su_co)">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr v-if="issueTypes.length === 0">
              <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL THEME -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="custom-modal">
          <div class="modal-header">
            <h3>{{ isEditing ? 'Chỉnh sửa Loại sự cố' : 'Thêm Loại sự cố mới' }}</h3>
            <button class="btn-close" @click="closeModal"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Tên loại sự cố <span class="text-danger">*</span></label>
              <input type="text" class="form-control" v-model="form.ten_loai" placeholder="VD: Mất điện, Rò rỉ nước..." />
            </div>
            <div class="form-group">
              <label>Mức ưu tiên (1 = Cao nhất, 4 = Thấp nhất) <span class="text-danger">*</span></label>
              <select class="form-control" v-model="form.muc_uu_tien">
                <option value="1">1 - Rất khẩn cấp</option>
                <option value="2">2 - Khẩn cấp</option>
                <option value="3">3 - Bình thường</option>
                <option value="4">4 - Ưu tiên thấp</option>
              </select>
            </div>
            <div class="form-group">
              <label>Mô tả chi tiết</label>
              <textarea class="form-control" v-model="form.mo_ta" rows="3" placeholder="Mô tả về nhóm sự cố này..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-secondary" @click="closeModal">Hủy bỏ</button>
            <button class="btn-primary" @click="submitForm" :disabled="!form.ten_loai || processing">
              <i v-if="processing" class="fas fa-spinner fa-spin"></i>
              <span v-else><i class="fas fa-save"></i> Lặp dữ liệu</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const issueTypes = ref([]);
const loading = ref(true);
const processing = ref(false);
const showModal = ref(false);
const isEditing = ref(false);

const form = ref({
  id_loai_su_co: null,
  ten_loai: '',
  muc_uu_tien: '3',
  mo_ta: ''
});

onMounted(() => {
  fetchIssueTypes();
});

async function fetchIssueTypes() {
  loading.value = true;
  try {
    const res = await api.get('/loai-su-co');
    if (res.data) {
      issueTypes.value = res.data;
    }
  } catch (error) {
    console.error('Lỗi khi tải danh mục sự cố:', error);
  } finally {
    loading.value = false;
  }
}

function openCreateModal() {
  isEditing.value = false;
  form.value = { id_loai_su_co: null, ten_loai: '', muc_uu_tien: '3', mo_ta: '' };
  showModal.value = true;
}

function openEditModal(item) {
  isEditing.value = true;
  form.value = { ...item };
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
}

async function submitForm() {
  if (!form.value.ten_loai) return;
  processing.value = true;
  
  try {
    const payload = {
      ten_loai: form.value.ten_loai,
      muc_uu_tien: form.value.muc_uu_tien,
      mo_ta: form.value.mo_ta
    };

    if (isEditing.value) {
      await api.put(`/loai-su-co/${form.value.id_loai_su_co}`, payload);
    } else {
      await api.post('/loai-su-co', payload);
    }
    
    await fetchIssueTypes();
    closeModal();
  } catch (err) {
    console.error('Lỗi lưu dữ liệu:', err);
    alert('Đã có lỗi xảy ra!');
  } finally {
    processing.value = false;
  }
}

async function confirmDelete(id) {
  if (confirm('Bạn có chắc chắn muốn xóa danh mục này? Các yêu cầu bảo trì liên quan có thể bị ảnh hưởng.')) {
    try {
      await api.delete(`/loai-su-co/${id}`);
      await fetchIssueTypes();
    } catch (err) {
      console.error('Lỗi xóa:', err);
      alert('Không thể xóa mục này vì có thể đã có yêu cầu gắn với nó.');
    }
  }
}

function getPriorityClass(level) {
  if (level == 1) return 'bg-danger text-white';
  if (level == 2) return 'bg-warning text-dark';
  if (level == 3) return 'bg-info text-white';
  return 'bg-secondary text-white';
}
</script>

<style scoped>
.page {
  padding: 30px;
  background: #f8fafc;
  min-height: calc(100vh - 70px);
  font-family: 'Segoe UI', system-ui, sans-serif;
  color: #334155;
}

.header-section {
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

.card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.02);
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.card-header {
  padding: 16px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fff;
}

.card-header h3 {
  margin: 0;
  font-size: 16px;
  color: #1e293b;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}
.btn-primary:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.btn-secondary:hover { background: #e2e8f0; }

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  padding: 14px 24px;
  border-bottom: 1px solid #f1f5f9;
  text-align: left;
}

.table th {
  background: #f8fafc;
  font-weight: 600;
  color: #64748b;
  font-size: 13px;
  text-transform: uppercase;
}

.priority-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
}

.bg-danger { background: #ef4444; }
.bg-warning { background: #f59e0b; }
.bg-info { background: #3b82f6; }
.bg-secondary { background: #94a3b8; }
.text-white { color: white; }
.text-dark { color: #1e293b; }

.actions { display: flex; gap: 8px; }

.btn-icon {
  background: transparent;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
  font-size: 14px;
}
.btn-icon:hover { background: #f1f5f9; }
.text-primary { color: #3b82f6; }
.text-danger { color: #ef4444; }
.text-muted { color: #94a3b8; }
.text-center { text-align: center; }

/* MODAL STYLES */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.4);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(2px);
}

.custom-modal {
  background: white;
  width: 100%;
  max-width: 480px;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
  animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-header {
  padding: 16px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h3 { margin: 0; font-size: 16px; color: #0f172a; }
.btn-close {
  background: transparent;
  border: none;
  font-size: 18px;
  color: #94a3b8;
  cursor: pointer;
}
.btn-close:hover { color: #ef4444; }

.modal-body { padding: 24px; }
.form-group { margin-bottom: 16px; }
.form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #475569;
}
.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-family: inherit;
  font-size: 14px;
}
.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-footer {
  padding: 16px 24px;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  border-radius: 0 0 16px 16px;
}
</style>
