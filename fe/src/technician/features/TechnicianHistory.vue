<template>
  <div class="page">
    <header class="header">
      <h1>Lịch sử công việc</h1>
      <p>Tổng hợp các công việc đã xử lý.</p>
    </header>

    <section class="list">
      <article class="item">
        <div>
          <h3>JOB-0001</h3>
          <p>Kiểm tra máy bơm tầng hầm</p>
        </div>
        <span class="tag done">Hoàn thành</span>
      </article>

      <article class="item">
        <div>
          <h3>JOB-0002</h3>
          <p>Thay bóng đèn hành lang</p>
        </div>
        <span class="tag done">Hoàn thành</span>
      </article>
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

.list {
  display: grid;
  gap: 10px;
}

.item {
  background: #fff;
  border-radius: 14px;
  padding: 14px 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.item h3 {
  margin: 0;
  font-size: 14px;
}

.item p {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 12px;
}

.tag {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

.done {
  background: #dcfce7;
  color: #15803d;
}
</style>
