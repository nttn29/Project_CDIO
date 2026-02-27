<template>
  <div class="dashboard">
    <section class="stats">
      <div class="stat blue">
        <div class="stat-num">05</div>
        <div>
          <p>Công việc đang làm</p>
          <div class="bar"><span></span></div>
        </div>
      </div>
      <div class="stat orange">
        <div class="stat-num">03</div>
        <div>
          <p>Công việc chờ xử lý</p>
          <div class="bar"><span></span></div>
        </div>
      </div>
      <div class="stat red">
        <div class="stat-num">02</div>
        <div>
          <p>Cảnh báo thiết bị</p>
          <div class="bar"><span></span></div>
        </div>
      </div>
      <div class="stat green">
        <div class="stat-num">07</div>
        <div>
          <p>Bảo trì định kỳ</p>
          <div class="bar"><span></span></div>
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="panel">
        <div class="panel-header">
          <h3>Danh Sách Công Việc</h3>
          <div class="actions">
            <button class="tiny">≡</button>
            <button class="tiny">☰</button>
          </div>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Mã CV</th>
              <th>Loại công việc</th>
              <th>Thiết bị</th>
              <th>Trạng thái</th>
              <th>Hạn chót</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>CV1234</td>
              <td>Sửa chữa</td>
              <td>Máy nén khí</td>
              <td><span class="pill doing">Đang thực hiện</span></td>
              <td>12/04/2024</td>
            </tr>
            <tr>
              <td>CV1128</td>
              <td>Kiểm tra</td>
              <td>Hệ thống lạnh</td>
              <td><span class="pill wait">Chờ xử lý</span></td>
              <td>11/04/2024</td>
            </tr>
            <tr>
              <td>CV1097</td>
              <td>Bảo trì</td>
              <td>Bơm nước</td>
              <td><span class="pill doing">Đang làm</span></td>
              <td>10/04/2024</td>
            </tr>
            <tr>
              <td>CV1056</td>
              <td>Khắc phục sự cố</td>
              <td>Tủ điện</td>
              <td><span class="pill wait">Chờ xử lý</span></td>
              <td>09/04/2024</td>
            </tr>
          </tbody>
        </table>
        <button class="view-all">Xem tất cả</button>
      </div>

      <div class="panel monitor">
        <h3>Giám Sát Thiết Bị</h3>
        <div class="monitor-item warn">
          <div>
            <p>Nhiệt độ lò hơi</p>
            <strong>92°C</strong>
          </div>
          <span class="alert">Cảnh báo</span>
        </div>
        <div class="monitor-item ok">
          <div>
            <p>Áp suất máy nén</p>
            <strong>6.8 bar</strong>
          </div>
          <span class="alert">Bình thường</span>
        </div>
        <div class="monitor-item warn">
          <div>
            <p>Mức dầu bơm</p>
            <strong>LOW</strong>
          </div>
          <span class="alert">Cần kiểm tra</span>
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="panel">
        <h3>Tình Trạng Kho Vật Tư</h3>
        <div class="stock">
          <div>
            <p>Linh kiện</p>
            <div class="progress"><span style="width:45%"></span></div>
          </div>
          <div>
            <p>Vật tư thay thế</p>
            <div class="progress orange"><span style="width:30%"></span></div>
          </div>
        </div>
      </div>

      <div class="panel">
        <h3>Lịch Bảo Trì Sắp Tới</h3>
        <ul class="schedule">
          <li>15/04/2024 - Bảo trì máy bơm</li>
          <li>16/04/2024 - Kiểm tra điều hòa</li>
          <li>18/04/2024 - Thay dầu động cơ</li>
        </ul>
        <div class="quick">
          <button>Yêu cầu hỗ trợ</button>
          <button>Tài liệu hướng dẫn</button>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.dashboard {
  display: grid;
  gap: 18px;
  color: #e5e7eb;
}

.stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 12px;
}

.stat {
  background: linear-gradient(145deg, rgba(17, 24, 39, 0.9), rgba(15, 23, 42, 0.75));
  border: 1px solid rgba(148, 163, 184, 0.18);
  border-radius: 12px;
  padding: 12px;
  display: grid;
  grid-template-columns: 60px 1fr;
  gap: 10px;
  align-items: center;
  box-shadow: 0 12px 22px rgba(3, 7, 18, 0.45), inset 0 0 0 1px rgba(255, 255, 255, 0.03);
}

.stat-num {
  font-size: 24px;
  font-weight: 700;
  text-align: center;
  padding: 6px 0;
  border-radius: 8px;
  background: rgba(15, 23, 42, 0.85);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
}

.stat p {
  margin: 0 0 6px;
  font-size: 12px;
}

.bar {
  height: 6px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.1);
  overflow: hidden;
}

.bar span {
  display: block;
  height: 100%;
  width: 60%;
  background: #38bdf8;
}

.blue .stat-num { color: #38bdf8; }
.orange .stat-num { color: #f59e0b; }
.red .stat-num { color: #f87171; }
.green .stat-num { color: #4ade80; }

.grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 12px;
}

.panel {
  background: linear-gradient(160deg, rgba(17, 24, 39, 0.92), rgba(15, 23, 42, 0.8));
  border: 1px solid rgba(148, 163, 184, 0.18);
  border-radius: 12px;
  padding: 14px;
  box-shadow: 0 14px 28px rgba(3, 7, 18, 0.5), inset 0 0 0 1px rgba(255, 255, 255, 0.03);
}

.panel h3 {
  margin: 0 0 10px;
  font-size: 14px;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.actions {
  display: flex;
  gap: 6px;
}

.tiny {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  border: 1px solid rgba(148, 163, 184, 0.3);
  background: rgba(15, 23, 42, 0.6);
  color: #e5e7eb;
}

.table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}

.table th,
.table td {
  padding: 8px 6px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.16);
}

.pill {
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 11px;
}

.doing { background: #22c55e; color: #0b111b; }
.wait { background: #f59e0b; color: #0b111b; }

.view-all {
  margin-top: 10px;
  background: rgba(255, 255, 255, 0.07);
  color: #e5e7eb;
  border: 1px solid rgba(148, 163, 184, 0.24);
  padding: 6px 10px;
  border-radius: 8px;
}

.monitor-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  border-radius: 10px;
  margin-bottom: 8px;
}

.monitor-item.warn { background: rgba(239, 68, 68, 0.18); border: 1px solid rgba(239, 68, 68, 0.45); }
.monitor-item.ok { background: rgba(34, 197, 94, 0.18); border: 1px solid rgba(34, 197, 94, 0.45); }

.alert {
  padding: 4px 8px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.7);
  font-size: 11px;
}

.stock {
  display: grid;
  gap: 12px;
}

.progress {
  height: 8px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.1);
  overflow: hidden;
}

.progress span {
  display: block;
  height: 100%;
  background: #38bdf8;
}

.progress.orange span {
  background: #f97316;
}

.schedule {
  list-style: none;
  padding: 0;
  margin: 0 0 12px;
  display: grid;
  gap: 6px;
  font-size: 12px;
}

.quick {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.quick button {
  border: 1px solid rgba(148, 163, 184, 0.3);
  background: rgba(15, 23, 42, 0.6);
  color: #e5e7eb;
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 12px;
}

@media (max-width: 1100px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
</style>
