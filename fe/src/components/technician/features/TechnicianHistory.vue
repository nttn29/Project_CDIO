<template>
  <div class="page">

    <!-- ===== HERO ===== -->
    <div class="hero">
      <div class="hero-left">
        <div class="hero-icon"><i class="fas fa-file-lines"></i></div>
        <div>
          <span class="badge-pill">Báo cáo công việc</span>
          <h1>Báo Cáo & Lịch Sử</h1>
          <p>Ghi lại tiến độ, kết quả và các vấn đề phát sinh trong quá trình làm việc</p>
        </div>
      </div>
      <div class="hero-stats">
        <div class="stat-box">
          <span class="stat-icon blue"><i class="fas fa-clipboard-check"></i></span>
          <div>
            <span class="stat-num">{{ reports.length }}</span>
            <span class="stat-lbl">Báo cáo</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon green"><i class="fas fa-circle-check"></i></span>
          <div>
            <span class="stat-num">{{ completedCount }}</span>
            <span class="stat-lbl">Hoàn thành</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon orange"><i class="fas fa-triangle-exclamation"></i></span>
          <div>
            <span class="stat-num">{{ issueCount }}</span>
            <span class="stat-lbl">Sự cố</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon purple"><i class="fas fa-hourglass-half"></i></span>
          <div>
            <span class="stat-num">{{ pendingCount }}</span>
            <span class="stat-lbl">Đang xử lý</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== TABS ===== -->
    <div class="tabs">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        class="tab-btn"
        :class="{ active: activeTab === tab.key }"
        @click="activeTab = tab.key"
      >
        <i :class="tab.icon"></i>
        {{ tab.label }}
      </button>
    </div>

    <!-- ===== TAB: TẠO BÁO CÁO ===== -->
    <template v-if="activeTab === 'create'">
      <div class="two-col">
        <!-- Form -->
        <section class="card">
          <div class="card-head">
            <div class="head-icon indigo"><i class="fas fa-pen-to-square"></i></div>
            <div>
              <h2>Tạo báo cáo mới</h2>
              <p>Điền đầy đủ thông tin về công việc đã thực hiện</p>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group full">
              <label><i class="fas fa-heading"></i> Tiêu đề báo cáo <span class="req">*</span></label>
              <input v-model="form.title" placeholder="VD: Sửa xong hệ thống điện tầng 3..." />
            </div>

            <div class="form-group">
              <label><i class="fas fa-hashtag"></i> Mã công việc</label>
              <input v-model="form.jobCode" placeholder="VD: YC-042" />
            </div>

            <div class="form-group">
              <label><i class="fas fa-door-open"></i> Phòng / Vị trí</label>
              <input v-model="form.location" placeholder="VD: Phòng 301, Tầng 3..." />
            </div>

            <div class="form-group">
              <label><i class="fas fa-tag"></i> Loại công việc</label>
              <select v-model="form.jobType">
                <option value="">-- Chọn loại --</option>
                <option value="electric">Điện</option>
                <option value="plumbing">Nước</option>
                <option value="mechanical">Cơ khí</option>
                <option value="civil">Xây dựng</option>
                <option value="other">Khác</option>
              </select>
            </div>

            <div class="form-group">
              <label><i class="fas fa-circle-dot"></i> Kết quả</label>
              <select v-model="form.result">
                <option value="done">Hoàn thành</option>
                <option value="partial">Hoàn thành một phần</option>
                <option value="issue">Có sự cố</option>
                <option value="pending">Đang xử lý</option>
              </select>
            </div>

            <div class="form-group full">
              <label><i class="fas fa-clipboard-list"></i> Nội dung thực hiện <span class="req">*</span></label>
              <textarea v-model="form.content" rows="4" placeholder="Mô tả chi tiết các bước đã thực hiện, vấn đề gặp phải, và giải pháp xử lý..."></textarea>
            </div>

            <div class="form-group full">
              <label><i class="fas fa-triangle-exclamation"></i> Vấn đề phát sinh (nếu có)</label>
              <textarea v-model="form.issues" rows="2" placeholder="Ghi chú các vấn đề chưa giải quyết hoặc cần người khác hỗ trợ..."></textarea>
            </div>

            <div class="form-group">
              <label><i class="fas fa-clock"></i> Thời gian bắt đầu</label>
              <input type="datetime-local" v-model="form.startTime" />
            </div>

            <div class="form-group">
              <label><i class="fas fa-clock-rotate-left"></i> Thời gian kết thúc</label>
              <input type="datetime-local" v-model="form.endTime" />
            </div>
          </div>

          <!-- Duration display -->
          <div v-if="durationDisplay" class="duration-info">
            <i class="fas fa-stopwatch"></i>
            Thời gian làm việc: <strong>{{ durationDisplay }}</strong>
          </div>

          <div v-if="formError" class="alert-error">
            <i class="fas fa-circle-exclamation"></i> {{ formError }}
          </div>

          <div class="form-footer">
            <button class="btn-reset" @click="resetForm">
              <i class="fas fa-rotate-left"></i> Làm mới
            </button>
            <button class="btn-submit" @click="submitReport">
              <i class="fas fa-paper-plane"></i> Gửi báo cáo
            </button>
          </div>
        </section>

        <!-- Báo cáo gần đây -->
        <section class="card">
          <div class="card-head">
            <div class="head-icon green"><i class="fas fa-list-check"></i></div>
            <div>
              <h2>Báo cáo gần đây</h2>
              <p>{{ reports.length }} báo cáo đã gửi</p>
            </div>
          </div>

          <div class="report-list">
            <div v-if="reports.length === 0" class="empty-block">
              <i class="fas fa-inbox"></i>
              <p>Chưa có báo cáo nào</p>
            </div>
            <div v-for="rpt in reports.slice(0, 5)" :key="rpt.id" class="report-item">
              <div class="ri-top">
                <div class="ri-title">{{ rpt.title }}</div>
                <span class="result-pill" :class="'rp-' + rpt.result">
                  <i :class="resultIcon(rpt.result)"></i>
                  {{ resultLabel(rpt.result) }}
                </span>
              </div>
              <div class="ri-meta">
                <span v-if="rpt.jobCode"><i class="fas fa-hashtag"></i> {{ rpt.jobCode }}</span>
                <span v-if="rpt.location"><i class="fas fa-location-dot"></i> {{ rpt.location }}</span>
                <span><i class="fas fa-clock"></i> {{ rpt.submittedAt }}</span>
              </div>
              <p v-if="rpt.content" class="ri-content">{{ rpt.content.slice(0, 100) }}{{ rpt.content.length > 100 ? '...' : '' }}</p>
            </div>
          </div>
        </section>
      </div>
    </template>

    <!-- ===== TAB: LỊCH SỬ ===== -->
    <template v-if="activeTab === 'history'">
      <div class="toolbar">
        <div class="toolbar-left">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input v-model="histSearch" placeholder="Tìm kiếm báo cáo..." />
          </div>
          <select v-model="filterResult" class="filter-sel">
            <option value="">Tất cả kết quả</option>
            <option value="done">Hoàn thành</option>
            <option value="partial">Một phần</option>
            <option value="issue">Sự cố</option>
            <option value="pending">Đang xử lý</option>
          </select>
          <select v-model="filterType" class="filter-sel">
            <option value="">Tất cả loại</option>
            <option value="electric">Điện</option>
            <option value="plumbing">Nước</option>
            <option value="mechanical">Cơ khí</option>
            <option value="civil">Xây dựng</option>
            <option value="other">Khác</option>
          </select>
        </div>
        <div class="total-badge">
          <i class="fas fa-filter"></i> {{ filteredReports.length }} kết quả
        </div>
      </div>

      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tiêu đề</th>
              <th>Mã CV</th>
              <th>Vị trí</th>
              <th>Loại</th>
              <th>Kết quả</th>
              <th>Thời gian làm</th>
              <th>Ngày gửi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredReports.length === 0">
              <td colspan="8" class="empty-row">
                <i class="fas fa-search-minus"></i>
                <p>Không tìm thấy báo cáo nào</p>
              </td>
            </tr>
            <tr v-for="(rpt, idx) in filteredReports" :key="rpt.id" @click="openDetail(rpt)" class="clickable-row">
              <td class="td-idx">{{ idx + 1 }}</td>
              <td class="td-title">{{ rpt.title }}</td>
              <td><span class="code-tag" v-if="rpt.jobCode">{{ rpt.jobCode }}</span><span v-else class="na-text">—</span></td>
              <td class="td-loc">{{ rpt.location || '—' }}</td>
              <td><span class="type-tag" :class="'tt-' + rpt.jobType" v-if="rpt.jobType">{{ typeLabel(rpt.jobType) }}</span><span v-else class="na-text">—</span></td>
              <td>
                <span class="result-pill" :class="'rp-' + rpt.result">
                  <i :class="resultIcon(rpt.result)"></i>
                  {{ resultLabel(rpt.result) }}
                </span>
              </td>
              <td class="td-dur">{{ rpt.duration || '—' }}</td>
              <td class="td-date">{{ rpt.submittedAt }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <!-- ===== MODAL CHI TIẾT ===== -->
    <div v-if="detailReport" class="modal-overlay" @click.self="detailReport = null">
      <div class="modal-box">
        <div class="modal-header">
          <h2><i class="fas fa-file-lines"></i> Chi tiết báo cáo</h2>
          <button class="modal-close" @click="detailReport = null"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
          <div class="detail-title">{{ detailReport.title }}</div>
          <div class="detail-meta">
            <span v-if="detailReport.jobCode"><i class="fas fa-hashtag"></i> {{ detailReport.jobCode }}</span>
            <span v-if="detailReport.location"><i class="fas fa-location-dot"></i> {{ detailReport.location }}</span>
            <span class="result-pill" :class="'rp-' + detailReport.result">
              <i :class="resultIcon(detailReport.result)"></i>
              {{ resultLabel(detailReport.result) }}
            </span>
          </div>
          <div class="detail-section">
            <div class="ds-label"><i class="fas fa-clipboard-list"></i> Nội dung thực hiện</div>
            <div class="ds-content">{{ detailReport.content }}</div>
          </div>
          <div class="detail-section" v-if="detailReport.issues">
            <div class="ds-label issue-label"><i class="fas fa-triangle-exclamation"></i> Vấn đề phát sinh</div>
            <div class="ds-content issue-content">{{ detailReport.issues }}</div>
          </div>
          <div class="detail-times">
            <div><i class="fas fa-clock"></i> Bắt đầu: <strong>{{ detailReport.startDisplay }}</strong></div>
            <div><i class="fas fa-clock-rotate-left"></i> Kết thúc: <strong>{{ detailReport.endDisplay }}</strong></div>
            <div v-if="detailReport.duration"><i class="fas fa-stopwatch"></i> Thời gian: <strong>{{ detailReport.duration }}</strong></div>
          </div>
        </div>
      </div>
    </div>

    <!-- TOAST -->
    <transition name="toast-fade">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
        <i :class="toast.icon"></i> {{ toast.msg }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// ===== TABS =====
const activeTab = ref('create');
const tabs = [
  { key: 'create',  label: 'Tạo báo cáo',  icon: 'fas fa-pen-to-square' },
  { key: 'history', label: 'Lịch sử',       icon: 'fas fa-clock-rotate-left' },
];

// ===== REPORTS DATA =====
const reports = ref([
  {
    id: 1, title: 'Sửa xong hệ thống điện hành lang tầng 3',
    jobCode: 'YC-038', location: 'Tầng 3 - Hành lang A',
    jobType: 'electric', result: 'done',
    content: 'Đã kiểm tra và thay thế 3 cầu chì bị hở, siết lại toàn bộ đầu nối dây trong hộp điện. Đèn hành lang đã hoạt động bình thường.',
    issues: '',
    startTime: '2026-03-02T08:00', endTime: '2026-03-02T10:30',
    startDisplay: '08:00 02/03/2026', endDisplay: '10:30 02/03/2026', duration: '2 giờ 30 phút',
    submittedAt: '02/03/2026 11:00',
  },
  {
    id: 2, title: 'Xử lý ống nước rò rỉ phòng 212',
    jobCode: 'YC-035', location: 'Phòng 212 – Tầng 2',
    jobType: 'plumbing', result: 'done',
    content: 'Phát hiện ống nước nóng dưới bồn rửa bị nứt do lão hóa. Đã cắt đoạn hỏng và nối lại bằng ống PVC mới.',
    issues: '',
    startTime: '2026-03-01T14:00', endTime: '2026-03-01T16:00',
    startDisplay: '14:00 01/03/2026', endDisplay: '16:00 01/03/2026', duration: '2 giờ',
    submittedAt: '01/03/2026 16:30',
  },
  {
    id: 3, title: 'Kiểm tra thang máy – phát hiện sự cố',
    jobCode: 'YC-030', location: 'Thang máy – Lõi B',
    jobType: 'mechanical', result: 'issue',
    content: 'Kiểm tra định kỳ thang máy số 2. Phát hiện tiếng ồn bất thường từ rãnh trượt tầng 5.',
    issues: 'Cần kỹ thuật chuyên sâu về thang máy để kiểm tra rãnh trượt và hệ thống phanh. Đã báo cáo lên quản lý.',
    startTime: '2026-02-28T09:00', endTime: '2026-02-28T11:00',
    startDisplay: '09:00 28/02/2026', endDisplay: '11:00 28/02/2026', duration: '2 giờ',
    submittedAt: '28/02/2026 11:30',
  },
  {
    id: 4, title: 'Sơn lại tường ẩm mốc phòng 204',
    jobCode: 'YC-025', location: 'Phòng 204 – Tầng 2',
    jobType: 'civil', result: 'partial',
    content: 'Đã cạo lớp sơn cũ bị ẩm, xử lý chống thấm và sơn lót. Chưa sơn phủ hoàn thành do hết vật tư.',
    issues: 'Cần thêm 3 lít sơn phủ màu trắng ngà. Đã tạo yêu cầu cấp vật tư.',
    startTime: '2026-02-25T08:30', endTime: '2026-02-25T12:30',
    startDisplay: '08:30 25/02/2026', endDisplay: '12:30 25/02/2026', duration: '4 giờ',
    submittedAt: '25/02/2026 13:00',
  },
  {
    id: 5, title: 'Bảo trì điều hòa phòng 501',
    jobCode: 'YC-022', location: 'Phòng 501 – Tầng 5',
    jobType: 'electric', result: 'pending',
    content: 'Đang vệ sinh dàn lạnh và kiểm tra môi chất. Chờ bộ đo áp suất từ kho.',
    issues: 'Thiếu bộ đo áp suất ga. Đang đợi cấp phát.',
    startTime: '2026-02-24T13:00', endTime: '',
    startDisplay: '13:00 24/02/2026', endDisplay: '—', duration: '',
    submittedAt: '24/02/2026 14:00',
  },
]);

// ===== STATS =====
const completedCount = computed(() => reports.value.filter(r => r.result === 'done').length);
const issueCount     = computed(() => reports.value.filter(r => r.result === 'issue').length);
const pendingCount   = computed(() => reports.value.filter(r => r.result === 'pending').length);

// ===== FORM =====
const formError = ref('');
const defaultForm = () => ({
  title: '', jobCode: '', location: '', jobType: '', result: 'done',
  content: '', issues: '', startTime: '', endTime: '',
});
const form = ref(defaultForm());

const durationDisplay = computed(() => {
  if (!form.value.startTime || !form.value.endTime) return '';
  const diff = new Date(form.value.endTime) - new Date(form.value.startTime);
  if (diff <= 0) return 'Thời gian không hợp lệ';
  const h = Math.floor(diff / 3600000);
  const m = Math.floor((diff % 3600000) / 60000);
  return h > 0 ? `${h} giờ ${m} phút` : `${m} phút`;
});

function resetForm() {
  form.value = defaultForm();
  formError.value = '';
}

function formatDT(val) {
  if (!val) return '—';
  const d = new Date(val);
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const hh = String(d.getHours()).padStart(2, '0');
  const mi = String(d.getMinutes()).padStart(2, '0');
  return `${hh}:${mi} ${dd}/${mm}/${d.getFullYear()}`;
}

function submitReport() {
  formError.value = '';
  if (!form.value.title.trim()) { formError.value = 'Vui lòng nhập tiêu đề báo cáo!'; return; }
  if (!form.value.content.trim()) { formError.value = 'Vui lòng nhập nội dung thực hiện!'; return; }

  const now = new Date();
  const pad = n => n.toString().padStart(2, '0');
  const nowStr = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}`;

  reports.value.unshift({
    id: Date.now(),
    title: form.value.title,
    jobCode: form.value.jobCode,
    location: form.value.location,
    jobType: form.value.jobType,
    result: form.value.result,
    content: form.value.content,
    issues: form.value.issues,
    startTime: form.value.startTime,
    endTime: form.value.endTime,
    startDisplay: formatDT(form.value.startTime),
    endDisplay: formatDT(form.value.endTime),
    duration: durationDisplay.value,
    submittedAt: nowStr,
  });

  showToast('Đã gửi báo cáo thành công!', 'success', 'fas fa-check-circle');
  resetForm();
}

// ===== HISTORY FILTER =====
const histSearch   = ref('');
const filterResult = ref('');
const filterType   = ref('');

const filteredReports = computed(() =>
  reports.value.filter(r => {
    const q = histSearch.value.toLowerCase();
    const matchSearch = !q || r.title.toLowerCase().includes(q) || (r.jobCode || '').toLowerCase().includes(q) || (r.location || '').toLowerCase().includes(q);
    const matchResult = !filterResult.value || r.result === filterResult.value;
    const matchType   = !filterType.value   || r.jobType === filterType.value;
    return matchSearch && matchResult && matchType;
  })
);

// ===== HELPERS =====
function resultLabel(r) {
  return { done: 'Hoàn thành', partial: 'Một phần', issue: 'Sự cố', pending: 'Đang xử lý' }[r] || r;
}
function resultIcon(r) {
  return { done: 'fas fa-circle-check', partial: 'fas fa-circle-half-stroke', issue: 'fas fa-triangle-exclamation', pending: 'fas fa-hourglass-half' }[r] || 'fas fa-circle';
}
function typeLabel(t) {
  return { electric: 'Điện', plumbing: 'Nước', mechanical: 'Cơ khí', civil: 'Xây dựng', other: 'Khác' }[t] || t;
}

// ===== DETAIL MODAL =====
const detailReport = ref(null);
function openDetail(rpt) { detailReport.value = rpt; }

// ===== TOAST =====
const toast = ref({ show: false, type: 'success', msg: '', icon: '' });
let toastTimer = null;
function showToast(msg, type = 'success', icon = 'fas fa-check-circle') {
  if (toastTimer) clearTimeout(toastTimer);
  toast.value = { show: true, type, msg, icon };
  toastTimer = setTimeout(() => { toast.value.show = false; }, 3000);
}
</script>

<style scoped>
/* ===== PAGE ===== */
.page {
  display: flex;
  flex-direction: column;
  gap: 22px;
  padding-bottom: 60px;
  color: #0f172a;
  font-family: 'Inter', 'Segoe UI', sans-serif;
}

/* ===== HERO ===== */
.hero {
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  border-radius: 20px;
  padding: 28px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
  box-shadow: 0 8px 30px rgba(79, 70, 229, .3);
}
.hero-left { display: flex; align-items: center; gap: 18px; }
.hero-icon {
  width: 60px; height: 60px;
  background: rgba(255,255,255,.12);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; color: #c4b5fd;
}
.hero-left h1 { margin: 4px 0 4px; font-size: 26px; font-weight: 800; color: #fff; }
.hero-left p  { margin: 0; font-size: 13px; color: rgba(255,255,255,.75); }

.badge-pill {
  display: inline-block;
  background: rgba(196,181,253,.2);
  border: 1px solid rgba(196,181,253,.4);
  color: #c4b5fd;
  border-radius: 20px;
  padding: 2px 12px;
  font-size: 11px; font-weight: 600;
  letter-spacing: .5px; text-transform: uppercase;
  margin-bottom: 4px;
}

.hero-stats { display: flex; gap: 12px; flex-wrap: wrap; }
.stat-box {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 14px; padding: 14px 18px;
  display: flex; align-items: center; gap: 12px; min-width: 110px;
}
.stat-icon {
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.stat-icon.blue   { background: rgba(59,130,246,.25);  color: #93c5fd; }
.stat-icon.green  { background: rgba(34,197,94,.25);   color: #86efac; }
.stat-icon.orange { background: rgba(251,146,60,.25);  color: #fdba74; }
.stat-icon.purple { background: rgba(196,181,253,.2);  color: #c4b5fd; }
.stat-box > div { display: flex; flex-direction: column; }
.stat-num { font-size: 22px; font-weight: 800; color: #fff; line-height: 1; }
.stat-lbl { font-size: 11px; color: rgba(255,255,255,.7); margin-top: 2px; }

/* ===== TABS ===== */
.tabs {
  display: flex; gap: 6px;
  border-bottom: 2px solid #e2e8f0;
}
.tab-btn {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 20px; border: none; background: none;
  font-size: 14px; font-weight: 500; color: #64748b;
  border-radius: 10px 10px 0 0;
  border-bottom: 2px solid transparent; margin-bottom: -2px;
  cursor: pointer; transition: all .2s;
}
.tab-btn:hover { background: #f8fafc; color: #1e293b; }
.tab-btn.active { color: #7c3aed; font-weight: 700; border-bottom-color: #7c3aed; background: #f5f3ff; }

/* ===== TWO-COL ===== */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
@media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

/* ===== CARD ===== */
.card {
  background: #fff; border-radius: 16px; padding: 24px;
  box-shadow: 0 4px 20px rgba(15,23,42,.06);
  display: flex; flex-direction: column; gap: 18px;
  border: 1px solid #f1f5f9;
}
.card-head {
  display: flex; align-items: center; gap: 14px;
  border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;
}
.head-icon {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
}
.head-icon.indigo { background: #ede9fe; color: #7c3aed; }
.head-icon.green  { background: #dcfce7; color: #16a34a; }
.card-head h2 { margin: 0; font-size: 17px; font-weight: 700; color: #1e293b; }
.card-head p  { margin: 3px 0 0; font-size: 12px; color: #64748b; }

/* ===== FORM ===== */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label {
  font-size: 12px; font-weight: 600; color: #475569;
  display: flex; align-items: center; gap: 6px;
}
.form-group label i { color: #94a3b8; }
.req { color: #ef4444; }
.form-group input,
.form-group select,
.form-group textarea {
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  padding: 10px 13px; font-size: 13px; color: #1e293b;
  outline: none; transition: border-color .2s; background: #fff; resize: none;
  font-family: inherit;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.08); }

.duration-info {
  display: flex; align-items: center; gap: 8px;
  background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 10px;
  padding: 10px 14px; font-size: 13px; color: #6d28d9;
}
.duration-info i { color: #7c3aed; }
.duration-info strong { color: #4c1d95; }

.alert-error {
  background: #fff1f2; border: 1px solid #fecdd3; color: #be123c;
  border-radius: 10px; padding: 10px 14px; font-size: 13px;
  display: flex; align-items: center; gap: 8px;
}

.form-footer {
  display: flex; justify-content: flex-end; gap: 10px;
  border-top: 1px solid #f1f5f9; padding-top: 16px;
}
.btn-reset {
  background: #f1f5f9; color: #475569; border: none;
  border-radius: 10px; padding: 10px 18px;
  font-size: 13px; font-weight: 600; cursor: pointer; transition: .2s;
  display: flex; align-items: center; gap: 7px;
}
.btn-reset:hover { background: #e2e8f0; }
.btn-submit {
  background: linear-gradient(135deg, #7c3aed, #4f46e5);
  color: #fff; border: none; border-radius: 10px;
  padding: 10px 22px; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: .2s;
  display: flex; align-items: center; gap: 7px;
  box-shadow: 0 4px 14px rgba(124,58,237,.3);
}
.btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(124,58,237,.4); }

/* ===== REPORT LIST (CARD SIDEBAR) ===== */
.report-list { display: flex; flex-direction: column; gap: 10px; overflow-y: auto; max-height: 480px; }
.empty-block { text-align: center; padding: 40px; color: #94a3b8; }
.empty-block i { font-size: 32px; display: block; margin-bottom: 10px; opacity: .4; }
.empty-block p { margin: 0; font-size: 13px; }

.report-item {
  background: #f8fafc; border-radius: 12px; padding: 14px 16px;
  border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 6px; transition: .2s;
}
.report-item:hover { border-color: #c4b5fd; background: #faf5ff; }
.ri-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
.ri-title { font-weight: 700; font-size: 13px; color: #1e293b; flex: 1; }
.ri-meta { display: flex; flex-wrap: wrap; gap: 8px; font-size: 11px; color: #64748b; align-items: center; }
.ri-meta i { color: #94a3b8; margin-right: 2px; }
.ri-content { font-size: 12px; color: #64748b; margin: 0; line-height: 1.5; font-style: italic; }

/* ===== TOOLBAR ===== */
.toolbar {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; flex-wrap: wrap;
}
.toolbar-left { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.search-box {
  display: flex; align-items: center; gap: 8px;
  background: #fff; border: 1.5px solid #e2e8f0;
  border-radius: 10px; padding: 8px 14px; min-width: 220px;
}
.search-box i { color: #94a3b8; font-size: 13px; }
.search-box input { border: none; outline: none; background: transparent; font-size: 13px; color: #1e293b; width: 100%; }
.filter-sel {
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  padding: 8px 12px; font-size: 13px; color: #1e293b;
  background: #fff; outline: none; cursor: pointer;
}
.filter-sel:focus { border-color: #7c3aed; }
.total-badge {
  background: #f5f3ff; border: 1px solid #ddd6fe; color: #6d28d9;
  border-radius: 10px; padding: 8px 14px; font-size: 13px; font-weight: 600;
  display: flex; align-items: center; gap: 6px;
}

/* ===== TABLE ===== */
.table-card {
  background: #fff; border-radius: 16px;
  box-shadow: 0 4px 20px rgba(15,23,42,.06); overflow: hidden;
  border: 1px solid #f1f5f9;
}
table { width: 100%; border-collapse: collapse; }
thead tr { background: #f8fafc; }
th {
  padding: 13px 16px; text-align: left;
  font-size: 12px; font-weight: 700; color: #64748b;
  text-transform: uppercase; letter-spacing: .5px;
  border-bottom: 1px solid #e2e8f0;
}
td { padding: 13px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
tr:last-child td { border-bottom: none; }
tbody tr { transition: background .15s; }
.clickable-row { cursor: pointer; }
.clickable-row:hover { background: #faf5ff; }

.td-idx   { color: #94a3b8; font-size: 12px; width: 36px; }
.td-title { font-weight: 600; color: #1e293b; max-width: 220px; }
.td-loc   { color: #64748b; font-size: 12px; }
.td-dur   { color: #64748b; font-size: 12px; white-space: nowrap; }
.td-date  { color: #94a3b8; font-size: 12px; white-space: nowrap; }
.na-text  { color: #cbd5e1; }

.code-tag {
  background: #f1f5f9; color: #334155; border-radius: 6px;
  padding: 3px 8px; font-size: 11px; font-weight: 700; font-family: 'Courier New', monospace;
}

/* Result pills */
.result-pill {
  display: inline-flex; align-items: center; gap: 5px;
  border-radius: 20px; padding: 4px 10px; font-size: 11px; font-weight: 600; white-space: nowrap;
}
.rp-done    { background: #dcfce7; color: #15803d; }
.rp-partial { background: #fef9c3; color: #854d0e; }
.rp-issue   { background: #fee2e2; color: #b91c1c; }
.rp-pending { background: #e0f2fe; color: #0369a1; }

/* Type tags */
.type-tag {
  display: inline-block; border-radius: 20px;
  padding: 3px 10px; font-size: 11px; font-weight: 600;
}
.tt-electric   { background: #fef9c3; color: #854d0e; }
.tt-plumbing   { background: #dbeafe; color: #1d4ed8; }
.tt-mechanical { background: #f3e8ff; color: #7e22ce; }
.tt-civil      { background: #fce7f3; color: #9d174d; }
.tt-other      { background: #f1f5f9; color: #475569; }

.empty-row {
  text-align: center; padding: 48px 20px; color: #94a3b8; font-size: 13px;
}
.empty-row i { font-size: 36px; margin-bottom: 10px; display: block; opacity: .4; }
.empty-row p { margin: 0; }

/* ===== MODAL ===== */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(15,23,42,.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 9999; padding: 20px;
  backdrop-filter: blur(4px);
}
.modal-box {
  background: #fff; border-radius: 20px; width: 100%; max-width: 540px;
  box-shadow: 0 25px 60px rgba(15,23,42,.25); overflow: hidden;
}
.modal-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 24px;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
}
.modal-header h2 { margin: 0; font-size: 17px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; }
.modal-header h2 i { color: #c4b5fd; }
.modal-close {
  background: rgba(255,255,255,.15); border: none; color: #fff;
  width: 32px; height: 32px; border-radius: 8px;
  cursor: pointer; font-size: 14px; transition: .2s;
}
.modal-close:hover { background: rgba(255,255,255,.25); }
.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }

.detail-title { font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.4; }
.detail-meta { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; font-size: 12px; color: #64748b; }
.detail-meta i { color: #94a3b8; margin-right: 2px; }

.detail-section { display: flex; flex-direction: column; gap: 6px; }
.ds-label {
  font-size: 12px; font-weight: 700; color: #475569;
  display: flex; align-items: center; gap: 6px;
  text-transform: uppercase; letter-spacing: .4px;
}
.ds-label i { color: #7c3aed; }
.issue-label i { color: #dc2626; }
.ds-content {
  font-size: 13px; color: #334155; line-height: 1.65;
  background: #f8fafc; border-radius: 10px; padding: 12px 14px; border: 1px solid #f1f5f9;
}
.issue-content { background: #fff7f7; border-color: #fecdd3; color: #7f1d1d; }

.detail-times {
  display: flex; flex-wrap: wrap; gap: 14px;
  font-size: 12px; color: #64748b; padding-top: 8px;
  border-top: 1px solid #f1f5f9;
}
.detail-times i { color: #94a3b8; margin-right: 4px; }
.detail-times strong { color: #1e293b; }

/* ===== TOAST ===== */
.toast {
  position: fixed; bottom: 28px; right: 28px; z-index: 99999;
  display: flex; align-items: center; gap: 10px;
  padding: 14px 20px; border-radius: 12px;
  font-size: 13px; font-weight: 600;
  box-shadow: 0 8px 24px rgba(0,0,0,.15);
  min-width: 240px;
}
.toast-success { background: #0f172a; color: #4ade80; }
.toast-error   { background: #0f172a; color: #f87171; }

.toast-fade-enter-active, .toast-fade-leave-active { transition: all .3s ease; }
.toast-fade-enter-from { opacity: 0; transform: translateY(16px); }
.toast-fade-leave-to   { opacity: 0; transform: translateY(16px); }
</style>
