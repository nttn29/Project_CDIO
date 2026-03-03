<template>
  <div class="page">

    <!-- ===== HERO ===== -->
    <div class="hero">
      <div class="hero-left">
        <div class="hero-icon"><i class="fas fa-boxes-stacked"></i></div>
        <div>
          <span class="badge-pill">Kho vật tư</span>
          <h1>Quản lý Vật Tư</h1>
          <p>Theo dõi tồn kho, xuất vật tư và gửi yêu cầu cấp phát mới</p>
        </div>
      </div>
      <div class="hero-stats">
        <div class="stat-box">
          <span class="stat-icon blue"><i class="fas fa-cubes"></i></span>
          <div>
            <span class="stat-num">{{ inventory.length }}</span>
            <span class="stat-lbl">Chủng loại</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon green"><i class="fas fa-check-circle"></i></span>
          <div>
            <span class="stat-num">{{ okCount }}</span>
            <span class="stat-lbl">Đủ hàng</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></span>
          <div>
            <span class="stat-num">{{ lowCount }}</span>
            <span class="stat-lbl">Sắp hết</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon red"><i class="fas fa-times-circle"></i></span>
          <div>
            <span class="stat-num">{{ outCount }}</span>
            <span class="stat-lbl">Hết hàng</span>
          </div>
        </div>
        <div class="stat-box">
          <span class="stat-icon purple"><i class="fas fa-paper-plane"></i></span>
          <div>
            <span class="stat-num">{{ pendingRequests }}</span>
            <span class="stat-lbl">Đang yêu cầu</span>
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
        <span v-if="tab.badge" class="tab-badge">{{ tab.badge }}</span>
      </button>
    </div>

    <!-- ===== TAB: TỒN KHO ===== -->
    <template v-if="activeTab === 'inventory'">
      <div class="toolbar">
        <div class="toolbar-left">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input v-model="search" placeholder="Tìm kiếm vật tư..." />
          </div>
          <select v-model="filterCat" class="filter-sel">
            <option value="">Tất cả danh mục</option>
            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
          </select>
          <select v-model="filterStatus" class="filter-sel">
            <option value="">Tất cả trạng thái</option>
            <option value="ok">Đủ hàng</option>
            <option value="low">Sắp hết</option>
            <option value="out">Hết hàng</option>
          </select>
        </div>
        <button class="btn-request-top" @click="activeTab = 'request'">
          <i class="fas fa-plus"></i> Yêu cầu cấp vật tư
        </button>
      </div>

      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Mã</th>
              <th>Tên vật tư</th>
              <th>Danh mục</th>
              <th>Đơn vị</th>
              <th>Tồn kho</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredInventory.length === 0">
              <td colspan="8" class="empty-row">
                <i class="fas fa-box-open"></i>
                <p>Không tìm thấy vật tư nào</p>
              </td>
            </tr>
            <tr v-for="(item, idx) in filteredInventory" :key="item.id" :class="rowClass(item)">
              <td class="td-idx">{{ idx + 1 }}</td>
              <td><span class="code-tag">{{ item.code }}</span></td>
              <td class="td-name">{{ item.name }}</td>
              <td><span class="cat-tag">{{ item.category }}</span></td>
              <td class="td-unit">{{ item.unit }}</td>
              <td>
                <div class="stock-cell">
                  <span class="stock-num" :class="stockNumClass(item)">{{ item.stock }}</span>
                  <div class="stock-bar">
                    <div class="stock-fill" :class="stockBarClass(item)" :style="{ width: stockPercent(item) }"></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="status-pill" :class="statusClass(item)">
                  <i :class="statusIcon(item)"></i>
                  {{ statusLabel(item) }}
                </span>
              </td>
              <td>
                <div class="action-group">
                  <button
                    class="btn-act btn-use"
                    :disabled="item.stock === 0"
                    @click="openUseModal(item)"
                    title="Xuất dùng"
                  >
                    <i class="fas fa-hand-holding"></i> Xuất dùng
                  </button>
                  <button class="btn-act btn-req" @click="prefillRequest(item)" title="Yêu cầu cấp thêm">
                    <i class="fas fa-file-circle-plus"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <!-- ===== TAB: YÊU CẦU CẤP VẬT TƯ ===== -->
    <template v-if="activeTab === 'request'">
      <div class="two-col">
        <section class="card">
          <div class="card-head">
            <div class="head-icon purple"><i class="fas fa-file-circle-plus"></i></div>
            <div>
              <h2>Tạo yêu cầu mới</h2>
              <p>Điền thông tin vật tư cần cấp phát</p>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label><i class="fas fa-tag"></i> Tên vật tư <span class="req">*</span></label>
              <input v-model="reqForm.name" placeholder="VD: Bóng đèn LED 18W..." />
            </div>
            <div class="form-group">
              <label><i class="fas fa-layer-group"></i> Danh mục</label>
              <select v-model="reqForm.category">
                <option value="">-- Chọn danh mục --</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-sort-numeric-up"></i> Số lượng <span class="req">*</span></label>
              <input v-model.number="reqForm.qty" type="number" min="1" placeholder="1" />
            </div>
            <div class="form-group">
              <label><i class="fas fa-ruler"></i> Đơn vị</label>
              <input v-model="reqForm.unit" placeholder="VD: cái, m, kg, lít..." />
            </div>
            <div class="form-group">
              <label><i class="fas fa-fire-flame-curved"></i> Mức độ ưu tiên</label>
              <select v-model="reqForm.priority">
                <option value="low">Thấp – Bình thường</option>
                <option value="medium">Trung bình – Cần sớm</option>
                <option value="high">Cao – Khẩn cấp</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fas fa-briefcase"></i> Công việc liên quan</label>
              <input v-model="reqForm.jobRef" placeholder="VD: Yêu cầu #42, phòng 201..." />
            </div>
            <div class="form-group full">
              <label><i class="fas fa-comment-dots"></i> Lý do / Ghi chú</label>
              <textarea v-model="reqForm.note" rows="3" placeholder="Mô tả lý do cần cấp vật tư..."></textarea>
            </div>
          </div>

          <div class="form-footer">
            <button class="btn-cancel-form" @click="resetReqForm">
              <i class="fas fa-rotate-left"></i> Làm mới
            </button>
            <button class="btn-submit-req" @click="submitRequest">
              <i class="fas fa-paper-plane"></i> Gửi yêu cầu
            </button>
          </div>
        </section>

        <section class="card">
          <div class="card-head">
            <div class="head-icon blue"><i class="fas fa-list-check"></i></div>
            <div>
              <h2>Yêu cầu của tôi</h2>
              <p>Các yêu cầu đã gửi gần đây</p>
            </div>
          </div>

          <div class="req-list">
            <div v-if="myRequests.length === 0" class="empty-block">
              <i class="fas fa-inbox"></i>
              <p>Chưa có yêu cầu nào</p>
            </div>
            <div v-for="req in myRequests" :key="req.id" class="req-item">
              <div class="req-top">
                <div class="req-name">{{ req.name }}</div>
                <span class="req-status-pill" :class="reqStatusClass(req.status)">
                  <i :class="reqStatusIcon(req.status)"></i>
                  {{ reqStatusLabel(req.status) }}
                </span>
              </div>
              <div class="req-meta">
                <span><i class="fas fa-layer-group"></i> {{ req.category || '—' }}</span>
                <span><i class="fas fa-sort-numeric-up"></i> {{ req.qty }} {{ req.unit }}</span>
                <span :class="'prio-badge prio-' + req.priority">
                  <i class="fas fa-fire-flame-curved"></i>
                  {{ priorityLabel(req.priority) }}
                </span>
                <span class="req-time"><i class="fas fa-clock"></i> {{ req.time }}</span>
              </div>
              <div v-if="req.note" class="req-note">
                <i class="fas fa-comment-dots"></i> {{ req.note }}
              </div>
            </div>
          </div>
        </section>
      </div>
    </template>

    <!-- ===== TAB: LỊCH SỬ SỬ DỤNG ===== -->
    <template v-if="activeTab === 'history'">
      <div class="toolbar">
        <div class="toolbar-left">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input v-model="historySearch" placeholder="Tìm theo tên vật tư..." />
          </div>
        </div>
        <div class="total-used">
          Tổng lượt xuất: <strong>{{ filteredHistory.length }}</strong>
        </div>
      </div>

      <div class="table-card">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Thời gian</th>
              <th>Vật tư</th>
              <th>Danh mục</th>
              <th>SL xuất</th>
              <th>Đơn vị</th>
              <th>Công việc</th>
              <th>Ghi chú</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredHistory.length === 0">
              <td colspan="8" class="empty-row">
                <i class="fas fa-clock-rotate-left"></i>
                <p>Chưa có lịch sử sử dụng</p>
              </td>
            </tr>
            <tr v-for="(h, idx) in filteredHistory" :key="h.id">
              <td class="td-idx">{{ idx + 1 }}</td>
              <td class="td-time">{{ h.time }}</td>
              <td class="td-name">{{ h.itemName }}</td>
              <td><span class="cat-tag">{{ h.category }}</span></td>
              <td><strong class="qty-out">-{{ h.qty }}</strong></td>
              <td>{{ h.unit }}</td>
              <td class="td-job">{{ h.jobRef || '—' }}</td>
              <td class="td-note">{{ h.note || '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <!-- ===== MODAL XUẤT DÙNG ===== -->
    <div v-if="showUseModal" class="modal-overlay" @click.self="closeUseModal">
      <div class="modal-box">
        <div class="modal-header">
          <h2><i class="fas fa-hand-holding"></i> Xuất vật tư</h2>
          <button class="modal-close" @click="closeUseModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
          <div class="selected-item-info">
            <div class="si-name">{{ selectedItem?.name }}</div>
            <div class="si-meta">
              <span class="cat-tag">{{ selectedItem?.category }}</span>
              <span>Tồn kho: <strong>{{ selectedItem?.stock }} {{ selectedItem?.unit }}</strong></span>
            </div>
          </div>
          <div class="form-group">
            <label><i class="fas fa-sort-numeric-up"></i> Số lượng xuất <span class="req">*</span></label>
            <input
              v-model.number="useForm.qty"
              type="number"
              min="1"
              :max="selectedItem?.stock"
              placeholder="Nhập số lượng..."
              class="use-qty-input"
            />
            <p class="input-hint">Tối đa: {{ selectedItem?.stock }} {{ selectedItem?.unit }}</p>
          </div>
          <div class="form-group">
            <label><i class="fas fa-briefcase"></i> Công việc liên quan</label>
            <input v-model="useForm.jobRef" placeholder="VD: Yêu cầu #42, Phòng 301..." />
          </div>
          <div class="form-group">
            <label><i class="fas fa-comment-dots"></i> Ghi chú</label>
            <textarea v-model="useForm.note" rows="2" placeholder="Ghi chú thêm..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel-form" @click="closeUseModal">Huỷ</button>
          <button class="btn-submit-use" @click="confirmUse">
            <i class="fas fa-check"></i> Xác nhận xuất
          </button>
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

// ===== DATA =====
const inventory = ref([
  { id: 1, code: 'VT-001', name: 'Bóng đèn LED 18W',       category: 'Điện',    unit: 'cái',  stock: 24, minStock: 5  },
  { id: 2, code: 'VT-002', name: 'Dây điện Cadivi 2.5mm',  category: 'Điện',    unit: 'mét',  stock: 80, minStock: 20 },
  { id: 3, code: 'VT-003', name: 'Ống nước PVC Ø27',       category: 'Nước',    unit: 'mét',  stock: 3,  minStock: 5  },
  { id: 4, code: 'VT-004', name: 'Băng keo điện 3M',       category: 'Điện',    unit: 'cuộn', stock: 6,  minStock: 3  },
  { id: 5, code: 'VT-005', name: 'Khóa cửa tay gạt',      category: 'Cơ khí',  unit: 'cái',  stock: 0,  minStock: 2  },
  { id: 6, code: 'VT-006', name: 'Sơn tường nội thất',    category: 'Xây dựng',unit: 'lít',  stock: 12, minStock: 4  },
  { id: 7, code: 'VT-007', name: 'Cầu dao tự động 16A',   category: 'Điện',    unit: 'cái',  stock: 4,  minStock: 5  },
  { id: 8, code: 'VT-008', name: 'Van chặn nước Ø21',     category: 'Nước',    unit: 'cái',  stock: 8,  minStock: 3  },
  { id: 9, code: 'VT-009', name: 'Ổ cắm điện 3 chấu',    category: 'Điện',    unit: 'cái',  stock: 15, minStock: 5  },
  { id: 10,code: 'VT-010', name: 'Keo silicon chống thấm',category: 'Xây dựng',unit: 'tuýp', stock: 2,  minStock: 4  },
]);

const usageHistory = ref([
  { id: 1, time: '02/03/2026 09:15', itemName: 'Bóng đèn LED 18W',     category: 'Điện',    qty: 2, unit: 'cái', jobRef: 'YC #38 – Phòng 302', note: 'Thay bóng hành lang' },
  { id: 2, time: '01/03/2026 14:30', itemName: 'Băng keo điện 3M',     category: 'Điện',    qty: 1, unit: 'cuộn',jobRef: 'YC #35 – Phòng 118', note: '' },
  { id: 3, time: '28/02/2026 11:00', itemName: 'Ống nước PVC Ø27',     category: 'Nước',    qty: 2, unit: 'mét', jobRef: 'YC #30 – Căn hộ 501', note: 'Sửa ống bị nứt' },
  { id: 4, time: '27/02/2026 16:45', itemName: 'Dây điện Cadivi 2.5mm',category: 'Điện',    qty: 10,unit: 'mét', jobRef: 'YC #29 – Bảng điện tầng 3', note: '' },
  { id: 5, time: '25/02/2026 10:20', itemName: 'Sơn tường nội thất',   category: 'Xây dựng',qty: 3, unit: 'lít', jobRef: 'YC #25 – Phòng 204', note: 'Sơn lại tường ẩm' },
]);

const myRequests = ref([
  { id: 1, name: 'Khóa cửa tay gạt', category: 'Cơ khí',  qty: 3, unit: 'cái', priority: 'high',   status: 'pending',  time: '02/03/2026 08:00', note: 'Hết hoàn toàn, cần gấp' },
  { id: 2, name: 'Keo silicon chống thấm', category: 'Xây dựng', qty: 5, unit: 'tuýp', priority: 'medium', status: 'approved', time: '28/02/2026 14:00', note: '' },
  { id: 3, name: 'Cầu dao tự động 16A', category: 'Điện', qty: 4, unit: 'cái', priority: 'medium', status: 'done',     time: '25/02/2026 09:00', note: '' },
]);

// ===== TABS =====
const activeTab = ref('inventory');
const tabs = computed(() => [
  { key: 'inventory', label: 'Tồn kho',          icon: 'fas fa-warehouse',        badge: null },
  { key: 'request',   label: 'Yêu cầu cấp phát', icon: 'fas fa-file-circle-plus', badge: pendingRequests.value || null },
  { key: 'history',   label: 'Lịch sử sử dụng',  icon: 'fas fa-clock-rotate-left',badge: null },
]);

// ===== STATS =====
const okCount         = computed(() => inventory.value.filter(i => i.stock > i.minStock).length);
const lowCount        = computed(() => inventory.value.filter(i => i.stock > 0 && i.stock <= i.minStock).length);
const outCount        = computed(() => inventory.value.filter(i => i.stock === 0).length);
const pendingRequests = computed(() => myRequests.value.filter(r => r.status === 'pending').length);

// ===== FILTER =====
const search       = ref('');
const filterCat    = ref('');
const filterStatus = ref('');
const categories   = computed(() => [...new Set(inventory.value.map(i => i.category))]);

const filteredInventory = computed(() =>
  inventory.value.filter(item => {
    const matchSearch = !search.value || item.name.toLowerCase().includes(search.value.toLowerCase()) || item.code.toLowerCase().includes(search.value.toLowerCase());
    const matchCat    = !filterCat.value || item.category === filterCat.value;
    const matchStatus = !filterStatus.value || getItemStatus(item) === filterStatus.value;
    return matchSearch && matchCat && matchStatus;
  })
);

// ===== INVENTORY HELPERS =====
function getItemStatus(item) {
  if (item.stock === 0)              return 'out';
  if (item.stock <= item.minStock)   return 'low';
  return 'ok';
}
function rowClass(item)      { const s = getItemStatus(item); return { 'row-low': s === 'low', 'row-out': s === 'out' }; }
function stockNumClass(item) { const s = getItemStatus(item); return { 'sn-ok': s === 'ok', 'sn-low': s === 'low', 'sn-out': s === 'out' }; }
function stockBarClass(item) { const s = getItemStatus(item); return { 'sb-ok': s === 'ok', 'sb-low': s === 'low', 'sb-out': s === 'out' }; }
function stockPercent(item) {
  if (item.stock === 0) return '0%';
  const max = Math.max(item.minStock * 3, item.stock);
  return Math.min(100, Math.round((item.stock / max) * 100)) + '%';
}
function statusClass(item)  { const s = getItemStatus(item); return { 'pill-ok': s === 'ok', 'pill-low': s === 'low', 'pill-out': s === 'out' }; }
function statusIcon(item)   { const s = getItemStatus(item); return s === 'ok' ? 'fas fa-check-circle' : s === 'low' ? 'fas fa-exclamation-triangle' : 'fas fa-times-circle'; }
function statusLabel(item)  { const s = getItemStatus(item); return s === 'ok' ? 'Đủ hàng' : s === 'low' ? 'Sắp hết' : 'Hết hàng'; }

// ===== REQUEST FORM =====
const reqForm = ref({ name: '', category: '', qty: 1, unit: '', priority: 'medium', jobRef: '', note: '' });

function resetReqForm() {
  reqForm.value = { name: '', category: '', qty: 1, unit: '', priority: 'medium', jobRef: '', note: '' };
}
function prefillRequest(item) {
  activeTab.value = 'request';
  reqForm.value = { name: item.name, category: item.category, qty: item.minStock, unit: item.unit, priority: item.stock === 0 ? 'high' : 'medium', jobRef: '', note: '' };
}
function submitRequest() {
  if (!reqForm.value.name.trim() || !reqForm.value.qty) {
    showToast('Vui lòng nhập tên vật tư và số lượng!', 'error', 'fas fa-exclamation-circle');
    return;
  }
  const now = new Date();
  const pad = n => n.toString().padStart(2, '0');
  const timeStr = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}`;
  myRequests.value.unshift({ id: Date.now(), ...reqForm.value, status: 'pending', time: timeStr });
  resetReqForm();
  showToast('Đã gửi yêu cầu cấp vật tư thành công!', 'success', 'fas fa-check-circle');
}

function reqStatusClass(s) { return { 'rs-pending': s === 'pending', 'rs-approved': s === 'approved', 'rs-done': s === 'done', 'rs-rejected': s === 'rejected' }; }
function reqStatusIcon(s)  { return s === 'pending' ? 'fas fa-clock' : s === 'approved' ? 'fas fa-thumbs-up' : s === 'done' ? 'fas fa-check-double' : 'fas fa-times'; }
function reqStatusLabel(s) { return s === 'pending' ? 'Chờ duyệt' : s === 'approved' ? 'Đã duyệt' : s === 'done' ? 'Đã cấp phát' : 'Từ chối'; }
function priorityLabel(p)  { return p === 'high' ? 'Khẩn cấp' : p === 'medium' ? 'Trung bình' : 'Thấp'; }

// ===== USE MODAL =====
const showUseModal = ref(false);
const selectedItem = ref(null);
const useForm      = ref({ qty: 1, jobRef: '', note: '' });

function openUseModal(item) { selectedItem.value = item; useForm.value = { qty: 1, jobRef: '', note: '' }; showUseModal.value = true; }
function closeUseModal()    { showUseModal.value = false; selectedItem.value = null; }

function confirmUse() {
  const qty = useForm.value.qty;
  if (!qty || qty < 1)                    { showToast('Số lượng không hợp lệ!', 'error', 'fas fa-exclamation-circle'); return; }
  if (qty > selectedItem.value.stock)     { showToast('Số lượng vượt quá tồn kho!', 'error', 'fas fa-exclamation-circle'); return; }
  const now = new Date();
  const pad = n => n.toString().padStart(2, '0');
  const timeStr = `${pad(now.getDate())}/${pad(now.getMonth()+1)}/${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}`;
  const name = selectedItem.value.name;
  const unit = selectedItem.value.unit;
  selectedItem.value.stock -= qty;
  usageHistory.value.unshift({ id: Date.now(), time: timeStr, itemName: name, category: selectedItem.value.category, qty, unit, jobRef: useForm.value.jobRef, note: useForm.value.note });
  closeUseModal();
  showToast(`Đã xuất ${qty} ${unit} – ${name}`, 'success', 'fas fa-check-circle');
}

// ===== HISTORY =====
const historySearch   = ref('');
const filteredHistory = computed(() =>
  usageHistory.value.filter(h => !historySearch.value || h.itemName.toLowerCase().includes(historySearch.value.toLowerCase()))
);

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
  background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
  border-radius: 20px;
  padding: 28px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
  box-shadow: 0 8px 30px rgba(15,23,42,.25);
}
.hero-left { display: flex; align-items: center; gap: 18px; }
.hero-icon {
  width: 60px; height: 60px;
  background: rgba(255,255,255,.08);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; color: #60a5fa;
}
.hero-left h1 { margin: 4px 0 4px; font-size: 26px; font-weight: 800; color: #fff; }
.hero-left p  { margin: 0; font-size: 13px; color: #94a3b8; }

.badge-pill {
  display: inline-block;
  background: rgba(96,165,250,.15);
  border: 1px solid rgba(96,165,250,.3);
  color: #60a5fa;
  border-radius: 20px;
  padding: 2px 12px;
  font-size: 11px; font-weight: 600;
  letter-spacing: .5px; text-transform: uppercase;
  margin-bottom: 4px;
}

.hero-stats { display: flex; gap: 12px; flex-wrap: wrap; }
.stat-box {
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 14px; padding: 14px 18px;
  display: flex; align-items: center; gap: 12px; min-width: 110px;
}
.stat-icon {
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.stat-icon.blue   { background: rgba(59,130,246,.2);  color: #60a5fa; }
.stat-icon.green  { background: rgba(34,197,94,.2);   color: #4ade80; }
.stat-icon.orange { background: rgba(251,146,60,.2);  color: #fb923c; }
.stat-icon.red    { background: rgba(239,68,68,.2);   color: #f87171; }
.stat-icon.purple { background: rgba(168,85,247,.2);  color: #c084fc; }
.stat-box > div { display: flex; flex-direction: column; }
.stat-num { font-size: 22px; font-weight: 800; color: #fff; line-height: 1; }
.stat-lbl { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* ===== TABS ===== */
.tabs {
  display: flex; gap: 6px;
  border-bottom: 2px solid #e2e8f0; padding-bottom: 0;
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
.tab-btn.active { color: #3b82f6; font-weight: 700; border-bottom-color: #3b82f6; background: #eff6ff; }
.tab-badge {
  background: #ef4444; color: #fff;
  border-radius: 99px; padding: 1px 7px;
  font-size: 11px; font-weight: 700; min-width: 18px; text-align: center;
}

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
.filter-sel:focus { border-color: #3b82f6; }
.btn-request-top {
  display: flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: #fff; border: none; border-radius: 10px;
  padding: 10px 18px; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: .2s;
  box-shadow: 0 4px 12px rgba(59,130,246,.3);
}
.btn-request-top:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59,130,246,.4); }

/* ===== TABLE ===== */
.table-card {
  background: #fff; border-radius: 16px;
  box-shadow: 0 4px 20px rgba(15,23,42,.06); overflow: hidden;
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
tbody tr:hover { background: #f8fafc; }
tbody tr.row-low { background: #fffbeb; }
tbody tr.row-low:hover { background: #fef3c7; }
tbody tr.row-out { background: #fef2f2; }
tbody tr.row-out:hover { background: #fee2e2; }

.td-idx  { color: #94a3b8; font-size: 12px; width: 36px; }
.td-name { font-weight: 600; color: #1e293b; }
.td-unit { color: #64748b; }
.td-time { color: #64748b; font-size: 12px; white-space: nowrap; }
.td-job  { color: #334155; font-size: 12px; }
.td-note { color: #64748b; font-size: 12px; max-width: 200px; }

.code-tag { background: #f1f5f9; color: #334155; border-radius: 6px; padding: 3px 8px; font-size: 11px; font-weight: 700; font-family: 'Courier New', monospace; }
.cat-tag  { background: #dbeafe; color: #1d4ed8; border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }

.stock-cell { display: flex; flex-direction: column; gap: 4px; }
.stock-num  { font-weight: 700; font-size: 15px; }
.sn-ok  { color: #16a34a; }
.sn-low { color: #ea580c; }
.sn-out { color: #dc2626; }
.stock-bar  { height: 5px; background: #f1f5f9; border-radius: 999px; width: 80px; }
.stock-fill { height: 100%; border-radius: 999px; transition: width .4s; }
.sb-ok  { background: #22c55e; }
.sb-low { background: #f97316; }
.sb-out { background: #ef4444; }

.status-pill { display: inline-flex; align-items: center; gap: 5px; border-radius: 20px; padding: 4px 10px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.pill-ok  { background: #dcfce7; color: #15803d; }
.pill-low { background: #ffedd5; color: #c2410c; }
.pill-out { background: #fee2e2; color: #b91c1c; }

.action-group { display: flex; align-items: center; gap: 6px; }
.btn-act { display: flex; align-items: center; gap: 5px; border: none; border-radius: 8px; padding: 7px 12px; font-size: 12px; font-weight: 600; cursor: pointer; transition: .2s; }
.btn-use { background: #eff6ff; color: #2563eb; }
.btn-use:hover:not(:disabled) { background: #dbeafe; }
.btn-use:disabled { opacity: .4; cursor: not-allowed; }
.btn-req { background: #f0fdf4; color: #15803d; padding: 7px 10px; }
.btn-req:hover { background: #dcfce7; }
.qty-out { color: #dc2626; font-size: 14px; }

.empty-row { text-align: center; padding: 48px 20px; color: #94a3b8; font-size: 13px; }
.empty-row i { font-size: 36px; margin-bottom: 10px; display: block; opacity: .4; }
.empty-row p { margin: 0; }

/* ===== TWO-COL ===== */
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
@media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

/* ===== CARD ===== */
.card {
  background: #fff; border-radius: 16px; padding: 24px;
  box-shadow: 0 4px 20px rgba(15,23,42,.06);
  display: flex; flex-direction: column; gap: 18px;
}
.card-head {
  display: flex; align-items: center; gap: 14px;
  border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;
}
.head-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.head-icon.purple { background: #f3e8ff; color: #9333ea; }
.head-icon.blue   { background: #dbeafe; color: #2563eb; }
.card-head h2 { margin: 0; font-size: 17px; font-weight: 700; color: #1e293b; }
.card-head p  { margin: 3px 0 0; font-size: 12px; color: #64748b; }

/* ===== FORM ===== */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 12px; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 6px; }
.form-group label i { color: #94a3b8; }
.req { color: #ef4444; }
.form-group input,
.form-group select,
.form-group textarea {
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  padding: 10px 13px; font-size: 13px; color: #1e293b;
  outline: none; transition: border-color .2s; background: #fff; resize: none;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color: #3b82f6; }

.form-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px; }
.btn-cancel-form {
  background: #f1f5f9; color: #475569; border: none;
  border-radius: 10px; padding: 10px 18px;
  font-size: 13px; font-weight: 600; cursor: pointer; transition: .2s;
  display: flex; align-items: center; gap: 7px;
}
.btn-cancel-form:hover { background: #e2e8f0; }
.btn-submit-req {
  background: linear-gradient(135deg, #9333ea, #7c3aed);
  color: #fff; border: none; border-radius: 10px;
  padding: 10px 22px; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: .2s;
  display: flex; align-items: center; gap: 7px;
  box-shadow: 0 4px 14px rgba(147,51,234,.3);
}
.btn-submit-req:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(147,51,234,.4); }

/* ===== REQUEST LIST ===== */
.req-list { display: flex; flex-direction: column; gap: 10px; max-height: 420px; overflow-y: auto; }
.empty-block { text-align: center; padding: 40px; color: #94a3b8; }
.empty-block i { font-size: 32px; display: block; margin-bottom: 10px; opacity: .4; }
.empty-block p { margin: 0; font-size: 13px; }

.req-item {
  background: #f8fafc; border-radius: 12px; padding: 14px 16px;
  border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 8px; transition: .2s;
}
.req-item:hover { border-color: #cbd5e1; background: #f1f5f9; }
.req-top  { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
.req-name { font-weight: 700; font-size: 14px; color: #1e293b; }
.req-meta { display: flex; flex-wrap: wrap; gap: 8px; font-size: 12px; color: #64748b; align-items: center; }
.req-meta i { color: #94a3b8; margin-right: 2px; }
.req-time { color: #94a3b8; font-size: 11px; }
.req-note { font-size: 12px; color: #64748b; font-style: italic; }

.req-status-pill { display: inline-flex; align-items: center; gap: 5px; border-radius: 20px; padding: 4px 10px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.rs-pending  { background: #fef9c3; color: #854d0e; }
.rs-approved { background: #dbeafe; color: #1d4ed8; }
.rs-done     { background: #dcfce7; color: #15803d; }
.rs-rejected { background: #fee2e2; color: #b91c1c; }

.prio-badge { display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; padding: 2px 8px; font-size: 11px; font-weight: 600; }
.prio-high   { background: #fee2e2; color: #b91c1c; }
.prio-medium { background: #ffedd5; color: #c2410c; }
.prio-low    { background: #f0fdf4; color: #15803d; }

/* ===== HISTORY ===== */
.total-used { font-size: 13px; color: #64748b; }
.total-used strong { color: #1e293b; }

/* ===== MODAL ===== */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(15,23,42,.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 9999; padding: 20px;
  backdrop-filter: blur(4px);
}
.modal-box {
  background: #fff; border-radius: 20px; width: 100%; max-width: 480px;
  box-shadow: 0 25px 60px rgba(15,23,42,.25); overflow: hidden;
}
.modal-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 24px;
  background: linear-gradient(135deg, #1e293b, #0f172a);
  border-bottom: 1px solid rgba(255,255,255,.07);
}
.modal-header h2 { margin: 0; font-size: 17px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; }
.modal-header h2 i { color: #60a5fa; }
.modal-close {
  background: rgba(255,255,255,.1); border: none; color: #94a3b8;
  width: 32px; height: 32px; border-radius: 8px;
  cursor: pointer; font-size: 14px; transition: .2s;
}
.modal-close:hover { background: rgba(255,255,255,.2); color: #fff; }
.modal-body { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f1f5f9; background: #f8fafc; }

.selected-item-info { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px; }
.si-name { font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.si-meta { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #475569; }
.use-qty-input { width: 100%; font-size: 18px !important; font-weight: 700 !important; text-align: center; }
.input-hint { margin: 4px 0 0; font-size: 11px; color: #94a3b8; }

.btn-submit-use {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: #fff; border: none; border-radius: 10px;
  padding: 10px 22px; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: .2s;
  display: flex; align-items: center; gap: 7px;
  box-shadow: 0 4px 14px rgba(34,197,94,.3);
}
.btn-submit-use:hover { transform: translateY(-1px); }

/* ===== TOAST ===== */
.toast {
  position: fixed; bottom: 28px; right: 28px;
  padding: 14px 20px; border-radius: 12px;
  font-size: 14px; font-weight: 600;
  display: flex; align-items: center; gap: 10px;
  z-index: 99999;
  box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.toast-success { background: #22c55e; color: #fff; }
.toast-error   { background: #ef4444; color: #fff; }
.toast-fade-enter-active,
.toast-fade-leave-active { transition: all .3s ease; }
.toast-fade-enter-from,
.toast-fade-leave-to { opacity: 0; transform: translateY(12px); }
</style>
