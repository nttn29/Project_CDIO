<template>
  <div class="page">
    <!-- HERO BANNER -->
    <div class="hero">
      <div class="hero-text">
        <span class="badge">Quản lý công việc</span>
        <h1>Danh sách Công Việc</h1>
        <p>Theo dõi và xử lý các yêu cầu bảo trì được phân công cho bạn.</p>
      </div>
      <div class="hero-stats">
        <div class="stat-pill">
          <span class="stat-num">{{ jobs.filter(j => j.status === 'moi').length }}</span>
          <span class="stat-lbl">Chờ tiếp nhận</span>
        </div>
        <div class="stat-pill doing">
          <span class="stat-num">{{ jobs.filter(j => j.status === 'dang_xu_ly').length }}</span>
          <span class="stat-lbl">Đang xử lý</span>
        </div>
        <div class="stat-pill done">
          <span class="stat-num">{{ jobs.filter(j => j.status === 'hoan_thanh').length }}</span>
          <span class="stat-lbl">Hoàn thành</span>
        </div>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <div class="filter-pills">
        <button
          v-for="f in filterOptions"
          :key="f.value"
          class="filter-pill"
          :class="{ active: statusFilter === f.value }"
          @click="statusFilter = f.value"
        >{{ f.label }}</button>
      </div>
      <div class="search-wrap">
        <i class="fas fa-search search-icon"></i>
        <input v-model.trim="searchTerm" type="text" placeholder="Tìm theo mã, địa điểm, mô tả..." />
      </div>
    </div>

    <!-- ERROR -->
    <div v-if="error" class="error-banner">
      <i class="fas fa-exclamation-circle"></i> {{ error }}
    </div>

    <!-- JOB LIST -->
    <section class="list">
      <!-- Loading skeleton -->
      <div v-if="loading" class="loading-wrap">
        <div class="skeleton" v-for="n in 4" :key="n"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="displayJobs.length === 0" class="empty-state">
        <div class="empty-icon">📋</div>
        <h3>Không có công việc nào</h3>
        <p>Chưa có yêu cầu nào khớp với bộ lọc hiện tại.</p>
      </div>

      <!-- Cards -->
      <article v-for="job in displayJobs" :key="job.id || job.code" class="job-card">
        <div class="card-top">
          <div class="card-title-wrap">
            <div class="job-icon" :class="statusClass(job.status)">
              <i :class="statusIcon(job.status)"></i>
            </div>
            <div>
              <div class="job-code">{{ job.code || 'PC-?' }}</div>
              <div class="job-title">{{ job.title || 'Yêu cầu bảo trì' }}</div>
            </div>
          </div>
          <span class="tag" :class="statusClass(job.status)">{{ statusLabel(job.status) }}</span>
        </div>

        <div class="card-meta-grid">
          <div class="meta-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ job.location || 'Chưa cập nhật' }}</span>
          </div>
          <div class="meta-item">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ formatDate(job.scheduled_at) }}</span>
          </div>
          <div class="meta-item full-row">
            <i class="fas fa-align-left"></i>
            <span>{{ job.description || 'Chưa có mô tả' }}</span>
          </div>
        </div>

        <div class="card-actions">
          <button v-if="job.status === 'moi'" class="btn-accept" @click="updateStatus(job, 'dang_xu_ly')">
            <i class="fas fa-check"></i> Tiếp nhận
          </button>
          <button v-if="job.status === 'dang_xu_ly'" class="btn-done" @click="updateStatus(job, 'hoan_thanh')">
            <i class="fas fa-check-double"></i> Hoàn thành
          </button>
          <button class="btn-detail" @click="goDetail(job)">
            <i class="fas fa-eye"></i> Xem chi tiết
          </button>
        </div>
      </article>
    </section>

    <!-- PAGINATION -->
    <div class="pagination" v-if="!loading && totalPages > 1">
      <button class="page-btn" :disabled="page === 1" @click="page--">
        <i class="fas fa-chevron-left"></i> Trước
      </button>
      <div class="page-nums">
        <span class="page-info">Trang <strong>{{ page }}</strong> / {{ totalPages }}</span>
      </div>
      <button class="page-btn" :disabled="page === totalPages" @click="page++">
        Sau <i class="fas fa-chevron-right"></i>
      </button>
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

const filterOptions = [
  { value: 'all', label: 'Tất cả' },
  { value: 'moi', label: 'Chờ tiếp nhận' },
  { value: 'dang_xu_ly', label: 'Đang xử lý' },
  { value: 'hoan_thanh', label: 'Hoàn thành' },
  { value: 'huy', label: 'Đã hủy' },
];

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

function statusIcon(status) {
  if (status === 'moi') return 'fas fa-inbox';
  if (status === 'dang_xu_ly') return 'fas fa-spinner';
  if (status === 'hoan_thanh') return 'fas fa-check-circle';
  return 'fas fa-times-circle';
}

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
  display: flex;
  flex-direction: column;
  gap: 20px;
  color: #0f172a;
  padding-bottom: 40px;
}

