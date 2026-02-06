<template>
  <div class="invoice-container">
    <div class="no-print actions-top">
      <button class="btn-back" @click="$emit('back')">← Quay lại</button>
      <button class="btn-print" @click="printInvoice">In hóa đơn ngay</button>
    </div>

    <section class="invoice-paper printable-area" id="invoice-section">
      <div class="invoice-header">
        <div class="brand">
          <h1>HÓA ĐƠN DỊCH VỤ</h1>
          <p class="invoice-no">Số: #{{ invoiceData.id }}</p>
        </div>
        <div class="meta">
          <p>Ngày lập: {{ invoiceData.createdAt }}</p>
        </div>
      </div>

      <div class="address-grid">
        <div class="col">
          <h3 class="label">KHÁCH HÀNG</h3>
          <p class="value">
            <strong>{{ invoiceData.customerName }}</strong>
          </p>
          <p class="sub-value">SĐT: {{ invoiceData.phone }}</p>
          <p class="sub-value">Địa chỉ: {{ invoiceData.houseNumber }}</p>
        </div>
        <div class="col">
          <h3 class="label">KỸ THUẬT VIÊN</h3>
          <p class="value">
            <strong>{{ invoiceData.technicianName }}</strong>
          </p>
          <p class="sub-value">Đơn vị: Đội kỹ thuật tòa nhà</p>
        </div>
      </div>

      <div class="task-detail">
        <h3 class="label">NỘI DUNG YÊU CẦU</h3>
        <div class="task-box">
          {{ invoiceData.maintenanceContent }}
        </div>
      </div>

      <table class="cost-table">
        <thead>
          <tr>
            <th>Mô tả chi tiết</th>
            <th class="text-right">Chi phí (VNĐ)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Phí bảo trì và thay thế vật tư (trọn gói)</td>
            <td class="text-right">{{ formatCurrency(invoiceData.cost) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td>TỔNG CỘNG</td>
            <td class="text-right total-price">
              {{ formatCurrency(invoiceData.cost) }}
            </td>
          </tr>
        </tfoot>
      </table>

      <div class="signature-area">
        <div class="sig-block">
          <p>Khách hàng</p>
          <span class="sig-guide">(Ký và ghi rõ họ tên)</span>
        </div>
        <div class="sig-block">
          <p>Kỹ thuật viên</p>
          <span class="sig-guide">(Ký và ghi rõ họ tên)</span>
          <div class="sig-gap"></div>
          <p class="sig-name">{{ invoiceData.technicianName }}</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from "vue";

const invoiceData = ref({
  id: "2026-0001",
  createdAt: "04/02/2026",
  customerName: "Trần Thị B",
  phone: "0905 123 456",
  houseNumber: "A1001 - Chung cư Sunrise",
  maintenanceContent:
    "Xử lý sự cố rò rỉ nước tại vòi sen nhà tắm và thay mới dây cấp nước bồn rửa mặt.",
  technicianName: "Nguyễn Văn A",
  cost: 450000,
});

const formatCurrency = (val) => new Intl.NumberFormat("vi-VN").format(val);

function printInvoice() {
  window.print();
}
</script>

<style scoped>
/* GIAO DIỆN HIỂN THỊ TRÊN WEB (Dashboard) */
.invoice-container {
  background-color: #525659;
  min-height: 100vh;
  padding: 40px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.actions-top {
  margin-bottom: 20px;
  width: 210mm;
  display: flex;
  justify-content: space-between;
}

.invoice-paper {
  background: white;
  width: 210mm;
  min-height: 297mm;
  padding: 20mm;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  box-sizing: border-box;
}

.invoice-header {
  display: flex;
  justify-content: space-between;
  border-bottom: 2px solid #333;
  padding-bottom: 15px;
  margin-bottom: 30px;
}

.invoice-header h1 {
  margin: 0;
  font-size: 24px;
  color: #1a1a1a;
}
.invoice-no {
  color: #d32f2f;
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
  color: #666;
  border-bottom: 1px solid #eee;
  padding-bottom: 5px;
  margin-bottom: 10px;
}
.sub-value {
  margin: 4px 0;
  font-size: 14px;
  color: #444;
}

.task-box {
  border: 1px dashed #ccc;
  padding: 15px;
  margin-bottom: 30px;
  line-height: 1.6;
}

.cost-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 50px;
}
.cost-table th {
  background: #f2f2f2;
  border: 1px solid #ddd;
  padding: 12px;
}
.cost-table td {
  border: 1px solid #ddd;
  padding: 12px;
}
.total-price {
  font-size: 20px;
  font-weight: bold;
  color: #000;
}
.text-right {
  text-align: right;
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
  color: #888;
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
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}
.btn-back {
  background: #ecf0f1;
  border: 1px solid #bdc3c7;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

/* ============================================================
   PHẦN QUAN TRỌNG: CSS KHI IN (FIX TRIỆT ĐỂ SIDEBAR)
   ============================================================ */
@media print {
  /* 1. Ẩn mọi thứ từ root của ứng dụng */
  body * {
    visibility: hidden !important;
  }

  /* 2. Ép buộc vùng hóa đơn hiển thị và ghi đè vị trí */
  .printable-area,
  .printable-area * {
    visibility: visible !important;
  }

  .printable-area {
    position: fixed !important; /* Dùng fixed để thoát khỏi luồng layout của sidebar */
    top: 0 !important;
    left: 0 !important;
    width: 210mm !important; /* Fix cứng chiều ngang A4 */
    height: 297mm !important;
    margin: 0 !important;
    padding: 15mm !important; /* Tạo lề trống để in đẹp */
    background: white !important;
    box-shadow: none !important;
    z-index: 99999999 !important; /* Luôn nằm trên cùng */
  }

  /* 3. Loại bỏ lề do Layout/Sidebar của Dashboard tạo ra */
  html,
  body {
    margin: 0 !important;
    padding: 0 !important;
    background: white !important;
    width: 210mm;
    height: 297mm;
  }

  /* Ẩn các icon/sidebar cụ thể nếu chúng vẫn lọt vào */
  .no-print,
  .sidebar,
  .header,
  nav,
  aside {
    display: none !important;
  }

  @page {
    size: A4;
    margin: 0; /* Để lề được quản lý bởi padding của .printable-area */
  }
}
</style>
