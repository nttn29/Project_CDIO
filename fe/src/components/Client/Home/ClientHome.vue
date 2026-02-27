<template>
  <div class="page">
    <!-- WELCOME -->
    <div class="welcome">
      <h1>👋 Xin chào!</h1>
      <p>Chào mừng bạn đến với EcoHome Resident 🌿</p>
    </div>

    <!-- STATS -->
    <div class="stats">
      <div class="card">
        <h3>📢 Thông báo</h3>
        <p class="big">{{ dashboard.notifications }}</p>
        <span>mới</span>
      </div>

      <div class="card">
        <h3>🛠️ Yêu cầu</h3>
        <p class="big">{{ dashboard.requests }}</p>
        <span>đang xử lý</span>
      </div>

      <div class="card">
        <h3>⭐ Đánh giá</h3>
        <p class="big">{{ dashboard.rating }}</p>
        <span>/ 5</span>
      </div>
    </div>

    <!-- PLACEHOLDER -->
    <div class="section note">
      <p>
        📌 Dữ liệu sẽ được lấy từ hệ thống khi kết nối Backend
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'

const dashboard = reactive({
  notifications: '--',
  requests: '--',
  rating: '--'
})

// 👉 Sau này BE xong chỉ cần:
// axios.get('/api/dashboard').then(res => Object.assign(dashboard, res.data))
</script>

<style scoped>
.page {
  padding: 24px;
}

.welcome {
  margin-bottom: 24px;
}

.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
  gap: 16px;
  /* keep stats centered on wide viewports */
  max-width: 760px;
  margin: 0 auto;
}

.card {
  background: #e8f4ff;
  border-radius: 16px;
  padding: 20px;
  text-align: center;
}

.big {
  font-size: 32px;
  font-weight: bold;
  margin: 8px 0;
}

.section.note {
  margin-top: 32px;
  background: #fff;
  padding: 16px;
  border-radius: 14px;
  color: #666;
  text-align: center;
}
</style>