/* HERO */
.hero {
  background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
  border-radius: 20px;
  padding: 28px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
  color: #fff;
}

.badge {
  display: inline-block;
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.35);
  border-radius: 999px;
  padding: 4px 14px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 10px;
}

.hero-text h1 {
  margin: 0 0 6px;
  font-size: 26px;
  font-weight: 700;
  color: #fff;
}

.hero-text p {
  margin: 0;
  font-size: 14px;
  opacity: 0.85;
}

.hero-stats {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.stat-pill {
  background: rgba(255,255,255,0.18);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 14px;
  padding: 12px 20px;
  text-align: center;
  min-width: 80px;
}

.stat-pill.doing {
  background: rgba(251, 191, 36, 0.25);
  border-color: rgba(251, 191, 36, 0.4);
}

.stat-pill.done {
  background: rgba(34, 197, 94, 0.25);
  border-color: rgba(34, 197, 94, 0.4);
}

.stat-num {
  display: block;
  font-size: 24px;
  font-weight: 700;
  color: #fff;
  line-height: 1;
}

.stat-lbl {
  display: block;
  font-size: 11px;
  color: rgba(255,255,255,0.8);
  margin-top: 4px;
}

/* FILTER BAR */
.filter-bar {
  background: #fff;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
  border: 1px solid #e2e8f0;
}

.filter-pills {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-pill {
  padding: 6px 16px;
  border-radius: 999px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  font-size: 12px;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-pill:hover {
  background: #e0ebff;
  border-color: #93c5fd;
  color: #1d4ed8;
}

.filter-pill.active {
  background: #2563eb;
  border-color: #2563eb;
  color: #fff;
  font-weight: 600;
}

.search-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 12px;
  color: #94a3b8;
  font-size: 13px;
}

.search-wrap input {
  padding: 9px 12px 9px 34px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-size: 13px;
  width: 260px;
  outline: none;
  transition: 0.2s;
  background: #f8fafc;
}

.search-wrap input:focus {
  border-color: #3b82f6;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

/* ERROR */
.error-banner {
  background: #fff1f2;
  border: 1px solid #fecdd3;
  color: #be123c;
  border-radius: 12px;
  padding: 12px 16px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* LIST */
.list {
  display: grid;
  gap: 14px;
}

.loading-wrap {
  display: grid;
  gap: 14px;
}

.skeleton {
  height: 130px;
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 16px;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: #fff;
  border-radius: 16px;
  border: 1px dashed #e2e8f0;
}

.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-state h3 { margin: 0 0 8px; font-size: 18px; }
.empty-state p  { margin: 0; color: #94a3b8; font-size: 14px; }

/* JOB CARD */
.job-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
  border: 1px solid #f1f5f9;
  transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.job-card:hover {
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
  transform: translateY(-2px);
}

.card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 16px;
}

.card-title-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}

.job-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}

.job-icon.new    { background: #dbeafe; color: #1d4ed8; }
.job-icon.doing  { background: #fef9c3; color: #b45309; }
.job-icon.done   { background: #dcfce7; color: #15803d; }
.job-icon.cancel { background: #fee2e2; color: #b91c1c; }

.job-code  { font-size: 15px; font-weight: 700; color: #0f172a; }
.job-title { font-size: 13px; color: #64748b; margin-top: 2px; }

.tag {
  padding: 5px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
  flex-shrink: 0;
}

.tag.new    { background: #dbeafe; color: #1d4ed8; }
.tag.doing  { background: #fef9c3; color: #b45309; }
.tag.done   { background: #dcfce7; color: #15803d; }
.tag.cancel { background: #fee2e2; color: #b91c1c; }

.card-meta-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px 16px;
  padding: 14px;
  background: #f8fafc;
  border-radius: 10px;
  margin-bottom: 16px;
}

.meta-item {
  display: flex;
  align-items: flex-start;
  gap: 7px;
  font-size: 13px;
  color: #475569;
}

.meta-item i {
  color: #94a3b8;
  margin-top: 2px;
  width: 14px;
  flex-shrink: 0;
}

.meta-item.full-row { grid-column: 1 / -1; }

.card-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-accept {
  padding: 9px 18px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #2563eb, #4f46e5);
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.2s ease;
}

.btn-accept:hover {
  opacity: 0.9;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(37,99,235,0.3);
}

.btn-done {
  padding: 9px 18px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #16a34a, #059669);
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.2s ease;
}

.btn-done:hover {
  opacity: 0.9;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(22,163,74,0.3);
}

.btn-detail {
  padding: 9px 18px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  color: #475569;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: 0.2s ease;
}

.btn-detail:hover {
  background: #e0ebff;
  border-color: #93c5fd;
  color: #1d4ed8;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
}

.page-btn {
  padding: 8px 18px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  color: #475569;
  transition: 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: #2563eb;
  border-color: #2563eb;
  color: #fff;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: default;
}

.page-info {
  font-size: 13px;
  color: #64748b;
}
</style>
