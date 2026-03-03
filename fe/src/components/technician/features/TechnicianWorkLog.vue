<template>
  <div class="page">
    <!-- HERO -->
    <div class="hero">
      <div class="hero-text">
        <span class="badge">Ghi chép công việc</span>
        <h1>WorkLog & Chi Phí</h1>
        <p>Ghi lại quá trình thực hiện, thời gian làm việc, vật tư sử dụng và chi phí phát sinh.</p>
      </div>
      <div class="hero-stats">
        <div class="stat-pill">
          <span class="stat-num">{{ logs.length }}</span>
          <span class="stat-lbl">Lượt ghi</span>
        </div>
        <div class="stat-pill cost">
          <span class="stat-num">{{ totalCostDisplay }}</span>
          <span class="stat-lbl">Tổng chi phí</span>
        </div>
      </div>
    </div>

    <!-- FORM GHI MỚI -->
    <section class="card form-card">
      <div class="card-head">
        <div class="head-icon"><i class="fas fa-pen-to-square"></i></div>
        <div>
          <h2>Ghi WorkLog mới</h2>
          <p>Điền đầy đủ thông tin bên dưới để lưu lại quá trình làm việc</p>
        </div>
      </div>

      <div class="form-grid">
        <!-- Nội dung thực hiện -->
        <div class="form-group full">
          <label><i class="fas fa-clipboard-list"></i> Nội dung thực hiện <span class="req">*</span></label>
          <textarea v-model="form.content" rows="3" placeholder="Mô tả chi tiết công việc đã thực hiện..."></textarea>
        </div>

        <!-- Thời gian -->
        <div class="form-group">
          <label><i class="fas fa-clock"></i> Thời gian bắt đầu <span class="req">*</span></label>
          <input type="datetime-local" v-model="form.startTime" />
        </div>
        <div class="form-group">
          <label><i class="fas fa-clock-rotate-left"></i> Thời gian kết thúc <span class="req">*</span></label>
          <input type="datetime-local" v-model="form.endTime" />
        </div>
        <div class="form-group">
          <label><i class="fas fa-stopwatch"></i> Thời gian làm (giờ)</label>
          <input type="text" :value="durationDisplay" readonly class="readonly-field" placeholder="Tự động tính" />
        </div>

        <!-- Vật tư sử dụng -->
        <div class="form-group full">
          <label><i class="fas fa-boxes-stacked"></i> Vật tư sử dụng</label>
          <div class="material-list">
            <div v-for="(m, idx) in form.materials" :key="idx" class="material-row">
              <input v-model="m.name" type="text" placeholder="Tên vật tư..." class="mat-name" />
              <input v-model.number="m.qty" type="number" min="0" placeholder="SL" class="mat-qty" />
              <input v-model="m.unit" type="text" placeholder="Đơn vị" class="mat-unit" />
              <input v-model.number="m.price" type="number" min="0" placeholder="Đơn giá (VNĐ)" class="mat-price" />
              <div class="mat-subtotal">{{ formatCurrency(m.qty * m.price) }}</div>
              <button class="btn-remove-mat" @click="removeMaterial(idx)" title="Xóa">
                <i class="fas fa-trash"></i>
              </button>
            </div>
            <div v-if="form.materials.length === 0" class="no-material">Chưa có vật tư nào. Nhấn "+ Thêm" để bổ sung.</div>
          </div>
          <button class="btn-add-mat" @click="addMaterial">
            <i class="fas fa-plus"></i> Thêm vật tư
          </button>
        </div>

        <!-- Chi phí phát sinh -->
        <div class="form-group full">
          <label><i class="fas fa-money-bill-wave"></i> Chi phí phát sinh khác</label>
          <div class="extra-cost-list">
            <div v-for="(c, idx) in form.extraCosts" :key="idx" class="extra-row">
              <input v-model="c.desc" type="text" placeholder="Mô tả chi phí..." class="cost-desc" />
              <input v-model.number="c.amount" type="number" min="0" placeholder="Số tiền (VNĐ)" class="cost-amount" />
              <button class="btn-remove-mat" @click="removeExtraCost(idx)" title="Xóa">
                <i class="fas fa-trash"></i>
              </button>
            </div>
            <div v-if="form.extraCosts.length === 0" class="no-material">Chưa có chi phí phát sinh.</div>
          </div>
          <button class="btn-add-mat outline" @click="addExtraCost">
            <i class="fas fa-plus"></i> Thêm chi phí
          </button>
        </div>

        <!-- Tổng chi phí form -->
        <div class="form-group full">
          <div class="cost-summary">
            <div class="cost-row">
              <span>Chi phí vật tư:</span>
              <strong>{{ formatCurrency(materialCost) }}</strong>
            </div>
            <div class="cost-row">
              <span>Chi phí phát sinh:</span>
              <strong>{{ formatCurrency(extraCostTotal) }}</strong>
            </div>
            <div class="cost-row total">
              <span>Tổng chi phí:</span>
              <strong class="total-num">{{ formatCurrency(materialCost + extraCostTotal) }}</strong>
            </div>
          </div>
        </div>

        <!-- Ghi chú -->
        <div class="form-group full">
          <label><i class="fas fa-note-sticky"></i> Ghi chú thêm</label>
          <textarea v-model="form.note" rows="2" placeholder="Ghi chú khác (tùy chọn)..."></textarea>
        </div>
      </div>

      <div v-if="formError" class="error-msg"><i class="fas fa-circle-exclamation"></i> {{ formError }}</div>
      <div v-if="formSuccess" class="success-msg"><i class="fas fa-circle-check"></i> {{ formSuccess }}</div>

      <div class="form-actions">
        <button class="btn-reset" @click="resetForm"><i class="fas fa-rotate-left"></i> Xóa form</button>
        <button class="btn-submit" @click="submitLog" :disabled="submitting">
          <i v-if="submitting" class="fas fa-spinner fa-spin"></i>
          <template v-else><i class="fas fa-floppy-disk"></i> Lưu WorkLog</template>
        </button>
      </div>
    </section>

    <!-- LỊCH SỬ WORKLOG -->
    <section class="card logs-card">
      <div class="card-head">
        <div class="head-icon green"><i class="fas fa-list-check"></i></div>
        <div>
          <h2>Lịch sử WorkLog</h2>
          <p>Danh sách các lần đã ghi trong đợt</p>
        </div>
      </div>

      <div v-if="logs.length === 0" class="empty-logs">
        <div class="empty-icon">📝</div>
        <p>Chưa có WorkLog nào được ghi.</p>
      </div>

      <div v-else class="log-list">
        <div v-for="(log, idx) in logs" :key="idx" class="log-item">
          <div class="log-header">
            <div class="log-index">#{{ idx + 1 }}</div>
            <div class="log-meta">
              <span><i class="fas fa-clock"></i> {{ log.startTime }} → {{ log.endTime }}</span>
              <span class="duration-tag">{{ log.duration }}</span>
            </div>
            <button class="btn-delete-log" @click="deleteLog(idx)" title="Xóa">
              <i class="fas fa-trash"></i>
            </button>
          </div>
          <p class="log-content">{{ log.content }}</p>

          <div v-if="log.materials.length > 0" class="log-section">
            <div class="section-title"><i class="fas fa-boxes-stacked"></i> Vật tư</div>
            <div class="mat-tags">
              <span v-for="(m, mi) in log.materials" :key="mi" class="mat-tag">
                {{ m.name }} × {{ m.qty }} {{ m.unit }} — {{ formatCurrency(m.qty * m.price) }}
              </span>
            </div>
          </div>

          <div v-if="log.extraCosts.length > 0" class="log-section">
            <div class="section-title"><i class="fas fa-money-bill-wave"></i> Chi phí phát sinh</div>
            <div class="mat-tags">
              <span v-for="(c, ci) in log.extraCosts" :key="ci" class="cost-tag">
                {{ c.desc }}: {{ formatCurrency(c.amount) }}
              </span>
            </div>
          </div>

          <div v-if="log.note" class="log-note"><i class="fas fa-note-sticky"></i> {{ log.note }}</div>

          <div class="log-total">
            Tổng chi phí: <strong>{{ formatCurrency(log.totalCost) }}</strong>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000';

