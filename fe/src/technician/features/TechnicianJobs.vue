<template>
  <div class="page">
    <header class="header">
      <div>
        <h1>Danh sách công việc</h1>
        <p>Theo dõi các công việc được giao.</p>
      </div>
      <div class="filters">
        <select v-model="statusFilter">
          <option value="all">Tất cả trạng thái</option>
          <option value="moi">Mới</option>
          <option value="dang_xu_ly">Đang xử lý</option>
          <option value="hoan_thanh">Hoàn thành</option>
          <option value="huy">Hủy</option>
        </select>
        <input v-model.trim="searchTerm" type="text" placeholder="Tìm theo mã hoặc địa điểm" />
      </div>
    </header>

    <section class="list">
      <article class="job-card create-card">
        <div class="job-top">
          <div>
            <h3>Tạo công việc mới</h3>
            <p>Nhập thông tin để tạo nhanh</p>
          </div>
          <span class="tag new">Mới</span>
        </div>
        <div class="create-grid">
          <label class="field">
            <span>Mã công việc</span>
            <input v-model.trim="createForm.code" type="text" placeholder="JOB-0006" />
          </label>
          <label class="field">
            <span>Tiêu đề</span>
            <input v-model.trim="createForm.title" type="text" placeholder="Sửa đèn tầng 3" />
          </label>
          <label class="field">
            <span>Địa điểm</span>
            <input v-model.trim="createForm.location" type="text" placeholder="Tòa A - Tầng 3" />
          </label>
          <label class="field">
            <span>Lịch hẹn</span>
            <input v-model="createForm.scheduled_at" type="datetime-local" />
          </label>
          <label class="field">
            <span>Trạng thái</span>
            <select v-model="createForm.status">
              <option value="moi">Mới</option>
              <option value="dang_xu_ly">Đang xử lý</option>
              <option value="hoan_thanh">Hoàn thành</option>
              <option value="huy">Hủy</option>
            </select>
          </label>
          <label class="field">
            <span>Ưu tiên</span>
            <select v-model="createForm.priority">
              <option value="thap">Thấp</option>
              <option value="trung_binh">Trung bình</option>
              <option value="cao">Cao</option>
            </select>
          </label>
        </div>
        <label class="field">
          <span>Mô tả</span>
          <textarea v-model.trim="createForm.description" rows="2" placeholder="Mô tả ngắn"></textarea>
        </label>
        <div class="actions">
          <button class="primary" @click="createJob">Tạo công việc</button>
        </div>
      </article>

      <article v-for="job in displayJobs" :key="job.id || job.code" class="job-card">
        <div class="job-top">
          <div>
            <h3>{{ job.code || "JOB-????" }}</h3>
            <p>{{ job.title }}</p>
          </div>
          <span class="tag" :class="statusClass(job.status)">{{ statusLabel(job.status) }}</span>
        </div>
        <p class="meta">Địa điểm: {{ job.location || "Chưa cập nhật" }}</p>
        <p class="meta">Lịch hẹn: {{ formatDate(job.scheduled_at) }}</p>
        <div class="actions">
          <button class="primary" @click="onPrimaryAction(job)">{{ primaryLabel(job.status) }}</button>
          <button class="ghost" @click="goDetail(job)">Xem chi tiết</button>
          <button class="ghost" @click="startEdit(job)">Chỉnh sửa</button>
          <button class="ghost danger" @click="deleteJob(job)">Xóa</button>
        </div>

        <div v-if="editingId === job.id" class="edit-panel">
          <div class="create-grid">
            <label class="field">
              <span>Mã công việc</span>
              <input v-model.trim="editForm.code" type="text" />
            </label>
            <label class="field">
              <span>Tiêu đề</span>
              <input v-model.trim="editForm.title" type="text" />
            </label>
            <label class="field">
              <span>Địa điểm</span>
              <input v-model.trim="editForm.location" type="text" />
            </label>
            <label class="field">
              <span>Lịch hẹn</span>
              <input v-model="editForm.scheduled_at" type="datetime-local" />
            </label>
            <label class="field">
              <span>Trạng thái</span>
              <select v-model="editForm.status">
                <option value="moi">Mới</option>
                <option value="dang_xu_ly">Đang xử lý</option>
                <option value="hoan_thanh">Hoàn thành</option>
                <option value="huy">Hủy</option>
              </select>
            </label>
            <label class="field">
              <span>Ưu tiên</span>
              <select v-model="editForm.priority">
                <option value="thap">Thấp</option>
                <option value="trung_binh">Trung bình</option>
                <option value="cao">Cao</option>
              </select>
            </label>
          </div>
          <label class="field">
            <span>Mô tả</span>
            <textarea v-model.trim="editForm.description" rows="2"></textarea>
          </label>
          <div class="actions">
            <button class="primary" @click="saveEdit(job)">Lưu</button>
            <button class="ghost" @click="cancelEdit">Hủy</button>
          </div>
        </div>
      </article>
    </section>

    <div class="pagination" v-if="totalPages > 1">
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

const jobs = ref([]);
const statusFilter = ref("all");
const searchTerm = ref("");
const page = ref(1);
const perPage = ref(5);
const totalPages = ref(1);
const loading = ref(false);
const error = ref("");
const editingId = ref(null);

const createForm = ref({
  code: "",
  title: "",
  location: "",
  description: "",
  scheduled_at: "",
  status: "moi",
  priority: "trung_binh",
});

