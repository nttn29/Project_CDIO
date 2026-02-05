<template>
  <div class="page">
    <h2>Phân công kỹ thuật viên</h2>

    <table class="table">
      <thead>
        <tr>
          <th>Tên kỹ thuật viên</th>
          <th>Số điện thoại</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="tech in technicians" :key="tech.id">
          <td>{{ tech.name }}</td>
          <td>{{ tech.phone }}</td>
          <td>
            <span class="status" :class="tech.busy ? 'busy' : 'free'">
              {{ tech.busy ? "Đang nhận việc" : "Đang rảnh" }}
            </span>
          </td>
          <td class="actions-col">
            <button class="btn-view" @click="openJobs(tech)">
              Xem công việc
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <Teleport to="body">
    <div
      v-if="showJobs"
      class="pc-modal-overlay"
      @click.self="closeJobs"
      role="dialog"
      aria-modal="true"
    >
      <div class="pc-modal" v-if="selectedTech" @click.stop> 
        <h3>Công việc của {{ selectedTech.name }}</h3>

        <table class="table">
          <thead>
            <tr>
              <th>Số nhà</th>
              <th>Khách hàng</th>
              <th>Nội dung</th>
              <th>Lịch hẹn</th>
              <th></th>
            </tr>
          </thead>

          <tbody>
            <tr v-if="!selectedTech.jobs || selectedTech.jobs.length === 0">
              <td colspan="5" class="empty">
                Kỹ thuật viên này chưa có công việc
              </td>
            </tr>

            <tr v-for="job in selectedTech.jobs" :key="job.id">
              <td>{{ job.house }}</td>
              <td>{{ job.customer }}</td>
              <td>{{ job.content }}</td>
              <td>{{ job.date }}</td>
              <td>
                <button class="btn-view-job" @click="openJobDetail(job)">
                  Xem
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="modal-actions">
          <button class="btn-close" @click="closeJobs">Đóng</button>
        </div>
      </div>
    </div>

    <div
      v-if="showJobDetail"
      class="pc-modal-overlay detail-overlay"
      @click.self="closeJobDetail"
      role="dialog"
      aria-modal="true"
    >
      <div class="pc-modal pc-detail-modal" v-if="selectedJob" @click.stop> 
        <h3>Chi tiết công việc</h3>

        <div class="job-detail">
          <p><strong>Số nhà:</strong> {{ selectedJob.house }}</p>
          <p><strong>Khách hàng:</strong> {{ selectedJob.customer }}</p>
          <p><strong>Nội dung:</strong> {{ selectedJob.content }}</p>
          <p><strong>Thời gian:</strong> {{ selectedJob.date }}</p>
        </div>

        <div class="modal-actions">
          <button class="btn-close" @click="closeJobDetail">Đóng</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

/* ===== STATE ===== */
const showJobs = ref(false);
const showJobDetail = ref(false);
const selectedTech = ref({ name: "", jobs: [] });
const selectedJob = ref(null);

/* ===== MOCK DATA ===== */
const technicians = ref([
  {
    id: 1,
    name: "Nguyễn Văn A",
    phone: "0901 111 222",
    busy: true,
    jobs: [
      {
        id: 1,
        house: "A1001",
        customer: "Trần Thị B",
        content: "Sửa điện phòng 203",
        date: "10/02/2026 - 09:00",
      },
    ],
  },
  {
    id: 2,
    name: "Lê Văn C",
    phone: "0902 333 444",
    busy: false ,
    jobs: [],
  },
]);

/* ===== METHODS ===== */
function openJobs(tech) {
  selectedTech.value = tech;
  selectedJob.value = null;
  showJobDetail.value = false;
  showJobs.value = true;
}

function closeJobs() {
  showJobs.value = false;
  selectedJob.value = null;
  showJobDetail.value = false;
}

function openJobDetail(job) {
  selectedJob.value = job;
  showJobDetail.value = true;
}

function closeJobDetail() {
  showJobDetail.value = false;
  selectedJob.value = null;
}

function handleKeyDown(e) {
  if (e.key === "Escape") {
    if (showJobDetail.value) closeJobDetail();
    else if (showJobs.value) closeJobs();
  }
}

onMounted(() => window.addEventListener("keydown", handleKeyDown));
onBeforeUnmount(() => window.removeEventListener("keydown", handleKeyDown));
</script>

<style>
/* CSS GLOBAL - Dành cho các thành phần Teleport ra ngoài body */
.pc-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999999;
}

/* Tăng z-index cho modal chi tiết để đè lên modal danh sách nếu cần */
.detail-overlay {
  z-index: 1000000;
}

.pc-modal {
  background: #ffffff;
  width: 820px;
  max-width: 90%;
  padding: 24px 26px;
  border-radius: 16px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
}

.pc-detail-modal {
  width: 520px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn-close {
  background: #e0e0e0;
  color: #333;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: #d5d5d5;
}

.btn-view-job {
  padding: 4px 12px;
  background: #1abc9c;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.job-detail p {
  margin: 12px 0;
  line-height: 1.5;
}

.empty {
  color: #999;
  font-style: italic;
  padding: 20px !important;
}

/* Các style dùng chung cho table trong và ngoài modal */
.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

.table th,
.table td {
  border: 1px solid #eee;
  padding: 12px 10px;
  text-align: center;
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
}
</style>

<style scoped>
/* CSS SCOPED - Chỉ dành cho trang hiện tại */
.page {
  padding: 20px;
  font-family: sans-serif;
}

.status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
}

.status.free {
  background: #e7f7ee;
  color: #2e8b57;
}

.status.busy {
  background: #ffeaea;
  color: #d9534f;
}

.actions-col {
  display: flex;
  justify-content: center;
}

.btn-view {
  padding: 7px 16px;
  background: #3498db;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-view:hover {
  background: #2980b9;
}
</style>