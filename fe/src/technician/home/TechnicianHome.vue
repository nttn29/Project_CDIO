<template>
  <div class="tech-home">
    <!-- HERO SECTION -->
    <header class="hero">
      <div class="hero-content">
        <span class="badge">Technician Center</span>
        <h1>Trang chủ kỹ thuật viên</h1>
        <p class="subtitle">
          Theo dõi công việc, lịch hẹn và trạng thái xử lý trong ngày.
        </p>
      </div>
    </header>

    <!-- STATS -->
    <section class="stats">
      <div class="stat-card">
        <div class="icon-wrap noti-icon">
          <i class="fas fa-calendar-day"></i>
        </div>
        <div class="info">
          <p class="stat-label">Công việc hôm nay</p>
          <p class="stat-value">{{ stats.today }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon-wrap processing-icon">
          <i class="fas fa-spinner fa-spin-hover"></i>
        </div>
        <div class="info">
          <p class="stat-label">Đang xử lý</p>
          <p class="stat-value">{{ stats.doing }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="icon-wrap done-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="info">
          <p class="stat-label">Hoàn thành</p>
          <p class="stat-value">{{ stats.done }}</p>
        </div>
      </div>
    </section>

    <!-- CONTENT PANELS -->
    <div class="panels-grid">
      <section class="panel">
        <div class="panel-header">
          <h2><i class="fas fa-clock"></i> Lịch hẹn gần nhất</h2>
        </div>
        <ul v-if="stats.latest && stats.latest.length > 0" class="latest-list">
          <li v-for="job in stats.latest" :key="job.id" class="job-item">
            <div class="job-info">
              <strong>{{ job.code }}</strong> - {{ job.title }}
              <span class="loc"><i class="fas fa-map-marker-alt"></i> {{ job.location }}</span>
            </div>
            <div class="job-actions">
              <button
                v-if="job.status === 'moi'"
                class="accept-btn"
                @click="acceptJob(job)"
              >
                Đã tiếp nhận
              </button>
              <router-link
                :to="{ path: '/technician/job-detail', query: { code: job.code } }"
                class="action-btn"
              >
                Xem chi tiết
              </router-link>
            </div>
          </li>
        </ul>
        <div v-else class="empty-state">
           <i class="fas fa-calendar-times"></i>
           <p>Chưa có lịch hẹn mới.</p>
        </div>
      </section>

      <section class="panel">
        <div class="panel-header">
          <h2><i class="fas fa-exclamation-circle"></i> Yêu cầu mới</h2>
        </div>
        <div class="empty-state">
           <i class="fas fa-inbox"></i>
           <p>Không có yêu cầu khẩn cấp nào.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const API_BASE = import.meta.env.VITE_API_BASE || "http://localhost:8000";
const router = useRouter();
const techUser = (() => {
  try {
    const raw = localStorage.getItem("tech_user");
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
})();
const technicianId = techUser?.id || techUser?.id_nguoi_dung || null;

const stats = ref({
  today: 0,
  doing: 0,
  done: 0,
  latest: []
});

async function fetchStats() {
  try {
    const url = new URL(`${API_BASE}/api/technician/jobs/stats`);
    if (technicianId) {
      url.searchParams.set("technician_id", technicianId);
    }
    const res = await fetch(url.toString());
    const data = await res.json();
    stats.value = data;
  } catch (err) {
    console.error("Lỗi khi tải thống kê công việc", err);
  }
}

async function acceptJob(job) {
  if (!job?.id) return;
  try {
    await fetch(`${API_BASE}/api/technician/jobs/${job.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ status: "dang_xu_ly" }),
    });
    await fetchStats();
    router.push({ path: "/technician/job-detail", query: { code: job.code } });
  } catch (err) {
    console.error("Không cập nhật được trạng thái tiếp nhận", err);
  }
}

onMounted(() => {
  fetchStats();
});
</script>

<style scoped>
.tech-home {
  padding: 30px;
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* HERO */
.hero {
  background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
  color: #fff;
  border-radius: 20px;
  padding: 40px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
}

.hero::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background: url('https://www.transparenttextures.com/patterns/connected-points.png');
  opacity: 0.2;
  pointer-events: none;
}

.hero-content {
  position: relative;
  z-index: 1;
}

.badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(8px);
  padding: 8px 16px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.hero h1 {
  font-size: 38px;
  margin: 0 0 10px;
  font-weight: 800;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.subtitle {
  font-size: 16px;
  opacity: 0.9;
  margin: 0;
}

/* STATS */
.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}

.stat-card {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  border: 1px solid #f1f5f9;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.1);
}

.icon-wrap {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  margin-right: 20px;
  flex-shrink: 0;
}

.noti-icon { background: #fef3c7; color: #f59e0b; }
.processing-icon { background: #dbeafe; color: #3b82f6; }
.done-icon { background: #dcfce7; color: #10b981; }

.stat-card:hover .fa-spin-hover {
  animation: fa-spin 2s infinite linear;
}

.info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  margin: 0 0 4px;
  color: #64748b;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.stat-value {
  margin: 0;
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

/* PANELS */
.panels-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 24px;
}

.panel {
  background: #fff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  border: 1px solid #f1f5f9;
}

.panel-header {
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 16px;
  margin-bottom: 16px;
}

.panel h2 {
  margin: 0;
  font-size: 18px;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 10px;
}

.panel h2 i {
  color: #3b82f6;
}

/* LISTS */
.latest-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.job-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.2s ease;
}

.job-item:hover {
  background-color: #f8fafc;
  padding-left: 10px;
  padding-right: 10px;
  margin-left: -10px;
  margin-right: -10px;
  border-radius: 10px;
  border-bottom-color: transparent;
}

.job-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.job-info {
  font-size: 15px;
  color: #334155;
}

.job-info strong {
  color: #0f172a;
}

.loc {
  display: block;
  font-size: 13px;
  color: #64748b;
  margin-top: 6px;
}

.loc i {
  color: #94a3b8;
  margin-right: 4px;
}

.action-btn {
  padding: 8px 12px;
  border-radius: 10px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  text-decoration: none;
  transition: all 0.2s ease;
  font-size: 12px;
  border: 1px solid #e2e8f0;
}

.action-btn:hover {
  background: #3b82f6;
  color: white;
}

.accept-btn {
  border: 1px solid #86efac;
  background: #dcfce7;
  color: #15803d;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.accept-btn:hover {
  background: #bbf7d0;
}

/* EMPTY STATE */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  color: #94a3b8;
  text-align: center;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-state p {
  margin: 0;
  font-size: 15px;
}
</style>