const editForm = ref({
  code: "",
  title: "",
  location: "",
  description: "",
  scheduled_at: "",
  status: "moi",
  priority: "trung_binh",
});

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const url = new URL(`${API_BASE}/api/technician/jobs`);
    url.searchParams.set("page", page.value);
    url.searchParams.set("per_page", perPage.value);
    if (statusFilter.value && statusFilter.value !== "all") {
      url.searchParams.set("status", statusFilter.value);
    }
    if (searchTerm.value) {
      url.searchParams.set("q", searchTerm.value);
    }

    const res = await fetch(url.toString());
    const data = await res.json();
    jobs.value = data.data || [];
    totalPages.value = data.last_page || 1;
  } catch (err) {
    error.value = "Không tải được danh sách công việc.";
  } finally {
    loading.value = false;
  }
}

fetchJobs();

let searchTimer;
watch([statusFilter, page], () => fetchJobs());
watch(searchTerm, () => {
  page.value = 1;
  clearTimeout(searchTimer);
  searchTimer = setTimeout(fetchJobs, 300);
});

function statusLabel(status) {
  if (status === "moi") return "Mới";
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

function primaryLabel(status) {
  if (status === "moi") return "Nhận việc";
  if (status === "dang_xu_ly") return "Cập nhật";
  if (status === "hoan_thanh") return "Đã xong";
  return "Đã hủy";
}

async function onPrimaryAction(job) {
  if (!job.id || String(job.id).startsWith("demo-")) {
    if (job.status === "moi") {
      job.status = "dang_xu_ly";
      return;
    }
    if (job.status === "dang_xu_ly") {
      job.status = "hoan_thanh";
    }
    return;
  }
  if (job.status === "moi") {
    await updateStatus(job, "dang_xu_ly");
    return;
  }
  if (job.status === "dang_xu_ly") {
    await updateStatus(job, "hoan_thanh");
  }
}

function goDetail(job) {
  router.push({ path: "/technician/job-detail", query: { code: job.code } });
}

async function updateStatus(job, status) {
  try {
    await fetch(`${API_BASE}/api/technician/jobs/${job.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ status }),
    });
    fetchJobs();
  } catch (err) {
    error.value = "Không cập nhật được trạng thái.";
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

function toInputDate(value) {
  if (!value) return "";
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return "";
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const yyyy = d.getFullYear();
  const hh = String(d.getHours()).padStart(2, "0");
  const min = String(d.getMinutes()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
}

async function createJob() {
  if (!createForm.value.code || !createForm.value.title) {
    error.value = "Vui lòng nhập mã và tiêu đề.";
    return;
  }
  try {
    await fetch(`${API_BASE}/api/technician/jobs`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(createForm.value),
    });
    createForm.value = {
      code: "",
      title: "",
      location: "",
      description: "",
      scheduled_at: "",
      status: "moi",
      priority: "trung_binh",
    };
    fetchJobs();
  } catch (err) {
    error.value = "Không tạo được công việc.";
  }
}

function startEdit(job) {
  if (!job.id) return;
  editingId.value = job.id;
  editForm.value = {
    code: job.code || "",
    title: job.title || "",
    location: job.location || "",
    description: job.description || "",
    scheduled_at: toInputDate(job.scheduled_at),
    status: job.status || "moi",
    priority: job.priority || "trung_binh",
  };
}

function cancelEdit() {
  editingId.value = null;
}

async function saveEdit(job) {
  try {
    await fetch(`${API_BASE}/api/technician/jobs/${job.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(editForm.value),
    });
    editingId.value = null;
    fetchJobs();
  } catch (err) {
    error.value = "Không lưu được công việc.";
  }
}

async function deleteJob(job) {
  if (!job.id) return;
  if (!confirm("Xóa công việc này?")) return;
  try {
    await fetch(`${API_BASE}/api/technician/jobs/${job.id}`, { method: "DELETE" });
    fetchJobs();
  } catch (err) {
    error.value = "Không xóa được công việc.";
  }
}
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
.filters input,
.create-grid select,
.create-grid input {
  padding: 8px 10px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
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

.create-card {
  border: 1px dashed #cbd5f5;
  background: #f8fbff;
}

.create-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 10px;
  margin-top: 10px;
}

.field textarea {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-size: 13px;
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

.pagination {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 10px;
  font-size: 12px;
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

.ghost.danger {
  border-color: #fecaca;
  color: #b91c1c;
}

.edit-panel {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px dashed #e2e8f0;
}
</style>
const demoJobs = [
  {
    id: "demo-1",
    code: "JOB-0003",
    title: "Bảo trì định kỳ thang máy",
    location: "Tòa A - Tầng 1",
    scheduled_at: "2026-02-24T09:00:00",
    status: "moi",
  },
  {
    id: "demo-2",
    code: "JOB-0004",
    title: "Kiểm tra hệ thống PCCC",
    location: "Tòa B - Tầng 5",
    scheduled_at: "2026-02-24T11:00:00",
    status: "dang_xu_ly",
  },
  {
    id: "demo-3",
    code: "JOB-0005",
    title: "Sửa đèn hành lang",
    location: "Tòa C - Tầng 2",
    scheduled_at: "2026-02-24T14:30:00",
    status: "hoan_thanh",
  },
];

const displayJobs = computed(() => {
  if (loading.value) return [];
  if (jobs.value.length > 0) return jobs.value;
  return demoJobs;
});
