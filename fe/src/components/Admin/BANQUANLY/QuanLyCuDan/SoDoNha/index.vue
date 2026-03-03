<template>
  <div class="page">
    <div class="building">
      <h1>Luxury Apartment Tower</h1>

      <!-- FLOORS -->
      <div v-for="floor in floors" :key="floor.level" class="floor">
        <div class="floor-label">Tầng {{ floor.level }}</div>

        <div class="units">
          <div
            v-for="room in floor.rooms"
            :key="room.code"
            class="unit"
            :class="{
              empty: !room.occupied,
              normal:
                room.occupied && room.requestCount === 0 && !room.scheduled,
              request: room.requestCount > 0 && !room.scheduled,
              scheduled: room.scheduled,
            }"
            @click="goToRequest(room.code)"
          >
            {{ room.code }}

            <!-- BADGE -->
            <span v-if="room.requestCount > 0 && !room.scheduled" class="badge">
              {{ room.requestCount }}
            </span>
          </div>
        </div>
      </div>

      <!-- LEGEND -->
      <div class="legend">
        <h3>Ghi chú</h3>
        <div class="legend-row">
          <div class="legend-item">
            <span class="demo empty"></span>
            Căn trống
          </div>

          <div class="legend-item">
            <span class="demo normal"></span>
            Bình thường
          </div>

          <div class="legend-item">
            <span class="demo request">
              <small>1</small>
            </span>
            Có yêu cầu
          </div>

          <div class="legend-item">
            <span class="demo scheduled"></span>
            Đã xác nhận / lên lịch
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SoDoCanHo",
  data() {
    return {
      floors: this.generateFloors(),
    };
  },

  mounted() {
    // DEMO TEST
    this.addRequest("A1001");
    this.addRequest("A903");
    this.confirmAndSchedule("A802");

    // DEMO căn trống
    this.setEmpty("A1002");
    this.setEmpty("A704");
  },

  methods: {
    generateFloors() {
      const floors = [];

      for (let i = 10; i >= 1; i--) {
        const rooms = [];

        for (let j = 1; j <= 6; j++) {
          rooms.push({
            code: `A${i}${j.toString().padStart(2, "0")}`,
            requestCount: 0,
            scheduled: false,
            occupied: true, // 👉 THÊM: mặc định là căn bình thường
          });
        }

        floors.push({
          level: i,
          rooms,
        });
      }

      return floors;
    },

    goToRequest(roomCode) {
      this.$router.push(`/admin/bao-tri/yeu-cau?room=${roomCode}`);
    },

    addRequest(roomCode) {
      const room = this.findRoom(roomCode);
      if (room && !room.scheduled) {
        room.requestCount = 1;
      }
    },

    confirmAndSchedule(roomCode) {
      const room = this.findRoom(roomCode);
      if (room) {
        room.requestCount = 0;
        room.scheduled = true;
      }
    },

    // 👉 THÊM: set căn trống
    setEmpty(roomCode) {
      const room = this.findRoom(roomCode);
      if (room) {
        room.occupied = false;
        room.requestCount = 0;
        room.scheduled = false;
      }
    },

    findRoom(roomCode) {
      for (const floor of this.floors) {
        const room = floor.rooms.find((r) => r.code === roomCode);
        if (room) return room;
      }
      return null;
    },
  },
};
</script>

<style scoped>
/* ===== PAGE ===== */
.page {
  min-height: 100vh;
  background: linear-gradient(135deg, #eef1f6, #f8f9fc);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 32px;
  padding-bottom: 32px;
  font-family: "Segoe UI", sans-serif;
}

/* ===== BUILDING ===== */
.building {
  background: #ffffff;
  padding: 36px 44px;
  border-radius: 18px;
  width: 900px;
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
}

h1 {
  text-align: center;
  margin-bottom: 32px;
  font-size: 26px;
  letter-spacing: 1px;
}

/* ===== FLOOR ===== */
.floor {
  display: grid;
  grid-template-columns: 90px 1fr;
  align-items: center;
  margin-bottom: 16px;
}

.floor-label {
  font-weight: 600;
  color: #444;
}

/* ===== UNITS ===== */
.units {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 14px;
}

.unit {
  height: 44px;
  border-radius: 10px;
  border: 1.5px solid #333;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.25s ease;
  position: relative;
}

/* ===== STATES ===== */
.unit.empty {
  background: #ffffff;
  color: #333;
}

.unit.normal {
  background: #8e8989;
  color: #f9f9f9;
}

.unit.request {
  background: #e74c3c;
  color: #fff;
}

.unit.scheduled {
  background: #039631;
  color: #ffffff;
}

.unit:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.18);
}

/* ===== BADGE ===== */
.badge {
  position: absolute;
  top: -7px;
  right: -7px;
  width: 18px;
  height: 18px;
  background: #ff3b3b;
  color: #fff;
  border-radius: 50%;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
}

/* ===== LEGEND ===== */
.legend {
  margin-top: 40px;
  padding-top: 24px;
  border-top: 1px dashed #ccc;
}

.legend h3 {
  margin-bottom: 16px;
}

.legend-row {
  display: flex;
  gap: 28px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.demo {
  width: 26px;
  height: 26px;
  border-radius: 6px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 11px;
  font-weight: bold;
  border: 1px solid #333;
}

.demo.empty {
  background: #ffffff;
}

.demo.normal {
  background: #8e8989;
}

.demo.request {
  background: #e74c3c;
  color: #fff;
}

.demo.scheduled {
  background: #039631;
}
</style>
