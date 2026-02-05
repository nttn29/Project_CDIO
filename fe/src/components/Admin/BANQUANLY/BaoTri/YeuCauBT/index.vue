<template>
  <div class="page">
    <div class="container">
      <!-- HEADER -->
      <div class="header">
        <h1>Yêu cầu bảo trì</h1>
      </div>

      <!-- TABLE -->
      <table class="table">
        <thead>
          <tr>
            <th>STT</th>
            <th>Chủ hộ</th>
            <th>SĐT</th>
            <th>Số nhà</th>
            <th>Nội dung yêu cầu</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(req, index) in requests" :key="req.id">
            <td>{{ index + 1 }}</td>
            <td>{{ req.owner }}</td>
            <td>{{ req.phone }}</td>
            <td>{{ req.roomCode }}</td>
            <td class="content">{{ req.content }}</td>

            <td>
              <span class="status" :class="req.status">
                {{ statusText(req.status) }}
              </span>
            </td>

            <td class="actions-col">
              <button
                class="btn approve"
                :class="{ hidden: req.status !== 'pending' }"
                @click="openModal(req)"
              >
                Xử lý
              </button>
            </td>
          </tr>

          <tr v-if="requests.length === 0">
            <td colspan="7" class="empty">Không có yêu cầu</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL XỬ LÝ -->
    <div v-if="showModal" class="modal">
      <div class="modal-box">
        <h2>Xử lý yêu cầu</h2>

        <div class="info">
          <p><b>Chủ hộ:</b> {{ selected.owner }}</p>
          <p><b>Số nhà:</b> {{ selected.roomCode }}</p>
          <p><b>Nội dung:</b> {{ selected.content }}</p>
        </div>

        <div class="form">
          <label>Phản hồi</label>
          <select v-model="form.status">
            <option value="approved">Chấp thuận</option>
            <option value="rejected">Từ chối</option>
          </select>

          <label>Ngày bảo trì</label>
          <input type="date" v-model="form.date" />

          <label>Phân công nhân viên</label>
          <input type="text" v-model="form.staff" placeholder="Tên nhân viên" />
        </div>

        <div class="modal-actions">
          <button class="btn cancel" @click="closeModal">Huỷ</button>
          <button class="btn save" @click="submit">Xác nhận</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "YeuCauBaoTri",

  data() {
    return {
      showModal: false,
      selected: {},

      requests: [
        {
          id: 1,
          owner: "Nguyễn Văn A",
          phone: "0901234567",
          roomCode: "A1001",
          content: "Máy lạnh không lạnh",
          status: "pending",
        },
        {
          id: 2,
          owner: "Trần Thị B",
          phone: "0912345678",
          roomCode: "A802",
          content: "Rò rỉ nước nhà vệ sinh",
          status: "approved",
        },
      ],

      form: {
        status: "approved",
        date: "",
        staff: "",
      },
    };
  },

  methods: {
    openModal(req) {
      this.selected = req;
      this.form = {
        status: "approved",
        date: "",
        staff: "",
      };
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
    },

    submit() {
      this.selected.status = this.form.status;

      // DEMO: lưu thêm thông tin xử lý
      this.selected.scheduleDate = this.form.date;
      this.selected.staff = this.form.staff;

      this.closeModal();
    },

    statusText(status) {
      return {
        pending: "Chờ xử lý",
        approved: "Đã chấp thuận",
        rejected: "Đã từ chối",
      }[status];
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
  margin-bottom: 20px;
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

.content {
  text-align: left;
}

.empty {
  text-align: center;
  color: #888;
  padding: 20px;
}

/* STATUS */
.status {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status.pending {
  background: #f1c40f;
}

.status.approved {
  background: #2ecc71;
  color: #fff;
}

.status.rejected {
  background: #e74c3c;
  color: #fff;
}

/* BUTTON */
.actions-col {
  display: flex;
  justify-content: center;
}

.btn {
  padding: 6px 12px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.btn.approve {
  background: #3498db;
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
  width: 460px;
  padding: 24px;
  border-radius: 14px;
}

.info p {
  margin: 4px 0;
}

.form {
  margin-top: 12px;
}

.form label {
  font-size: 13px;
  font-weight: 600;
  display: block;
  margin-top: 10px;
}

.form input,
.form select {
  width: 100%;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-top: 4px;
}

.modal-actions {
  margin-top: 18px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.hidden {
  visibility: hidden; /* chiếm chỗ nhưng không hiện */
}
</style>