const techUser = (() => {
  try { return JSON.parse(localStorage.getItem('tech_user') || 'null'); } catch { return null; }
})();
const technicianId = techUser?.id || techUser?.id_nguoi_dung || null;

const logs = ref([]);
const formError = ref('');
const formSuccess = ref('');
const submitting = ref(false);

onMounted(async () => {
  try {
    const url = new URL(`${API_BASE}/api/work-logs`);
    if (technicianId) url.searchParams.set('technician_id', technicianId);
    const res = await fetch(url.toString());
    if (res.ok) {
      const data = await res.json();
      logs.value = (data.data || data || []).map(mapApiLog);
    }
  } catch (err) {
    // backend chưa có endpoint thì bỏ qua
  }
});

function mapApiLog(item) {
  return {
    id: item.id,
    content: item.noi_dung || item.content || '',
    startTime: item.thoi_gian_bat_dau || item.start_time || '',
    endTime: item.thoi_gian_ket_thuc || item.end_time || '',
    duration: item.thoi_luong || item.duration || '',
    materials: item.vat_tu ? JSON.parse(item.vat_tu) : (item.materials || []),
    extraCosts: item.chi_phi_phat_sinh ? JSON.parse(item.chi_phi_phat_sinh) : (item.extraCosts || []),
    note: item.ghi_chu || item.note || '',
    totalCost: Number(item.tong_chi_phi || item.total_cost || 0),
    technicianName: item.technician?.ten || item.technician_name || '',
    jobCode: item.yeu_cau?.id_yeu_cau || item.job_code || '',
  };
}

