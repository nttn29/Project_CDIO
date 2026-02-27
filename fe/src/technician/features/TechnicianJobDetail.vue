<template>
  <div class="page">
    <header class="header">
      <div>
        <h1>Chi tiết công việc</h1>
        <p>Mã job: {{ job?.code || "Chưa có" }}</p>
      </div>
      <span class="tag" :class="statusClass(job?.status)">{{ statusLabel(job?.status) }}</span>
    </header>

    <section class="card">
      <h2>Thông tin công việc</h2>
      <div class="grid">
        <div>
          <p class="label">Tiêu đề</p>
          <p class="value">{{ job?.title || "Chưa cập nhật" }}</p>
        </div>
        <div>
          <p class="label">Địa điểm</p>
          <p class="value">{{ job?.location || "Chưa cập nhật" }}</p>
        </div>
        <div>
          <p class="label">Lịch hẹn</p>
          <p class="value">{{ formatDate(job?.scheduled_at) }}</p>
        </div>
        <div>
          <p class="label">Hạn chót</p>
          <p class="value">{{ formatDate(job?.due_at) }}</p>
        </div>
      </div>
    </section>

    <section class="card">
      <h2>Mô tả</h2>
      <p class="value">{{ job?.description || "Chưa có mô tả." }}</p>
    </section>

    <section class="actions">
      <button v-if="job?.status === 'moi'" class="primary" @click="updateStatus('dang_xu_ly')">
        Đã tiếp nhận yêu cầu
      </button>
      <button v-if="job?.status === 'dang_xu_ly'" class="primary" @click="updateStatus('hoan_thanh')">
        Hoàn thành
      </button>
      <button v-if="job?.status === 'dang_xu_ly'" class="ghost" @click="updateStatus('moi')">Tạm dừng</button>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000";
const route = useRoute();
const job = ref(null);

async function fetchJob() {
  const code = route.query.code;
  if (!code) return;
  try {
    const res = await fetch(`${API_BASE}/api/technician/jobs/code/${code}`);
    if (!res.ok) throw new Error("Not found");
    job.value = await res.json();
  } catch (err) {
    job.value = { code };
  }
}

async function updateStatus(status) {
  if (!job.value?.id) return;
  await fetch(`${API_BASE}/api/technician/jobs/${job.value.id}`, {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ status }),
  });
  await fetchJob();
}

function statusLabel(status) {
  if (status === "moi") return "Chờ tiếp nhận";
  if (status === "dang_xu_ly") return "Đang xử lý";
  if (status === "hoan_thanh") return "Hoàn thành";
  return "Hủy";
}

function statusClass(status) {
  if (status === "moi") return "tag-new";
  if (status === "dang_xu_ly") return "tag-doing";
  if (status === "hoan_thanh") return "tag-done";
  return "tag-cancel";
}

function formatDate(value) {
  if (!value) return "Chưa cập nhật";
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return value;
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const yyyy = d.getFullYear();
  const hh = String(d.getHours()).padStart(2, "0");
  const min = String(d.getMinutes()).padStart(2, "0");
  return `${hh}:${min} ${dd}/${mm}/${yyyy}`;
}

onMounted(fetchJob);
</script>

<style scoped>
.page {
  display: grid;
  gap: 16px;
  color: #0f172a;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.header h1 {
  margin: 0;
  font-size: 22px;
}

.header p {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 13px;
}

.tag {
  padding: 6px 12px;
  border-radius: 999px;
  background: #fde68a;
  color: #b45309;
  font-size: 12px;
  font-weight: 600;
}

.tag-new {
  background: #dbeafe;
  color: #1d4ed8;
}

.tag-doing {
  background: #fde68a;
  color: #b45309;
}

.tag-done {
  background: #dcfce7;
  color: #15803d;
}

.tag-cancel {
  background: #fee2e2;
  color: #b91c1c;
}

.card {
  background: #fff;
  border-radius: 14px;
  padding: 18px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.card h2 {
  margin: 0 0 10px;
  font-size: 16px;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
}

.label {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}

.value {
  margin: 4px 0 0;
  font-size: 14px;
}

.images {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 12px;
}

.image-placeholder {
  border: 1px dashed #cbd5f5;
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  color: #94a3b8;
  font-size: 13px;
}

.actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.primary {
  border: none;
  background: #2563eb;
  color: #fff;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 12px;
  cursor: pointer;
}

.ghost {
  border: 1px solid #e2e8f0;
  background: #fff;
  padding: 10px 14px;
  border-radius: 10px;
  font-size: 12px;
  cursor: pointer;
}
</style>
