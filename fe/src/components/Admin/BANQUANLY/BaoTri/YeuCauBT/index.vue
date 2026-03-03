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
            <th style="width: 50px;">STT</th>
            <th>Chủ hộ</th>
            <th>SĐT</th>
            <th style="width: 80px;">Số nhà</th>
            <th>Nội dung yêu cầu</th>
            <th style="width: 120px;">Trạng thái</th>
            <th style="width: 150px;">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(req, index) in requests" :key="req.id_yeu_cau">
            <td>{{ index + 1 }}</td>
            <td>{{ req.chu_ho ? req.chu_ho.ten : 'Chưa có' }}</td>
            <td>{{ req.chu_ho ? req.chu_ho.dien_thoai : 'Chưa có' }}</td>
            <td>{{ req.can_ho ? req.can_ho.so_can_ho : 'Chưa có' }}</td>
            <td class="content">{{ req.mo_ta }}</td>

            <td>
              <span class="status" :class="req.trang_thai">
                {{ statusText(req.trang_thai) }}
              </span>
            </td>

            <td class="actions-col">
              <div class="btn-group">
                <button
                  v-if="canApprove(req.trang_thai)"
                  class="btn approve"
                  @click="approveRequest(req)"
                >
                  Duyệt
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="requests.length === 0 && !loading">
            <td colspan="7" class="empty">Không có yêu cầu</td>
          </tr>
          <tr v-if="loading">
            <td colspan="7" class="empty">Đang tải...</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL XỬ LÝ -->
    <Teleport to="body">
    <div v-if="showModal" class="modal">
      <div class="modal-box">
        <h2>Xử lý yêu cầu</h2>

        <div class="info">
          <p><b>Chủ hộ:</b> {{ selected.chu_ho ? selected.chu_ho.ten : 'Chưa có' }}</p>
          <p><b>Số nhà:</b> {{ selected.can_ho ? selected.can_ho.so_can_ho : 'Chưa có' }}</p>
          <p><b>Nội dung:</b> {{ selected.mo_ta }}</p>
        </div>

        <div class="form">
          <label>Phản hồi phương thức xử lý</label>
          <select v-model="form.status">
            <option value="dang_xu_ly">Chấp thuận (Đang xử lý)</option>
            <option value="tu_choi">Từ chối</option>
          </select>

          <!-- 
          The backend API would require pushing assignment details to the phan_cong table.
          For now, tracking visually.
          -->
          <label v-if="form.status === 'dang_xu_ly'">Ngày bảo trì</label>
          <input v-if="form.status === 'dang_xu_ly'" type="date" v-model="form.date" />

          <label v-if="form.status === 'dang_xu_ly'">Phân công nhân viên</label>
          <select v-if="form.status === 'dang_xu_ly'" v-model="form.staff">
            <option value="" disabled>-- Chọn kỹ thuật viên --</option>
            <option v-for="tech in technicians" :key="tech.id_nguoi_dung" :value="tech.id_nguoi_dung">
              {{ tech.ten || tech.name }}
            </option>
          </select>
        </div>

        <div class="modal-actions">
          <button class="btn cancel" @click="closeModal">Huỷ</button>
          <button class="btn save" @click="submit">Xác nhận</button>
        </div>
      </div>
    </div>
    </Teleport>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: "YeuCauBaoTri",

  data() {
    return {
      loading: true,
      requests: [],
    };
  },

  mounted() {
    this.fetchRequests();
  },

  methods: {
    async fetchRequests() {
      try {
        this.loading = true;
        // Use public resident-compatible endpoint.
        // `/yeu_cau` is currently protected by auth:sanctum in backend route order.
        const res = await api.get('/yeu-cau-bao-tri');
        let data = res.data;
        const roomFilter = this.$route.query.room;
        if (roomFilter) {
          data = data.filter(req => req.can_ho && req.can_ho.so_can_ho === roomFilter);
        }
        this.requests = data;
      } catch (error) {
        // Backward fallback for deployments that only expose `/yeu_cau`.
        try {
          const fallback = await api.get('/yeu_cau');
          let fallbackData = fallback.data;
          const roomFilter = this.$route.query.room;
          if (roomFilter) {
            fallbackData = fallbackData.filter(req => req.can_ho && req.can_ho.so_can_ho === roomFilter);
          }
          this.requests = fallbackData;
        } catch (fallbackError) {
          console.error("Lỗi khi lấy yêu cầu bảo trì:", fallbackError);
        }
      } finally {
        this.loading = false;
      }
    },

    canApprove(status) {
      return ['moi', 'cho_xu_ly', 'pending'].includes(status);
    },

    async approveRequest(req) {
      try {
        await api.post(`/yeu-cau-bao-tri/${req.id_yeu_cau}/status`, {
          status: 'da_xac_nhan'
        });
        req.trang_thai = 'da_xac_nhan';
        this.$router.push({ path: '/admin/bao-tri/phan-cong', query: { highlight: req.id_yeu_cau } });
      } catch (error) {
        console.error("Lỗi duyệt yêu cầu:", error);
        alert('Duyệt yêu cầu không thành công');
      }
    },

    statusText(status) {
      const map = {
        moi: "Mới",
        cho_xu_ly: "Chờ xử lý",
        pending: "Chờ xử lý",
        da_xac_nhan: "Đã duyệt",
        dang_xu_ly: "Đang xử lý",
        approved: "Đã chấp thuận",
        hoan_thanh: "Hoàn thành",
        tu_choi: "Đã từ chối",
        rejected: "Đã từ chối",
      };
      return map[status] || status;
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
  table-layout: fixed; /* consistent column widths and avoid weird wrapping */
}

.table th,
.table td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  text-align: center;
  /* do not allow cells to wrap their contents */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
  /* keep the text on one line so the badge doesn't break */
  white-space: nowrap;
  display: inline-block;
}

.status.cho_xu_ly,
.status.pending {
  background: #f1c40f;
  color: #333;
}
.status.moi,
.status.da_xac_nhan {
  background: #e0f2fe;
  color: #0369a1;
}

.status.dang_xu_ly,
.status.approved {
  background: #3498db;
  color: #fff;
}

.status.hoan_thanh {
  background: #2ecc71;
  color: #fff;
}

.status.tu_choi,
.status.rejected {
  background: #e74c3c;
  color: #fff;
}

/* BUTTON */
.actions-col {
  overflow: visible !important;
  text-overflow: clip !important;
}

.btn-group {
  display: flex;
  justify-content: center;
  gap: 8px;
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
.btn.process {
  background: #10b981;
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
  align-items: flex-start;
  padding-top: 60px;
  overflow-y: auto;
  z-index: 9999;
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
</style>
