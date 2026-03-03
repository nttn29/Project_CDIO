<template>
  <div class="page">
    <!-- HERO SECTION -->
    <div class="admin-hero">
      <div class="hero-content">
        <h1>👋 Xin chào, Ban Quản Lý!</h1>
        <p>Tổng quan hệ thống EcoHome được cập nhật theo thời gian thực 🏢</p>
      </div>
    </div>

    <!-- MAIN STATS -->
    <div class="stats-grid">
      <div class="stat-card primary-card">
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="info">
          <h3>Tổng cư dân</h3>
          <p class="value">1,248</p>
          <span class="trend up"><i class="fas fa-arrow-up"></i> 12% so với tháng trước</span>
        </div>
      </div>

      <div class="stat-card warning-card">
        <div class="icon">
          <i class="fas fa-tools"></i>
        </div>
        <div class="info">
          <h3>Yêu cầu bảo trì</h3>
          <p class="value">34</p>
          <span class="trend"><i class="fas fa-minus"></i> Đang chờ xử lý</span>
        </div>
      </div>

      <div class="stat-card success-card">
        <div class="icon">
          <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="info">
          <h3>Doanh thu tháng</h3>
          <p class="value">452Tr</p>
          <span class="trend up"><i class="fas fa-arrow-up"></i> 5% so với tháng trước</span>
        </div>
      </div>

      <div class="stat-card danger-card">
        <div class="icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="info">
          <h3>Sự cố khẩn cấp</h3>
          <p class="value">2</p>
          <span class="trend down"><i class="fas fa-arrow-down"></i> Cần xử lý ngay</span>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick-actions">
      <h2>Phím tắt nhanh</h2>
      <div class="actions-grid">
        <router-link to="/admin/qlcd/danh-sach-nha" class="action-btn">
          <div class="action-icon"><i class="fas fa-user-plus"></i></div>
          <span>Thêm Cư Dân</span>
        </router-link>
        <router-link to="/admin/bao-tri/yeu-cau" class="action-btn">
          <div class="action-icon"><i class="fas fa-clipboard-list"></i></div>
          <span>Duyệt Bảo Trì</span>
        </router-link>
        <router-link to="/admin/tai-chinh/hoa-don" class="action-btn">
          <div class="action-icon"><i class="fas fa-file-invoice"></i></div>
          <span>Tạo Hóa Đơn</span>
        </router-link>
        <router-link to="/admin/bao-tri/phan-cong" class="action-btn">
          <div class="action-icon"><i class="fas fa-hard-hat"></i></div>
          <span>Phân Công Thợ</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
// Admin Dashboard logic
</script>

<style scoped>
.page {
  padding: 16px 30px 30px 30px;
  max-width: 1400px;
  margin: 0 auto;
}

/* ADMIN HERO */
.admin-hero {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border-radius: 20px;
  padding: 40px;
  color: white;
  margin-bottom: 30px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4);
  position: relative;
  overflow: hidden;
}

.admin-hero::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.2), transparent 50%);
  pointer-events: none;
}

.hero-content h1 {
  font-size: 36px;
  margin: 0 0 10px;
  font-weight: 800;
  background: linear-gradient(to right, #fff, #cbd5e1);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-content p {
  font-size: 16px;
  color: #94a3b8;
  margin: 0;
}

/* STATS GRID */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 24px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 20px;
  padding: 24px;
  display: flex;
  align-items: flex-start;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  border: 1px solid #f1f5f9;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 20px -5px rgba(0,0,0,0.1);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.primary-card::before { background: #3b82f6; }
.warning-card::before { background: #f59e0b; }
.success-card::before { background: #10b981; }
.danger-card::before { background: #ef4444; }

.icon {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  margin-right: 18px;
  flex-shrink: 0;
}

.primary-card .icon { background: #eff6ff; color: #3b82f6; }
.warning-card .icon { background: #fffbeb; color: #f59e0b; }
.success-card .icon { background: #ecfdf5; color: #10b981; }
.danger-card .icon { background: #fef2f2; color: #ef4444; }

.info h3 {
  margin: 0 0 8px;
  font-size: 14px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.value {
  margin: 0 0 10px;
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

.trend {
  font-size: 13px;
  font-weight: 500;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 4px;
}

.trend.up { color: #10b981; }
.trend.down { color: #ef4444; }

/* QUICK ACTIONS */
.quick-actions h2 {
  font-size: 20px;
  color: #1e293b;
  margin-bottom: 20px;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.action-btn {
  display: flex;
  align-items: center;
  padding: 16px 20px;
  background: white;
  border-radius: 16px;
  color: #334155;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  color: #0f172a;
}

.action-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  margin-right: 14px;
  transition: all 0.2s ease;
}

.action-btn:hover .action-icon {
  background: #3b82f6;
  color: white;
}
</style>