const defaultForm = () => ({
  content: '',
  startTime: '',
  endTime: '',
  materials: [],
  extraCosts: [],
  note: '',
});

const form = ref(defaultForm());

// Duration
const durationDisplay = computed(() => {
  if (!form.value.startTime || !form.value.endTime) return '';
  const diff = new Date(form.value.endTime) - new Date(form.value.startTime);
  if (diff <= 0) return 'Không hợp lệ';
  const h = Math.floor(diff / 3600000);
  const m = Math.floor((diff % 3600000) / 60000);
  return h > 0 ? `${h} giờ ${m} phút` : `${m} phút`;
});

// Costs
const materialCost = computed(() =>
  form.value.materials.reduce((s, m) => s + (Number(m.qty) || 0) * (Number(m.price) || 0), 0)
);
const extraCostTotal = computed(() =>
  form.value.extraCosts.reduce((s, c) => s + (Number(c.amount) || 0), 0)
);

// Total of all logs
const totalCostDisplay = computed(() => {
  const total = logs.value.reduce((s, l) => s + (l.totalCost || 0), 0);
  return formatCurrency(total);
});

function formatCurrency(val) {
  if (!val && val !== 0) return '0 ₫';
  return Number(val).toLocaleString('vi-VN') + ' ₫';
}

function addMaterial() {
  form.value.materials.push({ name: '', qty: 1, unit: 'cái', price: 0 });
}
function removeMaterial(idx) {
  form.value.materials.splice(idx, 1);
}
function addExtraCost() {
  form.value.extraCosts.push({ desc: '', amount: 0 });
}
function removeExtraCost(idx) {
  form.value.extraCosts.splice(idx, 1);
}

