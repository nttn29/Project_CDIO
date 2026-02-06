<template>
  <div class="page">
    <div class="container">
      <!-- HEADER -->
      <div class="header">
        <h1>Quản lý chủ căn hộ</h1>

        <div class="actions">
          <input
            v-model="keyword"
            type="text"
            placeholder="Tìm theo tên, CCCD, số nhà..."
          />
          <button class="btn add" @click="openAdd">+ Thêm mới</button>
        </div>
      </div>

      <!-- TABLE -->
      <table class="table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Họ tên</th>
            <th>CCCD</th>
            <th>SĐT</th>
            <th>Địa chỉ thường trú</th>
            <th>Số nhà</th>
            <th>Ngày đăng ký</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(item, index) in filteredOwners" :key="item.cccd">
            <td>{{ index + 1 }}</td>
            <td>{{ item.fullName }}</td>
            <td>{{ item.cccd }}</td>
            <td>{{ item.phone }}</td>
            <td>{{ item.address }}</td>
            <td>{{ item.roomCode }}</td>
            <td>{{ item.registerDate }}</td>
            <td class="actions-col">
              <button class="btn edit" @click="openEdit(item)">Sửa</button>
              <button class="btn delete" @click="remove(item)">Xoá</button>
            </td>
          </tr>

          <tr v-if="filteredOwners.length === 0">
            <td colspan="8" class="empty">Không có dữ liệu</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL -->
    <div v-if="showModal" class="modal">
      <div class="modal-box">
        <h2>{{ isEdit ? "Sửa chủ căn hộ" : "Thêm chủ căn hộ" }}</h2>

        <div class="form">
          <input v-model="form.fullName" placeholder="Họ tên" />
          <input v-model="form.cccd" placeholder="CCCD" />
          <input v-model="form.phone" placeholder="Số điện thoại" />
          <input v-model="form.address" placeholder="Địa chỉ thường trú" />
          <input v-model="form.roomCode" placeholder="Số nhà (VD: A1001)" />
          <input v-model="form.registerDate" type="date" />
        </div>

        <div class="modal-actions">
          <button class="btn cancel" @click="close">Huỷ</button>
          <button class="btn save" @click="save">
            {{ isEdit ? "Cập nhật" : "Thêm" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DanhSachNha",

  data() {
    return {
      keyword: "",
      showModal: false,
      isEdit: false,

      owners: [
        {
          fullName: "Nguyễn Văn A",
          cccd: "079203001234",
          phone: "0901234567",
          address: "Hải Châu, Đà Nẵng",
          roomCode: "A1001",
          registerDate: "2025-01-12",
        },
        {
          fullName: "Trần Thị B",
          cccd: "049198009876",
          phone: "0912345678",
          address: "Liên Chiểu, Đà Nẵng",
          roomCode: "A802",
          registerDate: "2025-01-20",
        },
      ],

      form: {
        fullName: "",
        cccd: "",
        phone: "",
        address: "",
        roomCode: "",
        registerDate: "",
      },
    };
  },

  computed: {
    filteredOwners() {
      const key = this.keyword.toLowerCase();
      return this.owners.filter(
        (o) =>
          o.fullName.toLowerCase().includes(key) ||
          o.cccd.includes(key) ||
          o.roomCode.toLowerCase().includes(key),
      );
    },
  },

  methods: {
    openAdd() {
      this.isEdit = false;
      this.form = {
        fullName: "",
        cccd: "",
        phone: "",
        address: "",
        roomCode: "",
        registerDate: "",
      };
      this.showModal = true;
    },

    openEdit(item) {
      this.isEdit = true;
      this.form = { ...item };
      this.showModal = true;
    },

    save() {
      if (this.isEdit) {
        const index = this.owners.findIndex((o) => o.cccd === this.form.cccd);
        if (index !== -1) this.owners[index] = { ...this.form };
      } else {
        this.owners.push({ ...this.form });
      }
      this.close();
    },

    remove(item) {
      if (confirm("Bạn chắc chắn muốn xoá?")) {
        this.owners = this.owners.filter((o) => o.cccd !== item.cccd);
      }
    },

    close() {
      this.showModal = false;
    },
  },
};
</script>

<style scoped>
.page {
  min-height: 100vh;
  background: #f4f6fb;
  padding: 40px;
  font-family: "Segoe UI", sans-serif;
}

.container {
  background: #fff;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.1);
}

/* HEADER */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.actions {
  display: flex;
  gap: 12px;
}

.actions input {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  text-align: center;
}

.table th {
  background: #f1f3f9;
}

.empty {
  text-align: center;
  color: #888;
  padding: 20px;
}

/* BUTTON */
.btn {
  padding: 6px 12px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.btn.add {
  background: #2ecc71;
  color: #fff;
}

.btn.edit {
  background: #3498db;
  color: #fff;
}
.actions-col {
  display: flex;
  justify-content: center;
  gap: 8px; /* khoảng cách giữa nút */
}

.btn.delete {
  background: #e74c3c;
  color: #fff;
}

.btn.save {
  background: #2ecc71;
  color: #fff;
}

.btn.cancel {
  background: #95a5a6;
  color: #fff;
}

/* MODAL */
.modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-box {
  background: #fff;
  width: 420px;
  padding: 24px;
  border-radius: 14px;
}

.form input {
  width: 100%;
  margin-bottom: 10px;
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
</style>
