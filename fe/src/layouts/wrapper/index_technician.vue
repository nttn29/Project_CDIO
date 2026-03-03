<template>
  <div class="tech-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="logo">{{ userInitials }}</div>
        <div>
          <h1>{{ displayName }}</h1>
          <p>Kỹ thuật viên</p>
        </div>
      </div>

      <nav class="menu">
        <router-link to="/technician" class="menu-item" exact-active-class="active">Trang chủ</router-link>
        <router-link to="/technician/jobs" class="menu-item" exact-active-class="active">Công việc</router-link>
        <router-link to="/technician/worklog" class="menu-item" exact-active-class="active">WorkLog & Chi phí</router-link>
        <router-link to="/technician/status" class="menu-item" exact-active-class="active">Kho vật tư</router-link>
        <router-link to="/technician/history" class="menu-item" exact-active-class="active">Báo cáo</router-link>
      </nav>

      <div class="logout-wrap">
        <button class="logout-btn" @click="logout">Đăng xuất</button>
      </div>
    </aside>

    <div class="main">
      <header class="topbar">
        <div class="title">
          <h2>{{ pageTitle }}</h2>
          <span class="status-pill online">Online</span>
        </div>
        <div class="icons">
          <div class="icon" title="Thông báo">
             <i class="fas fa-bell"></i>
          </div>
          <div class="icon" title="Tin nhắn">
             <i class="fas fa-envelope"></i>
          </div>
          <div class="icon" title="Cài đặt">
             <i class="fas fa-cog"></i>
          </div>
          <div class="icon" title="Thông tin cá nhân">
             <i class="fas fa-user"></i>
          </div>
        </div>
      </header>

      <main class="content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import "../../assets/css/icons.css";

const router = useRouter();
const route  = useRoute();

const pageTitles = {
  '/technician':         'Trang chủ',
  '/technician/jobs':    'Công việc',
  '/technician/worklog': 'WorkLog & Chi phí',
  '/technician/status':  'Kho vật tư',
  '/technician/history': 'Báo cáo',
};

const pageTitle = computed(() => pageTitles[route.path] ?? 'Technician Center');

const techUser = (() => {
  try {
    const raw = localStorage.getItem("tech_user");
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
})();

const displayName = computed(() => {
  const name = techUser?.ten || techUser?.name || "";
  return name.trim() || "Kỹ thuật viên";
});

const userInitials = computed(() => {
  const source = displayName.value.trim();
  if (!source) return "KT";
  const parts = source.split(/\s+/).filter(Boolean);
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
  return `${parts[0][0] || ""}${parts[parts.length - 1][0] || ""}`.toUpperCase();
});

function logout() {
  localStorage.removeItem("tech_auth");
  localStorage.removeItem("tech_user");
  router.push("/login-tech");
}
</script>

<style scoped>
.tech-shell {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 260px 1fr;
  background: linear-gradient(135deg, #f4f8ff 0%, #eef4ff 55%, #f7f9fc 100%);
  color: #0f172a;
  font-family: "Poppins", "Segoe UI", Tahoma, Arial, sans-serif;
}

.sidebar {
  background: #ffffff;
  padding: 18px;
  display: grid;
  gap: 18px;
  border-right: 1px solid #e2e8f0;
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.brand {
  display: flex;
  gap: 12px;
  align-items: center;
}

.logo {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, #3b82f6, #22d3ee);
  display: grid;
  place-items: center;
  font-weight: 700;
  color: #fff;
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
}

.brand h1 {
  margin: 0;
  font-size: 14px;
  letter-spacing: 0.5px;
}

.brand p {
  margin: 4px 0 0;
  font-size: 12px;
  color: #64748b;
}

.menu {
  display: grid;
  gap: 8px;
}

.menu-item {
  text-decoration: none;
  color: #0f172a;
  padding: 10px 12px;
  border-radius: 10px;
  background: #f8fafc;
  font-size: 13px;
  border: 1px solid #e2e8f0;
  transition: 0.15s ease;
}

.menu-item.active,
.menu-item:hover {
  background: #e0ebff;
  border-color: #93c5fd;
  box-shadow: 0 8px 18px rgba(59, 130, 246, 0.15);
  transform: translateX(2px);
}

.logout-wrap {
  margin-top: auto;
}

.logout-btn {
  width: 100%;
  border: 1px solid #fecaca;
  background: #fff1f2;
  color: #b91c1c;
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.15s ease;
}

.logout-btn:hover {
  background: #ffe4e6;
}

.main {
  display: grid;
  grid-template-rows: auto 1fr;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 20px;
  border-bottom: 1px solid #e2e8f0;
  background: #ffffff;
  position: sticky;
  top: 0;
  z-index: 100;
}

.title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.title h2 {
  margin: 0;
  font-size: 16px;
}

.title span {
  color: #64748b;
  font-size: 12px;
}

.icons {
  display: flex;
  gap: 8px;
}

.icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
}

.icon:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(15, 23, 42, 0.12);
  background: #e2e8f0;
  color: #3b82f6;
}

.content {
  padding: 20px;
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}

.status {
  padding: 2px 8px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 600;
  border: 1px solid transparent;
}

.status.online {
  background: #dcfce7;
  color: #15803d;
  border-color: #86efac;
}

.status.offline {
  background: #fee2e2;
  color: #b91c1c;
  border-color: #fecaca;
}

.status-pill {
  margin-left: 6px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 600;
  border: 1px solid transparent;
}

.status-pill.online {
  background: #dcfce7;
  color: #15803d;
  border-color: #86efac;
}

.status-pill.offline {
  background: #fee2e2;
  color: #b91c1c;
  border-color: #fecaca;
}

@media (max-width: 900px) {
  .tech-shell {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: sticky;
    top: 0;
    z-index: 10;
  }
}
</style>
