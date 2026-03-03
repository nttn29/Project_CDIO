<template>
  <div class="page">
    <!-- HEADER HERO -->
    <div class="vt-hero">
      <div class="hero-left">
        <div class="hero-icon"><i class="fas fa-boxes"></i></div>
        <div>
          <h1>Quản lý Vật tư</h1>
          <p>Theo dõi tồn kho, nhập – xuất và yêu cầu cấp phát vật tư</p>
        </div>
      </div>
      <div class="hero-stats">
        <div class="hstat">
          <span class="hstat-num">{{ totalItems }}</span>
          <span class="hstat-lbl">Tổng chủng loại</span>
        </div>
        <div class="hstat">
          <span class="hstat-num text-warning">{{ lowStockCount }}</span>
          <span class="hstat-lbl">Sắp hết hàng</span>
        </div>
        <div class="hstat">
          <span class="hstat-num text-danger">{{ outOfStockCount }}</span>
          <span class="hstat-lbl">Hết hàng</span>
        </div>
      </div>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input v-model="search" placeholder="Tìm kiếm vật tư..." />
        </div>
        <select v-model="filterCategory" class="filter-select">
          <option value="">Tất cả danh mục</option>
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
        <select v-model="filterStatus" class="filter-select">
          <option value="">Tất cả trạng thái</option>
          <option value="ok">Đủ hàng</option>
          <option value="low">Sắp hết</option>
          <option value="out">Hết hàng</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button class="btn-export" @click="exportCSV">
          <i class="fas fa-file-export"></i> Xuất Excel
        </button>
        <button class="btn-add" @click="openAddModal">
          <i class="fas fa-plus"></i> Thêm vật tư
        </button>
      </div>
    </div>

    <!-- TABLE -->
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
            <th>Mức cảnh báo</th>
            <th>Đơn giá</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredItems.length === 0">
            <td colspan="10" class="empty-row">
              <i class="fas fa-box-open"></i>
              <p>Không tìm thấy vật tư nào</p>
            </td>
          </tr>
          <tr v-for="(item, idx) in filteredItems" :key="item.id" :class="rowClass(item)">
            <td class="td-idx">{{ idx + 1 }}</td>
            <td><span class="code-badge">{{ item.code }}</span></td>
            <td class="td-name">{{ item.name }}</td>
            <td><span class="cat-tag">{{ item.category }}</span></td>
            <td>{{ item.unit }}</td>
            <td>
              <div class="stock-cell">
                <span class="stock-num" :class="stockNumClass(item)">{{ item.stock }}</span>
                <div class="stock-bar-bg">
                  <div class="stock-bar-fill" :class="stockBarClass(item)" :style="{ width: stockPercent(item) }"></div>
                </div>
              </div>
            </td>
            <td class="td-warn">{{ item.minStock }}</td>
            <td>{{ formatPrice(item.price) }}</td>
            <td>
              <span class="status-badge" :class="statusClass(item)">
                <i :class="statusIcon(item)"></i>
                {{ statusLabel(item) }}
              </span>
            </td>
            <td>
              <div class="action-group">
                <button class="btn-action btn-in" @click="openAdjustModal(item, 'in')" title="Nhập kho">
                  <i class="fas fa-arrow-down"></i>
                </button>
                <button class="btn-action btn-out" @click="openAdjustModal(item, 'out')" title="Xuất kho">
                  <i class="fas fa-arrow-up"></i>
                </button>
                <button class="btn-action btn-edit" @click="openEditModal(item)" title="Chỉnh sửa">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-del" @click="deleteItem(item.id)" title="Xoá">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- RECENT TRANSACTIONS -->
    <div class="section-title">
      <i class="fas fa-history"></i> Lịch sử giao dịch gần đây
    </div>
    <div class="table-card">
      <table>
        <thead>
          <tr>
            <th>Thời gian</th>
            <th>Loại</th>
            <th>Vật tư</th>
            <th>Số lượng</th>
            <th>Người thực hiện</th>
            <th>Ghi chú</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tx in transactions" :key="tx.id">
            <td class="td-time">{{ tx.time }}</td>
            <td>
              <span class="tx-type" :class="tx.type === 'in' ? 'tx-in' : 'tx-out'">
                <i :class="tx.type === 'in' ? 'fas fa-arrow-circle-down' : 'fas fa-arrow-circle-up'"></i>
                {{ tx.type === 'in' ? 'Nhập kho' : 'Xuất kho' }}
              </span>
            </td>
            <td>{{ tx.itemName }}</td>
            <td><strong>{{ tx.qty }}</strong></td>
            <td>{{ tx.by }}</td>
            <td class="td-note">{{ tx.note }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ===== MODAL THÊM / SỬA ===== -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-box">
        <div class="modal-header">
          <h2><i :class="modalMode === 'add' ? 'fas fa-plus-circle' : 'fas fa-edit'"></i>
            {{ modalMode === 'add' ? 'Thêm vật tư mới' : 'Chỉnh sửa vật tư' }}</h2>
          <button class="modal-close" @click="closeModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label>Mã vật tư</label>
              <input v-model="form.code" placeholder="VD: VT-001" />
            </div>
            <div class="form-group">
              <label>Tên vật tư</label>
              <input v-model="form.name" placeholder="Nhập tên vật tư" />
            </div>
            <div class="form-group">
              <label>Danh mục</label>
              <select v-model="form.category">
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
              </select>
            </div>
            <div class="form-group">
              <label>Đơn vị tính</label>
              <input v-model="form.unit" placeholder="VD: cái, m, lít..." />
            </div>
            <div class="form-group">
              <label>Tồn kho hiện tại</label>
              <input v-model.number="form.stock" type="number" min="0" />
            </div>
            <div class="form-group">
              <label>Mức cảnh báo tối thiểu</label>
              <input v-model.number="form.minStock" type="number" min="0" />
            </div>
            <div class="form-group full">
              <label>Đơn giá (VNĐ)</label>
              <input v-model.number="form.price" type="number" min="0" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="closeModal">Huỷ</button>
          <button class="btn-save" @click="saveItem">
            <i class="fas fa-save"></i> Lưu
          </button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL NHẬP / XUẤT KHO ===== -->
    <div v-if="showAdjustModal" class="modal-overlay" @click.self="showAdjustModal = false">
      <div class="modal-box modal-sm">
        <div class="modal-header">
          <h2>
            <i :class="adjustType === 'in' ? 'fas fa-arrow-down text-success' : 'fas fa-arrow-up text-danger'"></i>
            {{ adjustType === 'in' ? 'Nhập kho' : 'Xuất kho' }}: {{ adjustItem?.name }}
          </h2>
          <button class="modal-close" @click="showAdjustModal = false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
          <div class="form-group" style="margin-bottom:16px">
            <label>Số lượng {{ adjustType === 'in' ? 'nhập' : 'xuất' }}</label>
            <input v-model.number="adjustQty" type="number" min="1" placeholder="Nhập số lượng" />
          </div>
          <div class="form-group">
            <label>Ghi chú</label>
            <input v-model="adjustNote" placeholder="VD: Nhập từ nhà cung cấp ABC" />
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel" @click="showAdjustModal = false">Huỷ</button>
          <button class="btn-save" @click="confirmAdjust">
            <i class="fas fa-check"></i> Xác nhận
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AdminQuanLyVatTu",
  data() {
    return {
      search: "",
      filterCategory: "",
      filterStatus: "",
      showModal: false,
      showAdjustModal: false,
      modalMode: "add",
      adjustType: "in",
      adjustItem: null,
      adjustQty: 1,
      adjustNote: "",
      form: { code: "", name: "", category: "Điện", unit: "", stock: 0, minStock: 10, price: 0 },
      categories: ["Điện", "Nước - Ống", "Cơ khí", "Sơn - Vật liệu", "An toàn", "Vệ sinh", "Khác"],
      items: [
        { id: 1, code: "VT-001", name: "Bóng đèn LED 9W", category: "Điện", unit: "cái", stock: 45, minStock: 20, price: 35000 },
        { id: 2, code: "VT-002", name: "Dây điện 1.5mm (50m)", category: "Điện", unit: "cuộn", stock: 8, minStock: 10, price: 180000 },
        { id: 3, code: "VT-003", name: "Ổ cắm điện 3 chân", category: "Điện", unit: "cái", stock: 0, minStock: 15, price: 45000 },
        { id: 4, code: "VT-004", name: "Ống nước PVC 21mm", category: "Nước - Ống", unit: "m", stock: 120, minStock: 30, price: 25000 },
        { id: 5, code: "VT-005", name: "Van khóa nước 21mm", category: "Nước - Ống", unit: "cái", stock: 6, minStock: 10, price: 85000 },
        { id: 6, code: "VT-006", name: "Sơn nội thất Classic (5L)", category: "Sơn - Vật liệu", unit: "thùng", stock: 22, minStock: 5, price: 420000 },
        { id: 7, code: "VT-007", name: "Găng tay bảo hộ", category: "An toàn", unit: "đôi", stock: 3, minStock: 20, price: 15000 },
        { id: 8, code: "VT-008", name: "Nước tẩy rửa đa năng 1L", category: "Vệ sinh", unit: "chai", stock: 50, minStock: 20, price: 55000 },
        { id: 9, code: "VT-009", name: "Bu lông M10 (hộp 50 cái)", category: "Cơ khí", unit: "hộp", stock: 12, minStock: 5, price: 75000 },
        { id: 10, code: "VT-010", name: "Keo silicon trắng", category: "Sơn - Vật liệu", unit: "tuýp", stock: 0, minStock: 10, price: 42000 },
      ],
      transactions: [
        { id: 1, time: "02/03/2026 08:15", type: "in", itemName: "Bóng đèn LED 9W", qty: 50, by: "Quản Lý Tuấn", note: "Nhập từ kho trung tâm" },
        { id: 2, time: "01/03/2026 14:30", type: "out", itemName: "Van khóa nước 21mm", qty: 2, by: "Nguyễn Văn A", note: "Sửa chữa căn hộ A-502" },
        { id: 3, time: "01/03/2026 10:00", type: "out", itemName: "Dây điện 1.5mm (50m)", qty: 1, by: "Trần Thị B", note: "Bảo trì hành lang tầng 3" },
        { id: 4, time: "28/02/2026 16:45", type: "in", itemName: "Sơn nội thất Classic", qty: 10, by: "Quản Lý Tuấn", note: "Nhập theo hóa đơn #2024" },
        { id: 5, time: "28/02/2026 09:20", type: "out", itemName: "Găng tay bảo hộ", qty: 5, by: "Lê Văn C", note: "Cấp phát cho đội kỹ thuật" },
      ],
      nextId: 11,
    };
  },
  computed: {
    filteredItems() {
      return this.items.filter((item) => {
        const matchSearch = item.name.toLowerCase().includes(this.search.toLowerCase()) ||
          item.code.toLowerCase().includes(this.search.toLowerCase());
        const matchCat = !this.filterCategory || item.category === this.filterCategory;
        const matchStatus = !this.filterStatus ||
          (this.filterStatus === "ok" && item.stock >= item.minStock) ||
          (this.filterStatus === "low" && item.stock > 0 && item.stock < item.minStock) ||
          (this.filterStatus === "out" && item.stock === 0);
        return matchSearch && matchCat && matchStatus;
      });
    },
    totalItems() { return this.items.length; },
    lowStockCount() { return this.items.filter(i => i.stock > 0 && i.stock < i.minStock).length; },
    outOfStockCount() { return this.items.filter(i => i.stock === 0).length; },
  },
  methods: {
    statusLabel(item) {
      if (item.stock === 0) return "Hết hàng";
      if (item.stock < item.minStock) return "Sắp hết";
      return "Đủ hàng";
    },
    statusClass(item) {
      if (item.stock === 0) return "badge-danger";
      if (item.stock < item.minStock) return "badge-warning";
      return "badge-success";
    },
    statusIcon(item) {
      if (item.stock === 0) return "fas fa-times-circle";
      if (item.stock < item.minStock) return "fas fa-exclamation-triangle";
      return "fas fa-check-circle";
    },
    rowClass(item) {
      if (item.stock === 0) return "row-danger";
      if (item.stock < item.minStock) return "row-warning";
      return "";
    },
    stockNumClass(item) {
      if (item.stock === 0) return "num-danger";
      if (item.stock < item.minStock) return "num-warning";
      return "num-ok";
    },
    stockBarClass(item) {
      if (item.stock === 0) return "bar-danger";
      if (item.stock < item.minStock) return "bar-warning";
      return "bar-ok";
    },
    stockPercent(item) {
      const max = Math.max(item.minStock * 2, item.stock);
      return Math.min(100, Math.round((item.stock / max) * 100)) + "%";
    },
    formatPrice(p) {
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(p);
    },
    openAddModal() {
      this.modalMode = "add";
      this.form = { code: `VT-${String(this.nextId).padStart(3, "0")}`, name: "", category: "Điện", unit: "cái", stock: 0, minStock: 10, price: 0 };
      this.showModal = true;
    },
    openEditModal(item) {
      this.modalMode = "edit";
      this.form = { ...item };
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    saveItem() {
      if (!this.form.name || !this.form.code) return alert("Vui lòng nhập đầy đủ mã và tên vật tư!");
      if (this.modalMode === "add") {
        this.items.push({ ...this.form, id: this.nextId++ });
      } else {
        const idx = this.items.findIndex(i => i.id === this.form.id);
        if (idx !== -1) this.items[idx] = { ...this.form };
      }
      this.closeModal();
    },
    deleteItem(id) {
      if (confirm("Xác nhận xoá vật tư này?")) {
        this.items = this.items.filter(i => i.id !== id);
      }
    },
    openAdjustModal(item, type) {
      this.adjustItem = item;
      this.adjustType = type;
      this.adjustQty = 1;
      this.adjustNote = "";
      this.showAdjustModal = true;
    },
    confirmAdjust() {
      if (!this.adjustQty || this.adjustQty <= 0) return alert("Số lượng phải lớn hơn 0!");
      const idx = this.items.findIndex(i => i.id === this.adjustItem.id);
      if (this.adjustType === "in") {
        this.items[idx].stock += this.adjustQty;
      } else {
        if (this.adjustQty > this.items[idx].stock) return alert("Số lượng xuất vượt quá tồn kho!");
        this.items[idx].stock -= this.adjustQty;
      }
      this.transactions.unshift({
        id: Date.now(),
        time: new Date().toLocaleString("vi-VN"),
        type: this.adjustType,
        itemName: this.adjustItem.name,
        qty: this.adjustQty,
        by: "Quản Lý Tuấn",
        note: this.adjustNote || "—",
      });
      this.showAdjustModal = false;
    },
    exportCSV() {
      const header = ["Mã", "Tên vật tư", "Danh mục", "Đơn vị", "Tồn kho", "Mức cảnh báo", "Đơn giá", "Trạng thái"];
      const rows = this.items.map(i => [i.code, i.name, i.category, i.unit, i.stock, i.minStock, i.price, this.statusLabel(i)]);
      const csv = [header, ...rows].map(r => r.join(",")).join("\n");
      const blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8;" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url; a.download = "vat-tu.csv"; a.click();
    },
  },
};
</script>

<style scoped>
.page {
  padding: 20px 4px 40px;
  font-family: "Segoe UI", sans-serif;
  background: #f4f6fb;
  min-height: 100vh;
}

/* ===== HERO ===== */
.vt-hero {
  background: linear-gradient(135deg, #1d3557 0%, #2d5986 100%);
  border-radius: 18px;
  padding: 32px 36px;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  box-shadow: 0 8px 24px rgba(29,53,87,0.3);
}
.hero-left { display: flex; align-items: center; gap: 20px; }
.hero-icon {
  width: 60px; height: 60px;
  background: rgba(255,255,255,0.15);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px;
}
.vt-hero h1 { font-size: 24px; font-weight: 700; margin: 0 0 6px; color: #ffffff !important; }
.vt-hero p { margin: 0; opacity: 0.85; font-size: 14px; color: #ffffff !important; }
.hero-stats { display: flex; gap: 32px; }
.hstat { text-align: center; }
.hstat-num { display: block; font-size: 28px; font-weight: 800; }
.hstat-lbl { font-size: 12px; opacity: 0.8; }
.text-warning { color: #ffd166 !important; }
.text-danger { color: #ff6b6b !important; }
.text-success { color: #51cf66; }

/* ===== TOOLBAR ===== */
.toolbar {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 18px; gap: 12px; flex-wrap: wrap;
}
.toolbar-left { display: flex; gap: 12px; flex-wrap: wrap; }
.toolbar-right { display: flex; gap: 10px; }
.search-box {
  display: flex; align-items: center; gap: 8px;
  background: white; border: 1px solid #dde3f0;
  border-radius: 10px; padding: 8px 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.search-box i { color: #94a3b8; font-size: 14px; }
.search-box input {
  border: none; outline: none; font-size: 14px; width: 220px; color: #334155;
  background: transparent;
}
.filter-select {
  border: 1px solid #dde3f0; border-radius: 10px;
  padding: 8px 14px; font-size: 14px; color: #334155;
  background: white; cursor: pointer;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.btn-add, .btn-export {
  display: flex; align-items: center; gap: 8px;
  padding: 9px 18px; border-radius: 10px; border: none;
  font-size: 14px; font-weight: 600; cursor: pointer;
  transition: all 0.2s;
}
.btn-add { background: #2d5986; color: white; }
.btn-add:hover { background: #1d3557; }
.btn-export { background: #e8f0fe; color: #2d5986; }
.btn-export:hover { background: #c7d9f8; }

/* ===== TABLE ===== */
.table-card {
  background: white; border-radius: 14px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  overflow-x: auto; margin-bottom: 28px;
  -webkit-overflow-scrolling: touch;
}
table { width: 100%; border-collapse: collapse; min-width: 860px; }
theadr tr { background: #f1f5fb; }
thead tr { background: #f1f5fb; }
thead th {
  padding: 11px 10px; text-align: left;
  font-size: 11px; font-weight: 700;
  color: #64748b; text-transform: uppercase;
  letter-spacing: 0.5px; white-space: nowrap;
}
tbody tr { border-bottom: 1px solid #f1f5fb; transition: background 0.15s; }
tbody tr:hover { background: #f8faff; }
tbody td { padding: 10px 10px; font-size: 13px; color: #334155; vertical-align: middle; }

.td-idx { color: #94a3b8; font-size: 13px; width: 40px; }
.code-badge {
  background: #e8f0fe; color: #2d5986;
  padding: 3px 9px; border-radius: 6px;
  font-size: 12px; font-weight: 700; font-family: monospace;
}
.td-name { font-weight: 600; }
.cat-tag {
  background: #f0f4ff; color: #4a6fa5;
  padding: 3px 10px; border-radius: 20px;
  font-size: 12px; font-weight: 500;
}
.td-warn { color: #94a3b8; font-size: 13px; }
.stock-cell { display: flex; flex-direction: column; gap: 4px; min-width: 70px; }
.stock-num { font-weight: 700; font-size: 16px; }
.num-ok { color: #22c55e; }
.num-warning { color: #f59e0b; }
.num-danger { color: #ef4444; }
.stock-bar-bg { height: 5px; background: #e2e8f0; border-radius: 3px; }
.stock-bar-fill { height: 5px; border-radius: 3px; transition: width 0.4s; }
.bar-ok { background: #22c55e; }
.bar-warning { background: #f59e0b; }
.bar-danger { background: #ef4444; }

.row-warning { background: #fffbeb !important; }
.row-danger { background: #fff5f5 !important; }

.status-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 20px;
  font-size: 12px; font-weight: 600; white-space: nowrap;
}
.badge-success { background: #dcfce7; color: #16a34a; }
.badge-warning { background: #fef3c7; color: #d97706; }
.badge-danger { background: #fee2e2; color: #dc2626; }

.action-group { display: flex; gap: 4px; flex-wrap: nowrap; min-width: 132px; }
.btn-action {
  width: 28px; height: 28px; border: none; border-radius: 7px;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  font-size: 12px; transition: all 0.2s;
}
.btn-in { background: #dcfce7; color: #16a34a; }
.btn-in:hover { background: #16a34a; color: white; }
.btn-out { background: #fee2e2; color: #dc2626; }
.btn-out:hover { background: #dc2626; color: white; }
.btn-edit { background: #e8f0fe; color: #2563eb; }
.btn-edit:hover { background: #2563eb; color: white; }
.btn-del { background: #f3f4f6; color: #6b7280; }
.btn-del:hover { background: #6b7280; color: white; }

.empty-row { text-align: center; padding: 40px; color: #94a3b8; }
.empty-row i { font-size: 36px; margin-bottom: 8px; display: block; }
.empty-row p { margin: 0; font-size: 15px; }

/* ===== TRANSACTIONS ===== */
.section-title {
  font-size: 16px; font-weight: 700; color: #1d3557;
  margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
}
.td-time { color: #94a3b8; font-size: 13px; white-space: nowrap; }
.td-note { color: #64748b; font-size: 13px; }
.tx-type {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 10px; border-radius: 20px;
  font-size: 12px; font-weight: 600;
}
.tx-in { background: #dcfce7; color: #16a34a; }
.tx-out { background: #fee2e2; color: #dc2626; }

/* ===== MODAL ===== */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(15, 23, 42, 0.55);
  display: flex; align-items: center; justify-content: center;
  z-index: 9999; backdrop-filter: blur(3px);
}
.modal-box {
  background: white; border-radius: 18px;
  width: 600px; max-width: 95vw;
  box-shadow: 0 20px 60px rgba(0,0,0,0.25);
  overflow: hidden;
}
.modal-sm { width: 420px; }
.modal-header {
  padding: 20px 24px; background: #f8faff;
  border-bottom: 1px solid #e8edf5;
  display: flex; justify-content: space-between; align-items: center;
}
.modal-header h2 { font-size: 17px; font-weight: 700; color: #1d3557; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-close { background: none; border: none; font-size: 18px; cursor: pointer; color: #64748b; }
.modal-body { padding: 24px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 13px; font-weight: 600; color: #475569; }
.form-group input, .form-group select {
  border: 1px solid #dde3f0; border-radius: 9px;
  padding: 9px 13px; font-size: 14px; color: #334155;
  transition: border-color 0.2s;
}
.form-group input:focus, .form-group select:focus {
  outline: none; border-color: #2d5986;
  box-shadow: 0 0 0 3px rgba(45,89,134,0.1);
}
.modal-footer {
  padding: 16px 24px; border-top: 1px solid #e8edf5;
  display: flex; justify-content: flex-end; gap: 10px;
  background: #f8faff;
}
.btn-cancel {
  padding: 9px 20px; border-radius: 9px;
  border: 1px solid #dde3f0; background: white;
  color: #64748b; font-size: 14px; cursor: pointer;
}
.btn-save {
  padding: 9px 22px; border-radius: 9px; border: none;
  background: #2d5986; color: white;
  font-size: 14px; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; gap: 6px;
}
.btn-save:hover { background: #1d3557; }
</style>