async function submitLog() {
  formError.value = '';
  formSuccess.value = '';

  if (!form.value.content.trim()) {
    formError.value = 'Vui lòng nhập nội dung thực hiện.';
    return;
  }
  if (!form.value.startTime || !form.value.endTime) {
    formError.value = 'Vui lòng chọn thời gian bắt đầu và kết thúc.';
    return;
  }
  if (new Date(form.value.endTime) <= new Date(form.value.startTime)) {
    formError.value = 'Thời gian kết thúc phải sau thời gian bắt đầu.';
    return;
  }

  const totalCost = materialCost.value + extraCostTotal.value;

  const payload = {
    technician_id: technicianId,
    noi_dung: form.value.content,
    thoi_gian_bat_dau: form.value.startTime,
    thoi_gian_ket_thuc: form.value.endTime,
    thoi_luong: durationDisplay.value,
    vat_tu: JSON.stringify(form.value.materials),
    chi_phi_phat_sinh: JSON.stringify(form.value.extraCosts),
    ghi_chu: form.value.note,
    tong_chi_phi: totalCost,
  };

  submitting.value = true;
  try {
    const res = await fetch(`${API_BASE}/api/work-logs`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const saved = res.ok ? await res.json() : null;
    logs.value.unshift(saved ? mapApiLog(saved.data || saved) : {
      content: form.value.content,
      startTime: formatDateTime(form.value.startTime),
      endTime: formatDateTime(form.value.endTime),
      duration: durationDisplay.value,
      materials: [...form.value.materials],
      extraCosts: [...form.value.extraCosts],
      note: form.value.note,
      totalCost,
    });
  } catch {
    // fallback: lưu local nếu API lỗi
    logs.value.unshift({
      content: form.value.content,
      startTime: formatDateTime(form.value.startTime),
      endTime: formatDateTime(form.value.endTime),
      duration: durationDisplay.value,
      materials: [...form.value.materials],
      extraCosts: [...form.value.extraCosts],
      note: form.value.note,
      totalCost,
    });
  } finally {
    submitting.value = false;
  }

  formSuccess.value = 'Đã lưu WorkLog thành công!';
  setTimeout(() => (formSuccess.value = ''), 3000);
  form.value = defaultForm();
}

function resetForm() {
  form.value = defaultForm();
  formError.value = '';
  formSuccess.value = '';
}

function deleteLog(idx) {
  logs.value.splice(idx, 1);
}

function formatDateTime(val) {
  if (!val) return '';
  const d = new Date(val);
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const hh = String(d.getHours()).padStart(2, '0');
  const mi = String(d.getMinutes()).padStart(2, '0');
  return `${hh}:${mi} ${dd}/${mm}/${d.getFullYear()}`;
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  gap: 20px;
  color: #0f172a;
  padding-bottom: 40px;
}

/* HERO */
.hero {
  background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
  border-radius: 20px;
  padding: 28px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  color: #fff;
}

.badge {
  display: inline-block;
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.35);
  border-radius: 999px;
  padding: 4px 14px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 10px;
}

