<template>
  <div class="page">
    <header class="header">
      <h1>Trạng thái thợ</h1>
      <p>Cập nhật trạng thái làm việc hiện tại.</p>
    </header>

    <section class="card">
      <div class="status-row">
        <span class="dot online"></span>
        <div>
          <p class="label">Hiện tại</p>
          <p class="value">Sẵn sàng</p>
        </div>
      </div>

      <div class="buttons">
        <button class="primary">Sẵn sàng</button>
        <button class="ghost">Đang làm</button>
        <button class="ghost">Ngoại tuyến</button>
      </div>
    </section>

    <section class="card">
      <h2>Ghi chú ca trực</h2>
      <textarea placeholder="Nhập ghi chú..." rows="4"></textarea>
      <button class="save">Lưu ghi chú</button>
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

.card {
  background: #fff;
  border-radius: 14px;
  padding: 18px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
  display: grid;
  gap: 12px;
}

.status-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  display: inline-block;
}

.online {
  background: #22c55e;
}

.label {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}

.value {
  margin: 4px 0 0;
  font-size: 16px;
  font-weight: 600;
}

.buttons {
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

textarea {
  width: 100%;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 10px;
  font-size: 12px;
  resize: vertical;
}

.save {
  border: none;
  background: #0f172a;
  color: #fff;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 12px;
  cursor: pointer;
  width: fit-content;
}
</style>
