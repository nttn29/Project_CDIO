<template>
  <div class="page">
    <div class="container">
      <!-- HEADER -->
      <div class="header">
        <h5>Quản lý chủ căn hộ</h5>

        <div class="actions">
          <input v-model="keyword" type="text" placeholder="Tìm theo tên, CCCD, số nhà..." />
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
          <tr v-for="(item, index) in filteredOwners" :key="item.id_nguoi_dung">
            <td>{{ index + 1 }}</td>
            <td>{{ item.ten || item.fullName }}</td>
            <td>{{ item.cccd || 'Không có' }}</td>
            <td>{{ item.dien_thoai || item.phone }}</td>
            <td>{{ item.address || 'Không có' }}</td>
            <td>{{ item.so_can_ho || item.roomCode || 'Không có' }}</td>
            <td>{{ item.created_at ? new Date(item.created_at).toLocaleDateString() : item.registerDate }}</td>
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
          <input v-model="form.email" placeholder="Email (dùng để đăng nhập)" />
          <input v-model="form.phone" placeholder="Số điện thoại" />
          <input v-model="form.password" type="password" placeholder="Mật khẩu" v-if="!isEdit" />
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
import api from '@/services/api';

export default {
  name: "DanhSachNha",

  data() {
    return {
      keyword: "",
      showModal: false,
      isEdit: false,

      owners: [],

      form: {
        id_nguoi_dung: null,
        fullName: "",
        email: "",
        phone: "",
        password: "",
      },
    };
  },

  mounted() {
    this.fetchUsers();
  },

  computed: {
    filteredOwners() {
      const key = this.keyword.toLowerCase();
      return this.owners.filter(
        (o) =>
          (o.ten || o.fullName || "").toLowerCase().includes(key) ||
          (o.cccd || "").includes(key) ||
          (o.so_can_ho || o.roomCode || "").toLowerCase().includes(key),
      );
    },
  },

  methods: {
    async fetchUsers() {
      try {
        const res = await api.get('/users');
        this.owners = res.data.filter(u => u.vai_tro !== 'admin' && u.vai_tro !== 'technician');
      } catch (err) {
        console.error("Lỗi lấy danh sách cư dân:", err);
      }
    },

    openAdd() {
      this.isEdit = false;
      this.form = {
        id_nguoi_dung: null,
        fullName: "",
        email: "",
        phone: "",
        password: "",
      };
      this.showModal = true;
    },

    openEdit(item) {
      this.isEdit = true;
      this.form = { 
        id_nguoi_dung: item.id_nguoi_dung,
        fullName: item.ten || item.fullName,
        email: item.email || "",
        phone: item.dien_thoai || item.phone,
        password: "",
      };
      this.showModal = true;
    },

    async save() {
      try {
        if (this.isEdit) {
            // Note: API users/{id} update endpoint is /users/{id} but api.php does not route PUT /users/{id} to NguoiDungController@update explicitly for admin
            // Wait, api.php doesn't have PUT users/{id} inside ADMIN ROUTES. But it has it under RESIDENT ROUTES: `Route::put('users/{id}', ...)`
            await api.put(`/users/${this.form.id_nguoi_dung}`, {
                ten: this.form.fullName,
                email: this.form.email,
                dien_thoai: this.form.phone,
            });
        } else {
            await api.post('/users', {
                ten: this.form.fullName,
                email: this.form.email,
                dien_thoai: this.form.phone,
                mat_khau: this.form.password,
                vai_tro: 'resident',
                trang_thai: 'active'
            });
        }
        await this.fetchUsers();
        this.close();
      } catch (err) {
        alert("Có lỗi xảy ra khi lưu: " + (err?.response?.data?.message || err.message));
      }
    },

    async remove(item) {
      if (confirm("Bạn chắc chắn muốn xoá cư dân này?")) {
        try {
          await api.delete(`/users/${item.id_nguoi_dung}`);
          await this.fetchUsers();
        } catch (err) {
          alert("Không thể xóa cư dân.");
        }
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
