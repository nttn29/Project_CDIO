<template>
  <div class="page">
    <!-- HERO SECTION -->
    <div class="hero">
      <div class="hero-content">
        <h1>👋 Xin chào!</h1>
        <p>Chào mừng bạn đến với <strong>EcoHome Resident</strong> 🌿</p>
        <span class="badgee">Không gian sống hoàn hảo</span>
      </div>
    </div>

    <!-- STATS -->
    <div class="stats">
      <div class="card stat-card">
        <div class="icon-wrap noti-icon">
          <i class="fas fa-bell"></i>
        </div>
        <div class="info">
          <h3>Thông báo</h3>
          <p class="big">{{ dashboard.notifications }} <span class="unit">mới</span></p>
        </div>
      </div>

      <div class="card stat-card">
        <div class="icon-wrap req-icon">
          <i class="fas fa-tools"></i>
        </div>
        <div class="info">
          <h3>Yêu cầu</h3>
          <p class="big">{{ dashboard.requests }} <span class="unit">đang xử lý</span></p>
        </div>
      </div>

      <div class="card stat-card">
        <div class="icon-wrap star-icon">
          <i class="fas fa-star"></i>
        </div>
        <div class="info">
          <h3>Đánh giá</h3>
          <p class="big">{{ dashboard.rating }} <span class="unit">/ 5</span></p>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { reactive } from 'vue'

const dashboard = reactive({
  notifications: '2',
  requests: '1',
  rating: '4.8'
})

</script>

<style scoped>
.page {
  padding: 30px;
  max-width: 1200px;
  margin: 0 auto;
}

/* HERO */
.hero {
  background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
  border-radius: 24px;
  padding: 40px;
  color: white;
  margin-bottom: 40px;
  box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.4);
  position: relative;
  overflow: hidden;
}

.hero::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background: url('https://www.transparenttextures.com/patterns/cubes.png');
  opacity: 0.1;
  pointer-events: none;
}

.hero-content h1 {
  font-size: 42px;
  margin: 0 0 10px;
  font-weight: 800;
  text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hero-content p {
  font-size: 18px;
  opacity: 0.9;
  margin-bottom: 20px;
}

.badgee {
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.5px;
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
  padding: 24px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #f1f5f9;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.icon-wrap {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin-right: 20px;
}

.noti-icon {
  background: #fef3c7;
  color: #d97706;
}

.req-icon {
  background: #dbeafe;
  color: #2563eb;
}

.star-icon {
  background: #dcfce7;
  color: #16a34a;
}

.info h3 {
  margin: 0;
  font-size: 15px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.big {
  margin: 4px 0 0;
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
}

.unit {
  font-size: 14px;
  font-weight: 500;
  color: #94a3b8;
}

/* Section Notes removed in favor of a cleaner look */
</style>
