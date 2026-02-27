<template>
  <div class="page">
    <header class="header">
      <div>
        <h1>Tiếp nhận yêu cầu từ Admin</h1>
        <p>Danh sách các yêu cầu đã phân công cho bạn.</p>
      </div>
      <div class="filters">
        <select v-model="statusFilter">
          <option value="all">Tất cả trạng thái</option>
          <option value="moi">Chờ tiếp nhận</option>
          <option value="dang_xu_ly">Đang xử lý</option>
          <option value="hoan_thanh">Hoàn thành</option>
          <option value="huy">Hủy</option>
        </select>
        <input v-model.trim="searchTerm" type="text" placeholder="Tìm theo mã/địa điểm/mô tả" />
      </div>
    </header>

    <div v-if="error" class="error">{{ error }}</div>

    <section class="list">
      <article v-if="loading" class="job-card">Đang tải dữ liệu...</article>
      <article v-else-if="displayJobs.length === 0" class="job-card">Chưa có yêu cầu nào được phân công.</article>

      <article v-for="job in displayJobs" :key="job.id || job.code" class="job-card">
        <div class="job-top">
          <div>
            <h3>{{ job.code || "PC-?" }}</h3>
            <p>{{ job.title || "Yêu cầu bảo trì" }}</p>
          </div>
          <span class="tag" :class="statusClass(job.status)">{{ statusLabel(job.status) }}</span>
        </div>
        <p class="meta">Địa điểm: {{ job.location || "Chưa cập nhật" }}</p>
        <p class="meta">Lịch hẹn: {{ formatDate(job.scheduled_at) }}</p>
        <p class="meta">Mô tả: {{ job.description || "Chưa có mô tả" }}</p>

        <div class="actions">
          <button v-if="job.status === 'moi'" class="primary" @click="updateStatus(job, 'dang_xu_ly')">
            Đã tiếp nhận yêu cầu
          </button>
          <button v-if="job.status === 'dang_xu_ly'" class="primary" @click="updateStatus(job, 'hoan_thanh')">
            Hoàn thành
          </button>
          <button class="ghost" @click="goDetail(job)">Xem chi tiết</button>
        </div>
      </article>
    </section>

    <div class="pagination" v-if="!loading && totalPages > 1">
      <button class="ghost" :disabled="page === 1" @click="page--">Trang trước</button>
      <span>Trang {{ page }} / {{ totalPages }}</span>
      <button class="ghost" :disabled="page === totalPages" @click="page++">Trang sau</button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000";

const techUser = (() => {
  try {
    const raw = localStorage.getItem("tech_user");
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
})();
const technicianId = techUser?.id || techUser?.id_nguoi_dung || null;

const jobs = ref([]);
const statusFilter = ref("all");
const searchTerm = ref("");
const page = ref(1);
const perPage = ref(8);
const totalPages = ref(1);
const loading = ref(false);
const error = ref("");

const displayJobs = computed(() => jobs.value || []);

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const url = new URL(`${API_BASE}/api/technician/jobs`);
    url.searchParams.set("page", page.value);
    url.searchParams.set("per_page", perPage.value);
    if (technicianId) url.searchParams.set("technician_id", technicianId);
    if (statusFilter.value && statusFilter.value !== "all") url.searchParams.set("status", statusFilter.value);
    if (searchTerm.value) url.searchParams.set("q", searchTerm.value);

    const res = await fetch(url.toString());
    const data = await res.json();
    if (!res.ok) throw new Error(data?.message || "Không tải được danh sách công việc.");

    jobs.value = data.data || [];
    totalPages.value = data.last_page || 1;
  } catch (err) {
    error.value = err?.message || "Không tải được danh sách công việc.";
  } finally {
    loading.value = false;
  }
}

let searchTimer;
watch([statusFilter, page], fetchJobs);
watch(searchTerm, () => {
  page.value = 1;
  clearTimeout(searchTimer);
  searchTimer = setTimeout(fetchJobs, 250);
});

function statusLabel(status) {
  if (status === "moi") return "Chờ tiếp nhận";
  if (status === "dang_xu_ly") return "Đang xử lý";
  if (status === "hoan_thanh") return "Hoàn thành";
  return "Hủy";
}

function statusClass(status) {
  if (status === "moi") return "new";
  if (status === "dang_xu_ly") return "doing";
  if (status === "hoan_thanh") return "done";
  return "cancel";
}

function goDetail(job) {
  router.push({ path: "/technician/job-detail", query: { code: job.code } });
}

async function updateStatus(job, status) {
  try {
    const res = await fetch(`${API_BASE}/api/technician/jobs/${job.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ status }),
    });
    if (!res.ok) throw new Error("Không cập nhật được trạng thái.");
    await fetchJobs();
  } catch (err) {
    error.value = err?.message || "Không cập nhật được trạng thái.";
  }
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

fetchJobs();
</script>

<style scoped>
.page {
  display: grid;
  gap: 18px;
  color: #0f172a;
}

.header {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  align-items: flex-end;
  flex-wrap: wrap;
}

.header h1 {
  margin: 0;
  font-size: 22px;
}

.header p {
  margin: 6px 0 0;
  color: #64748b;
  font-size: 13px;
}

.filters {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filters select,
.filters input {
  padding: 8px 10px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-size: 12px;
}

.error {
  background: #fff1f2;
  border: 1px solid #fecdd3;
  color: #be123c;
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 12px;
}

.list {
  display: grid;
  gap: 12px;
}

.job-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.job-top {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: center;
}

.job-top h3 {
  margin: 0;
  font-size: 16px;
}

.job-top p {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 13px;
}

.meta {
  margin: 6px 0 0;
  font-size: 12px;
  color: #475569;
}

.tag {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

.tag.new {
  background: #dbeafe;
  color: #1d4ed8;
}

.tag.doing {
  background: #fde68a;
  color: #b45309;
}

.tag.done {
  background: #dcfce7;
  color: #15803d;
}

.tag.cancel {
  background: #fee2e2;
  color: #b91c1c;
}

.actions {
  margin-top: 12px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.primary {
  border: none;
  background: #2563eb;
  color: #fff;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 12px;
  cursor: pointer;
}

.ghost {
  border: 1px solid #e2e8f0;
  background: #fff;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 12px;
  cursor: pointer;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 10px;
  font-size: 12px;
}
</style>
