<template>
  <div class="page">
    <!-- HEADER -->
    <div class="header-section">
      <div>
        <h2>Quản lý Chi Phí Bảo Trì</h2>
        <p class="subtitle">Tổng hợp WorkLog và chi phí từ kỹ thuật viên</p>
      </div>
      <div class="header-stats">
        <div class="stat-badge purple">
          <i class="fas fa-file-invoice-dollar"></i>
          <span>{{ logs.length }} WorkLog</span>
        </div>
        <div class="stat-badge green">
          <i class="fas fa-coins"></i>
          <span>{{ formatCurrency(totalCost) }} tổng chi phí</span>
        </div>
      </div>
    </div>

    <!-- BỘ LỌC -->
    <div class="filter-bar">
      <div class="filter-group">
        <label><i class="fas fa-search"></i></label>
        <input v-model="search" type="text" placeholder="Tìm theo tên kỹ thuật viên, nội dung..." />
      </div>
      <div class="filter-group">
        <label><i class="fas fa-calendar"></i></label>
        <input v-model="filterFrom" type="date" title="Từ ngày" />
        <span class="sep">–</span>
        <input v-model="filterTo" type="date" title="Đến ngày" />
      </div>
      <button class="btn-refresh" @click="fetchLogs" :disabled="loading">
        <i class="fas fa-rotate" :class="{ 'fa-spin': loading }"></i> Làm mới
      </button>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
    </div>

    <!-- EMPTY -->
    <div v-else-if="filtered.length === 0" class="empty-state">
      <div class="empty-icon">📋</div>
      <h3>Chưa có WorkLog nào</h3>
      <p>Kỹ thuật viên chưa ghi nhận chi phí hoặc chưa có dữ liệu phù hợp với bộ lọc.</p>
    </div>

    <!-- BẢNG WORKLOG -->
    <div v-else class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Kỹ thuật viên</th>
            <th>Yêu cầu</th>
            <th>Nội dung</th>
            <th>Thời gian</th>
            <th>Thời lượng</th>
            <th>Vật tư</th>
            <th>Chi phí phát sinh</th>
            <th>Tổng chi phí</th>
            <th>Ghi chú</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(log, idx) in filtered" :key="log.id || idx" @click="openDetail(log)" class="clickable-row">
            <td>{{ idx + 1 }}</td>
            <td>
              <div class="tech-cell">
                <div class="tech-avatar-sm"><i class="fas fa-user-cog"></i></div>
                <span>{{ log.technicianName || 'Không rõ' }}</span>
              </div>
            </td>
            <td>
              <span v-if="log.jobCode" class="req-badge">#{{ log.jobCode }}</span>
              <span v-else class="text-gray">—</span>
            </td>
            <td class="content-cell">{{ log.content }}</td>
            <td class="time-cell">
              <div>{{ log.startTime }}</div>
              <div class="text-gray">→ {{ log.endTime }}</div>
            </td>
            <td><span class="duration-badge">{{ log.duration }}</span></td>
            <td>
              <div v-if="log.materials && log.materials.length > 0" class="tag-list">
                <span v-for="(m, mi) in log.materials" :key="mi" class="mat-tag">
                  {{ m.name }} ×{{ m.qty }}
                </span>
              </div>
              <span v-else class="text-gray">—</span>
            </td>
            <td>
              <div v-if="log.extraCosts && log.extraCosts.length > 0" class="tag-list">
                <span v-for="(c, ci) in log.extraCosts" :key="ci" class="extra-tag">
                  {{ c.desc }}: {{ formatCurrency(c.amount) }}
                </span>
              </div>
              <span v-else class="text-gray">—</span>
            </td>
            <td class="cost-cell">
              <strong class="cost-num">{{ formatCurrency(log.totalCost) }}</strong>
            </td>
            <td class="note-cell">{{ log.note || '—' }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="total-row">
            <td colspan="8" class="text-right"><strong>Tổng cộng:</strong></td>
            <td class="cost-cell"><strong class="cost-num total">{{ formatCurrency(filteredTotal) }}</strong></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- MODAL CHI TIẾT -->
    <Teleport to="body">
      <div v-if="detailLog" class="modal-overlay" @click.self="detailLog = null">
        <div class="detail-modal">
          <div class="modal-header">
            <h3><i class="fas fa-file-lines"></i> Chi tiết WorkLog</h3>
            <button @click="detailLog = null" class="btn-close"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
            <div class="detail-grid">
              <div class="detail-item"><span class="lbl">Kỹ thuật viên</span><span>{{ detailLog.technicianName || 'Không rõ' }}</span></div>
              <div class="detail-item"><span class="lbl">Yêu cầu</span><span>{{ detailLog.jobCode ? '#' + detailLog.jobCode : '—' }}</span></div>
              <div class="detail-item full"><span class="lbl">Nội dung</span><span>{{ detailLog.content }}</span></div>
              <div class="detail-item"><span class="lbl">Bắt đầu</span><span>{{ detailLog.startTime }}</span></div>
              <div class="detail-item"><span class="lbl">Kết thúc</span><span>{{ detailLog.endTime }}</span></div>
              <div class="detail-item"><span class="lbl">Thời lượng</span><span class="duration-badge">{{ detailLog.duration }}</span></div>
            </div>

            <div v-if="detailLog.materials && detailLog.materials.length > 0" class="detail-section">
              <h4><i class="fas fa-boxes-stacked"></i> Vật tư sử dụng</h4>
              <table class="mini-table">
                <thead><tr><th>Tên vật tư</th><th>SL</th><th>Đơn vị</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                <tbody>
                  <tr v-for="(m, i) in detailLog.materials" :key="i">
                    <td>{{ m.name }}</td>
                    <td>{{ m.qty }}</td>
                    <td>{{ m.unit }}</td>
                    <td>{{ formatCurrency(m.price) }}</td>
                    <td><strong>{{ formatCurrency(m.qty * m.price) }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="detailLog.extraCosts && detailLog.extraCosts.length > 0" class="detail-section">
              <h4><i class="fas fa-money-bill-wave"></i> Chi phí phát sinh</h4>
              <table class="mini-table">
                <thead><tr><th>Mô tả</th><th>Số tiền</th></tr></thead>
                <tbody>
                  <tr v-for="(c, i) in detailLog.extraCosts" :key="i">
                    <td>{{ c.desc }}</td>
                    <td><strong>{{ formatCurrency(c.amount) }}</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="detailLog.note" class="detail-note">
              <i class="fas fa-note-sticky"></i> {{ detailLog.note }}
            </div>

            <div class="detail-total">
              Tổng chi phí: <strong class="cost-num total">{{ formatCurrency(detailLog.totalCost) }}</strong>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/services/api';

const logs = ref([]);
const loading = ref(false);
const search = ref('');
const filterFrom = ref('');
const filterTo = ref('');
const detailLog = ref(null);

onMounted(fetchLogs);

async function fetchLogs() {
  loading.value = true;
  try {
    const res = await api.get('/work-logs');
    const data = res.data?.data || res.data || [];
    logs.value = data.map(mapApiLog);
  } catch (err) {
    console.error('Lỗi lấy worklog:', err);
    logs.value = [];
  } finally {
    loading.value = false;
  }
}

function mapApiLog(item) {
  let materials = [];
  let extraCosts = [];
  try { materials = typeof item.vat_tu === 'string' ? JSON.parse(item.vat_tu) : (item.vat_tu || item.materials || []); } catch {}
  try { extraCosts = typeof item.chi_phi_phat_sinh === 'string' ? JSON.parse(item.chi_phi_phat_sinh) : (item.chi_phi_phat_sinh || item.extraCosts || []); } catch {}
  return {
    id: item.id,
    technicianName: item.technician?.ten || item.technician?.name || item.technician_name || 'Không rõ',
    jobCode: item.yeu_cau?.id_yeu_cau || item.id_yeu_cau || item.job_code || '',
    content: item.noi_dung || item.content || '',
    startTime: formatDisplayDate(item.thoi_gian_bat_dau || item.start_time),
    endTime: formatDisplayDate(item.thoi_gian_ket_thuc || item.end_time),
    duration: item.thoi_luong || item.duration || '',
    materials,
    extraCosts,
    note: item.ghi_chu || item.note || '',
    totalCost: Number(item.tong_chi_phi || item.total_cost || 0),
    rawDate: item.thoi_gian_bat_dau || item.start_time || item.created_at || '',
  };
}

function formatDisplayDate(val) {
  if (!val) return '—';
  const d = new Date(val);
  if (isNaN(d)) return val;
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const hh = String(d.getHours()).padStart(2, '0');
  const mi = String(d.getMinutes()).padStart(2, '0');
  return `${hh}:${mi} ${dd}/${mm}/${d.getFullYear()}`;
}

function formatCurrency(val) {
  return Number(val || 0).toLocaleString('vi-VN') + ' ₫';
}

const filtered = computed(() => {
  return logs.value.filter(log => {
    const kw = search.value.toLowerCase();
    const matchKw = !kw ||
      (log.technicianName || '').toLowerCase().includes(kw) ||
      (log.content || '').toLowerCase().includes(kw) ||
      String(log.jobCode || '').includes(kw);

    let matchDate = true;
    if (filterFrom.value || filterTo.value) {
      const d = new Date(log.rawDate);
      if (filterFrom.value && d < new Date(filterFrom.value)) matchDate = false;
      if (filterTo.value && d > new Date(filterTo.value + 'T23:59:59')) matchDate = false;
    }
    return matchKw && matchDate;
  });
});

const totalCost = computed(() => logs.value.reduce((s, l) => s + (l.totalCost || 0), 0));
const filteredTotal = computed(() => filtered.value.reduce((s, l) => s + (l.totalCost || 0), 0));

function openDetail(log) {
  detailLog.value = log;
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

/* HEADER */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.header-section h2 { margin: 0 0 4px; font-size: 22px; color: #1e293b; }
.subtitle { margin: 0; color: #64748b; font-size: 14px; }
.header-stats { display: flex; gap: 12px; flex-wrap: wrap; }
.stat-badge {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 16px; border-radius: 12px;
  font-size: 13px; font-weight: 600;
}
.stat-badge.purple { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }
.stat-badge.green  { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }

/* FILTER */
.filter-bar {
  display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  background: #fff; padding: 14px 18px; border-radius: 12px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 20px;
}
.filter-group {
  display: flex; align-items: center; gap: 8px;
}
.filter-group label { color: #94a3b8; }
.filter-group input[type="text"] {
  border: 1px solid #e2e8f0; border-radius: 8px;
  padding: 7px 12px; font-size: 13px;
  width: 260px; outline: none;
}
.filter-group input[type="date"] {
  border: 1px solid #e2e8f0; border-radius: 8px;
  padding: 7px 10px; font-size: 13px; outline: none;
}
.sep { color: #94a3b8; }
.btn-refresh {
  margin-left: auto; padding: 8px 16px;
  background: #3b82f6; color: #fff; border: none;
  border-radius: 8px; font-size: 13px; font-weight: 600;
  cursor: pointer; display: flex; align-items: center; gap: 6px;
}
.btn-refresh:hover { background: #2563eb; }
.btn-refresh:disabled { opacity: 0.6; cursor: default; }

/* STATES */
.loading-state {
  text-align: center; padding: 60px; color: #94a3b8; font-size: 15px;
}
.empty-state {
  text-align: center; padding: 60px 20px;
  background: #fff; border-radius: 16px;
}
.empty-icon { font-size: 48px; margin-bottom: 12px; }
.empty-state h3 { margin: 0 0 6px; color: #1e293b; }
.empty-state p { color: #64748b; margin: 0; }

/* TABLE */
.table-wrapper {
  background: #fff; border-radius: 16px;
  box-shadow: 0 1px 6px rgba(0,0,0,0.07); overflow-x: auto;
}
.data-table {
  width: 100%; border-collapse: collapse;
  min-width: 1000px;
}
.data-table th {
  background: #f1f5f9; padding: 12px 14px;
  text-align: left; font-size: 12px;
  font-weight: 700; color: #475569;
  text-transform: uppercase; letter-spacing: 0.04em;
  white-space: nowrap;
}
.data-table td {
  padding: 12px 14px; border-bottom: 1px solid #f1f5f9;
  font-size: 13px; vertical-align: top;
}
.clickable-row:hover td { background: #f8fafc; cursor: pointer; }

.tech-cell { display: flex; align-items: center; gap: 8px; }
.tech-avatar-sm {
  width: 28px; height: 28px; border-radius: 7px;
  background: #e0f2fe; color: #0284c7;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; flex-shrink: 0;
}
.req-badge {
  background: #ede9fe; color: #7c3aed;
  padding: 2px 8px; border-radius: 6px;
  font-size: 12px; font-weight: 600;
}
.content-cell {
  max-width: 180px; white-space: nowrap;
  overflow: hidden; text-overflow: ellipsis;
}
.time-cell { font-size: 12px; white-space: nowrap; }
.text-gray { color: #94a3b8; font-size: 12px; }
.duration-badge {
  background: #f1f5f9; color: #475569;
  padding: 2px 8px; border-radius: 6px;
  font-size: 12px; font-weight: 600; white-space: nowrap;
}
.tag-list { display: flex; flex-wrap: wrap; gap: 4px; }
.mat-tag {
  background: #e0f2fe; color: #0369a1;
  padding: 2px 6px; border-radius: 5px; font-size: 11px;
}
.extra-tag {
  background: #fef9c3; color: #854d0e;
  padding: 2px 6px; border-radius: 5px; font-size: 11px;
}
.cost-cell { text-align: right; white-space: nowrap; }
.cost-num { color: #16a34a; font-size: 14px; }
.cost-num.total { color: #dc2626; font-size: 15px; }
.note-cell { color: #64748b; font-size: 12px; max-width: 120px; }
.text-right { text-align: right; }
.total-row td { background: #fefce8; padding: 14px; }

/* MODAL */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex; justify-content: center; align-items: flex-start;
  padding-top: 60px; overflow-y: auto; z-index: 9999;
}
.detail-modal {
  background: #fff; width: 680px; max-width: 95vw;
  border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  margin-bottom: 40px;
}
.modal-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
}
.modal-header h3 { margin: 0; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
.btn-close {
  background: none; border: none; cursor: pointer;
  width: 32px; height: 32px; border-radius: 8px;
  color: #64748b; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
}
.btn-close:hover { background: #f1f5f9; }
.modal-body { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }

.detail-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
.detail-item { display: flex; flex-direction: column; gap: 3px; }
.detail-item.full { grid-column: 1 / -1; }
.lbl { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }

.detail-section h4 {
  margin: 0 0 10px; font-size: 13px; color: #1e293b;
  display: flex; align-items: center; gap: 6px;
}
.mini-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mini-table th {
  background: #f8fafc; padding: 8px 10px;
  text-align: left; font-size: 11px; font-weight: 700;
  color: #64748b; text-transform: uppercase;
}
.mini-table td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
.detail-note {
  background: #fefce8; border-radius: 8px;
  padding: 10px 14px; font-size: 13px; color: #854d0e;
  display: flex; gap: 8px; align-items: flex-start;
}
.detail-total {
  text-align: right; padding: 12px 0 0;
  border-top: 2px solid #f1f5f9; font-size: 16px;
  display: flex; align-items: center; justify-content: flex-end; gap: 10px;
}
</style>