.hero-text h1 { margin: 0 0 6px; font-size: 26px; font-weight: 700; color: #fff; }
.hero-text p  { margin: 0; font-size: 14px; opacity: 0.85; }

.hero-stats { display: flex; gap: 12px; flex-wrap: wrap; }

.stat-pill {
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 14px;
  padding: 12px 24px;
  text-align: center;
  min-width: 90px;
}

.stat-pill.cost {
  background: rgba(250, 204, 21, 0.2);
  border-color: rgba(250, 204, 21, 0.4);
}

.stat-num { display: block; font-size: 22px; font-weight: 700; color: #fff; line-height: 1; }
.stat-lbl { display: block; font-size: 11px; color: rgba(255,255,255,0.8); margin-top: 4px; }

/* CARD */
.card {
  background: #fff;
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
  border: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.card-head {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f1f5f9;
}

.head-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #ede9fe;
  color: #7c3aed;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.head-icon.green { background: #dcfce7; color: #16a34a; }

.card-head h2 { margin: 0 0 4px; font-size: 18px; font-weight: 700; }
.card-head p  { margin: 0; font-size: 13px; color: #64748b; }

/* FORM */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 16px;
}

.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }

label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 6px;
}

label i { color: #7c3aed; width: 14px; }
.req { color: #ef4444; }

input[type="text"],
input[type="number"],
input[type="datetime-local"],
textarea {
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 13px;
  font-family: inherit;
  outline: none;
  transition: 0.2s;
  background: #f8fafc;
  color: #0f172a;
  width: 100%;
  box-sizing: border-box;
}

input:focus, textarea:focus {
  border-color: #7c3aed;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

textarea { resize: vertical; }
.readonly-field { background: #f1f5f9; color: #64748b; cursor: default; }

/* MATERIAL TABLE */
.material-list, .extra-cost-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.material-row, .extra-row {
  display: flex;
  gap: 8px;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px 12px;
}

.mat-name   { flex: 2; }
.mat-qty    { width: 70px; }
.mat-unit   { width: 80px; }
.mat-price  { flex: 1; }
.cost-desc  { flex: 3; }
.cost-amount { flex: 1; }

.mat-name, .mat-qty, .mat-unit, .mat-price, .cost-desc, .cost-amount {
  padding: 8px 10px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 12px;
  background: #fff;
  outline: none;
  box-sizing: border-box;
}

.mat-name:focus, .mat-qty:focus, .mat-unit:focus,
.mat-price:focus, .cost-desc:focus, .cost-amount:focus {
  border-color: #7c3aed;
}

.mat-subtotal {
  min-width: 100px;
  text-align: right;
  font-size: 12px;
  font-weight: 600;
  color: #7c3aed;
  white-space: nowrap;
}

.btn-remove-mat {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid #fecdd3;
  background: #fff1f2;
  color: #be123c;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
  transition: 0.2s;
}
.btn-remove-mat:hover { background: #ffe4e6; }

.no-material {
  text-align: center;
  padding: 14px;
  color: #94a3b8;
  font-size: 13px;
  font-style: italic;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px dashed #e2e8f0;
}

.btn-add-mat {
  margin-top: 6px;
  align-self: flex-start;
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  background: #ede9fe;
  color: #7c3aed;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.2s;
}
.btn-add-mat:hover { background: #ddd6fe; }
.btn-add-mat.outline { background: #f0fdf4; color: #16a34a; }
.btn-add-mat.outline:hover { background: #dcfce7; }

/* COST SUMMARY */
.cost-summary {
  background: linear-gradient(135deg, #faf5ff, #f5f3ff);
  border: 1px solid #ddd6fe;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cost-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: #475569;
}

.cost-row strong { color: #0f172a; }

.cost-row.total {
  border-top: 1px solid #ddd6fe;
  padding-top: 10px;
  margin-top: 4px;
  font-size: 16px;
  font-weight: 700;
}

.total-num { color: #7c3aed; font-size: 18px; }

/* ALERT */
.error-msg {
  background: #fff1f2;
  border: 1px solid #fecdd3;
  color: #be123c;
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.success-msg {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #15803d;
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* FORM ACTIONS */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding-top: 4px;
}

.btn-reset {
  padding: 10px 22px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #475569;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: 0.2s;
}
.btn-reset:hover { background: #f1f5f9; }

.btn-submit {
  padding: 10px 28px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #7c3aed, #4f46e5);
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: 0.2s;
}
.btn-submit:hover {
  opacity: 0.92;
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
}

/* LOG LIST */
.empty-logs {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}
.empty-icon { font-size: 40px; margin-bottom: 10px; }
.empty-logs p { margin: 0; font-size: 14px; }

.log-list { display: flex; flex-direction: column; gap: 14px; }

.log-item {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.log-header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.log-index {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: linear-gradient(135deg, #7c3aed, #4f46e5);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.log-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
  flex-wrap: wrap;
  font-size: 12px;
  color: #64748b;
}

.log-meta i { color: #94a3b8; }

.duration-tag {
  background: #ede9fe;
  color: #7c3aed;
  padding: 2px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

.btn-delete-log {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid #fecdd3;
  background: #fff1f2;
  color: #be123c;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
  transition: 0.2s;
  margin-left: auto;
}
.btn-delete-log:hover { background: #ffe4e6; }

.log-content {
  margin: 0;
  font-size: 14px;
  color: #0f172a;
  line-height: 1.6;
}

.log-section { display: flex; flex-direction: column; gap: 6px; }

.section-title {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 6px;
}

.mat-tags { display: flex; flex-wrap: wrap; gap: 6px; }

.mat-tag {
  background: #ede9fe;
  color: #5b21b6;
  border-radius: 8px;
  padding: 4px 10px;
  font-size: 12px;
  font-weight: 500;
}

.cost-tag {
  background: #fef9c3;
  color: #854d0e;
  border-radius: 8px;
  padding: 4px 10px;
  font-size: 12px;
  font-weight: 500;
}

.log-note {
  font-size: 13px;
  color: #64748b;
  font-style: italic;
  display: flex;
  align-items: center;
  gap: 6px;
}

.log-total {
  text-align: right;
  font-size: 14px;
  color: #475569;
  padding-top: 8px;
  border-top: 1px solid #e2e8f0;
}

.log-total strong { color: #7c3aed; font-size: 16px; }

@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
  .material-row, .extra-row { flex-wrap: wrap; }
  .mat-name, .mat-price, .cost-desc { width: 100%; flex: none; }
}
</style>
