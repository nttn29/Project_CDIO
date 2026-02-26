<template>
  <div class="tech-home">
    <header class="hero">
      <div class="hero-content">
        <p class="badge">Technician Center</p>
        <h1>Trang chủ kỹ thuật viên</h1>
        <p class="subtitle">
          Theo dõi công việc, lịch hẹn và trạng thái xử lý trong ngày.
        </p>
      </div>
    </header>

    <section class="stats">
      <div class="stat-card">
        <p class="stat-label">Công việc hôm nay</p>
        <p class="stat-value">0</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Đang xử lý</p>
        <p class="stat-value">0</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Hoàn thành</p>
        <p class="stat-value">0</p>
      </div>
    </section>

    <section class="panel">
      <h2>Lịch hẹn gần nhất</h2>
      <p class="empty">Chưa có lịch hẹn.</p>
    </section>

    <section class="panel">
      <h2>Yêu cầu mới</h2>
      <p class="empty">Chưa có yêu cầu mới.</p>
    </section>
  </div>
</template>

<style scoped>
.tech-home {
  padding: 24px;
  display: grid;
  gap: 20px;
  font-family: "Poppins", "Segoe UI", Tahoma, Arial, sans-serif;
  color: #0f172a;
}

.hero {
  background: linear-gradient(135deg, #0f4cff 0%, #15c1ff 100%);
  color: #fff;
  border-radius: 18px;
  padding: 28px;
}

.hero-content {
  max-width: 520px;
}

.badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.2);
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 12px;
  letter-spacing: 0.3px;
}

.subtitle {
  margin-top: 8px;
  opacity: 0.9;
}

.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
}

.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.stat-label {
  margin: 0;
  color: #64748b;
  font-size: 13px;
}

.stat-value {
  margin: 6px 0 0;
  font-size: 26px;
  font-weight: 700;
}

.panel {
  background: #fff;
  border-radius: 14px;
  padding: 18px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.panel h2 {
  margin: 0 0 6px;
  font-size: 16px;
}

.empty {
  margin: 0;
  color: #94a3b8;
  font-size: 13px;
}
</style>
