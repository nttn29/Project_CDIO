<template>
  <div class="invoice-container">
    <div class="no-print actions-top">
      <div v-if="!selectedRequest" class="selector-box">
        <h3>🏠 Chọn yêu cầu bảo trì đã hoàn thành để in hóa đơn</h3>
        
        <div v-if="loading" class="note">Đang tải dữ liệu...</div>
        <div v-if="!loading && loadError" class="note">{{ loadError }}</div>
        
        <table v-if="!loading && completedRequests.length > 0" class="requests-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Khách hàng</th>
              <th>Căn hộ</th>
              <th>Ngày hoàn thành</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="req in completedRequests" :key="req.id_yeu_cau">
              <td>#{{ req.id_yeu_cau }}</td>
              <td>{{ req.chu_ho?.ten || 'Không rõ' }}</td>
              <td>{{ req.can_ho?.so_can_ho || 'Không rõ' }}</td>
              <td>{{ new Date(req.updated_at).toLocaleDateString('vi-VN') }}</td>
              <td>
                <button class="btn-select" @click="selectRequest(req)">In hóa đơn</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!loading && completedRequests.length === 0" class="note">
          Không có yêu cầu nào đã hoàn thành.
        </div>
      </div>

      <div v-else class="action-buttons">
        <button class="btn-back" @click="selectedRequest = null">← Chọn lại</button>
        <button class="btn-print" @click="printInvoice">🖨️ In hóa đơn ngay</button>
      </div>
    </div>

    <section v-if="selectedRequest" class="invoice-paper printable-area" id="invoice-section">
      <div class="invoice-header">
        <div class="brand">
          <h1>HÓA ĐƠN BẢO TRÌ DỊCH VỤ</h1>
          <p class="invoice-no">Mã hóa đơn: #HD-{{ selectedRequest.id_yeu_cau }}-{{ new Date().getFullYear() }}</p>
        </div>
        <div class="meta">
          <p>Ngày lập: {{ new Date().toLocaleDateString('vi-VN') }}</p>
        </div>
      </div>

      <div class="address-grid">
        <div class="col">
          <h3 class="label">THÔNG TIN KHÁCH HÀNG</h3>
          <p class="value">
            <strong>{{ selectedRequest.chu_ho?.ten || 'Cư dân' }}</strong>
          </p>
          <p class="sub-value">SĐT: {{ selectedRequest.chu_ho?.dien_thoai || 'Chưa cập nhật' }}</p>
          <p class="sub-value">Căn hộ: {{ selectedRequest.can_ho?.so_can_ho || 'Chưa cập nhật' }} - Tòa nhà EcoHome</p>
        </div>
        <div class="col">
          <h3 class="label">ĐƠN VỊ THỰC HIỆN</h3>
          <p class="value">
            <strong>Ban Quản Lý EcoHome</strong>
          </p>
          <p class="sub-value">Đội Kỹ thuật & Bảo trì</p>
          <p class="sub-value">Hotline: 1900 1234</p>
        </div>
      </div>

      <div class="task-detail">
        <h3 class="label">NỘI DUNG YÊU CẦU XỬ LÝ</h3>
        <div class="task-box">
          <strong>Sự cố:</strong> {{ selectedRequest.loai_su_co?.ten_loai || 'Khác' }} <br/>
          <strong>Mô tả:</strong> {{ selectedRequest.mo_ta }}
        </div>
      </div>

      <table class="cost-table">
        <thead>
          <tr>
            <th>Mô tả chi tiết</th>
            <th class="text-middle">Số lượng</th>
            <th class="text-right">Đơn giá (VNĐ)</th>
            <th class="text-right">Thành tiền (VNĐ)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Phí bảo trì dịch vụ ({{ selectedRequest.loai_su_co?.ten_loai || 'Sửa chữa chung' }})</td>
            <td class="text-middle">1</td>
            <td class="text-right">{{ formatCurrency(mockCost) }}</td>
            <td class="text-right">{{ formatCurrency(mockCost) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3"><strong>TỔNG CỘNG</strong></td>
            <td class="text-right total-price">
              {{ formatCurrency(mockCost) }}
            </td>
          </tr>
        </tfoot>
      </table>

      <div class="signature-area">
        <div class="sig-block">
          <p>Đại diện Khách hàng</p>
          <span class="sig-guide">(Ký và ghi rõ họ tên)</span>
          <div class="sig-gap"></div>
          <p class="sig-name">{{ selectedRequest.chu_ho?.ten || 'Cư dân' }}</p>
        </div>
        <div class="sig-block">
          <p>Đại diện Ban Quản Lý</p>
          <span class="sig-guide">(Ký và ghi chú)</span>
          <div class="sig-gap"></div>
          <p class="sig-name">Ban Quản Lý</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from '@/services/api';

const completedRequests = ref([]);
const loading = ref(false);
const selectedRequest = ref(null);
const loadError = ref("");

const mockCost = 250000; // Hoặc lấy từ Backend nếu bảng có cột chi phí

const formatCurrency = (val) => new Intl.NumberFormat("vi-VN").format(val);

onMounted(async () => {
  fetchRequests();
});

async function fetchRequests() {
  loading.value = true;
  loadError.value = "";
  try {
    let payload = [];
    try {
      const res = await api.get('/yeu-cau-bao-tri');
      payload = Array.isArray(res.data) ? res.data : (res.data?.data || []);
    } catch (primaryError) {
      const fallback = await api.get('/yeu_cau');
      payload = Array.isArray(fallback.data) ? fallback.data : (fallback.data?.data || []);
    }

    const doneStatuses = new Set(['hoan_thanh', 'completed', 'done', 'da_hoan_thanh']);
    const normalize = (v) => String(v || '').trim().toLowerCase();
    completedRequests.value = payload.filter((r) => {
      const requestStatus = normalize(r?.trang_thai);
      const assignmentStatus = normalize(r?.phan_cong?.trang_thai);
      return doneStatuses.has(requestStatus) || doneStatuses.has(assignmentStatus);
    });
  } catch (error) {
    console.error("Lỗi khi tải yêu cầu bảo trì", error);
    loadError.value = "Không tải được dữ liệu yêu cầu bảo trì.";
  } finally {
    loading.value = false;
  }
}

function selectRequest(req) {
  selectedRequest.value = req;
}

function printInvoice() {
  window.print();
}
</script>

<style scoped>
/* GIAO DIỆN HIỂN THỊ TRÊN WEB (Dashboard) */
.invoice-container {
  background-color: #f1f5f9;
  min-height: 80vh;
  padding: 40px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.selector-box {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  width: 100%;
  max-width: 900px;
  margin-bottom: 30px;
}

.selector-box h3 {
  color: #1e293b;
  margin-bottom: 20px;
  font-size: 20px;
}

.requests-table {
  width: 100%;
  border-collapse: collapse;
}

.requests-table th, .requests-table td {
  border-bottom: 1px solid #e2e8f0;
  padding: 12px 15px;
  text-align: left;
}

.requests-table th {
  background: #f8fafc;
  color: #475569;
  font-size: 14px;
}

.btn-select {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  transition: 0.2s;
}

.btn-select:hover {
  background: #2563eb;
}

.actions-top {
  margin-bottom: 20px;
  width: 210mm;
}

.action-buttons {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.invoice-paper {
  background: white;
  width: 210mm;
  min-height: 297mm;
  padding: 20mm;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  box-sizing: border-box;
}

.invoice-header {
  display: flex;
  justify-content: space-between;
  border-bottom: 2px solid #1e293b;
  padding-bottom: 15px;
  margin-bottom: 30px;
}

.invoice-header h1 {
  margin: 0;
  font-size: 24px;
  color: #0f172a;
  font-weight: 800;
}
.invoice-no {
  color: #3b82f6;
  font-weight: bold;
  margin-top: 5px;
}

.address-grid {
  display: flex;
  justify-content: space-between;
  margin-bottom: 30px;
}
.label {
  font-size: 14px;
  color: #64748b;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 5px;
  margin-bottom: 10px;
  text-transform: uppercase;
}
.sub-value {
  margin: 4px 0;
  font-size: 14px;
  color: #334155;
}

.task-box {
  border: 1px dashed #cbd5e1;
  background: #f8fafc;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 30px;
  line-height: 1.6;
  color: #1e293b;
}

.cost-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 50px;
}
.cost-table th {
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  padding: 12px;
  color: #334155;
}
.cost-table td {
  border: 1px solid #cbd5e1;
  padding: 12px;
  color: #0f172a;
}
.total-price {
  font-size: 20px;
  font-weight: bold;
  color: #0f172a;
}
.text-right {
  text-align: right;
}
.text-middle {
  text-align: center;
}

.signature-area {
  display: flex;
  justify-content: space-around;
  text-align: center;
  margin-top: 40px;
}
.sig-guide {
  font-size: 12px;
  font-style: italic;
  color: #64748b;
}
.sig-gap {
  height: 80px;
}
.sig-name {
  font-weight: bold;
  text-transform: uppercase;
}

.btn-print {
  background: #e67e22;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: 0.2s;
}
.btn-print:hover { background: #d35400; }

.btn-back {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}
.btn-back:hover { background: #e2e8f0; }

/* ============================================================
   PHẦN QUAN TRỌNG: CSS KHI IN (FIX TRIỆT ĐỂ SIDEBAR)
   ============================================================ */
@media print {
  body * {
    visibility: hidden !important;
  }
  .printable-area, .printable-area * {
    visibility: visible !important;
  }
  .printable-area {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 210mm !important;
    height: 297mm !important;
    margin: 0 !important;
    padding: 15mm !important;
    background: white !important;
    box-shadow: none !important;
    z-index: 99999999 !important;
  }
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: white !important;
    width: 210mm;
    height: 297mm;
  }
  .no-print, .sidebar, .header, nav, aside {
    display: none !important;
  }
  @page {
    size: A4;
    margin: 0;
  }
}
</style>
