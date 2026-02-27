<template>
  <div class="page">
    <header class="header">
      <h1>Lịch hẹn</h1>
      <p>Theo dõi lịch làm việc trong tuần.</p>
    </header>

    <section class="calendar">
      <div class="day">
        <h3>Hôm nay</h3>
        <p>24/02/2026</p>
        <div class="slot">
          <span>08:30</span>
          <span>Kiểm tra máy bơm tầng hầm</span>
        </div>
        <div class="slot">
          <span>10:15</span>
          <span>Thay bóng đèn hành lang</span>
        </div>
      </div>

      <div class="day">
        <h3>Ngày mai</h3>
        <p>25/02/2026</p>
        <p class="empty">Chưa có lịch.</p>
      </div>
    </section>
  </div>
</template>

<style scoped>
.page {
  display: grid;
  gap: 16px;
  color: #0f172a;
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

.calendar {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 12px;
}

.day {
  background: #fff;
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.day h3 {
  margin: 0;
  font-size: 16px;
}

.day p {
  margin: 6px 0 0;
  color: #64748b;
  font-size: 12px;
}

.slot {
  margin-top: 10px;
  padding: 10px;
  border-radius: 10px;
  background: #eff6ff;
  display: grid;
  gap: 4px;
  font-size: 12px;
}

.empty {
  margin-top: 10px;
  color: #94a3b8;
  font-size: 12px;
}
</style>
