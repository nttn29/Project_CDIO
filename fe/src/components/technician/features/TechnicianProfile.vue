<template>
  <div class="page">
    <header class="header">
      <h1>Hồ sơ kỹ thuật viên</h1>
      <p>Thông tin cá nhân và kỹ năng.</p>
    </header>

    <section class="card">
      <div class="profile">
        <div class="avatar">KT</div>
        <div>
          <h2>Nguyễn Văn An</h2>
          <p>kỹ thuật viên điện nước</p>
        </div>
      </div>

      <div class="grid">
        <div>
          <p class="label">Số điện thoại</p>
          <p class="value">0909000111</p>
        </div>
        <div>
          <p class="label">Email</p>
          <p class="value">ktvien@condomaint.vn</p>
        </div>
        <div>
          <p class="label">Kinh nghiệm</p>
          <p class="value">5 năm</p>
        </div>
        <div>
          <p class="label">Trạng thái</p>
          <p class="value">Sẵn sàng</p>
        </div>
      </div>

      <button class="edit">Chỉnh sửa hồ sơ</button>
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
  gap: 14px;
}

.profile {
  display: flex;
  gap: 12px;
  align-items: center;
}

.avatar {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: #2563eb;
  color: #fff;
  display: grid;
  place-items: center;
  font-weight: 700;
}

.profile h2 {
  margin: 0;
  font-size: 18px;
}

.profile p {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 12px;
  text-transform: capitalize;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 12px;
}

.label {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}

.value {
  margin: 4px 0 0;
  font-size: 14px;
}

.edit {
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
