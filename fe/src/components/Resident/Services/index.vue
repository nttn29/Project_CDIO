<template>
  <div class="page">
    <h1>🛠️ Dịch vụ cư dân</h1>

    <div v-if="!userId" class="note">
      Vui lòng đăng nhập để gửi yêu cầu.
    </div>

    <div v-else class="form-card">
      <h2>Tạo yêu cầu mới</h2>

      <div v-if="!canHoId" class="note warn">
        Tài khoản chưa được gán căn hộ. Vui lòng liên hệ quản lý.
      </div>

      <label>Dịch vụ</label>
      <select v-model="selectedIssueId" :disabled="!canHoId">
        <option disabled value="">-- Chọn dịch vụ --</option>
        <option v-for="i in issueTypes" :key="i.id_loai_su_co" :value="i.id_loai_su_co">
          {{ i.ten_loai }}
        </option>
      </select>

      <label>Mô tả</label>
      <textarea v-model="description" rows="4" placeholder="Mô tả sự cố..." :disabled="!canHoId" />

      <label>Ưu tiên</label>
      <select v-model="priority" :disabled="!canHoId">
        <option value="binh_thuong">Bình thường</option>
        <option value="gan">Khẩn</option>
        <option value="kho">Thấp</option>
      </select>

      <button :disabled="!canHoId || loading" @click="submitRequest">
        {{ loading ? 'Đang gửi...' : 'Gửi yêu cầu' }}
      </button>

      <p v-if="error" class="note error">{{ error }}</p>
      <p v-if="success" class="note success">{{ success }}</p>
    </div>

    <div class="group">
      <h2>Danh sách dịch vụ</h2>
      <div class="service-grid">
        <div class="service-card" v-for="s in issueTypes" :key="s.id_loai_su_co">
          <div class="icon">🔧</div>
          <h3>{{ s.ten_loai }}</h3>
          <p>{{ s.mo_ta || 'Dịch vụ bảo trì/hỗ trợ' }}</p>
          <button @click="selectIssue(s.id_loai_su_co)">Chọn dịch vụ</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useRequestStore } from '@/stores/requestStore'
import * as requestService from '@/api/requestService'

const userStore = useUserStore()
const requestStore = useRequestStore()

const issueTypes = ref([])
const selectedIssueId = ref('')
const description = ref('')
const priority = ref('binh_thuong')
const loading = ref(false)
const error = ref('')
const success = ref('')

const userId = computed(() => userStore.user?.id_nguoi_dung || null)
const canHoId = computed(() => userStore.user?.can_ho?.id_can_ho || null)

function selectIssue(id) {
  selectedIssueId.value = id
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function submitRequest() {
  if (!userId.value || !canHoId.value) return
  if (!selectedIssueId.value || !description.value.trim()) {
    error.value = 'Vui lòng chọn dịch vụ và nhập mô tả'
    return
  }
  loading.value = true
  error.value = ''
  success.value = ''
  try {
    await requestStore.createRequest({
      id_cu_dan: userId.value,
      id_can_ho: canHoId.value,
      id_loai_su_co: selectedIssueId.value,
      mo_ta: description.value.trim(),
      thoi_gian_uu_tien: priority.value,
    })
    description.value = ''
    priority.value = 'binh_thuong'
    success.value = 'Đã gửi yêu cầu thành công!'
  } catch (err) {
    error.value = err?.error || err?.message || 'Gửi yêu cầu thất bại'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const response = await requestService.getIssueTypes()
    issueTypes.value = response.data || []
  } catch (err) {
    error.value = err?.error || err?.message || 'Không tải được danh sách dịch vụ'
  }
})
</script>

<style scoped>
.page {
  padding: 24px;
}

.form-card {
  background: #fff;
  border-radius: 18px;
  padding: 22px;
  box-shadow: 0 12px 30px rgba(0,0,0,.08);
  margin-bottom: 32px;
}

.group {
  margin-bottom: 40px;
}

h2 {
  margin-bottom: 16px;
  color: #0f172a;
}

label {
  display: block;
  margin: 10px 0 6px;
  font-weight: 600;
}

select,
textarea {
  width: 100%;
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #cbd5e1;
  background: white;
}

button {
  margin-top: 14px;
  padding: 10px 18px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #6ec1ff, #3b82f6);
  color: white;
  font-weight: 600;
  cursor: pointer;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.service-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 20px;
}

.service-card {
  background: white;
  border-radius: 18px;
  padding: 22px;
  box-shadow: 0 12px 30px rgba(0,0,0,.08);
  transition: .25s;
}

.service-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 22px 45px rgba(0,0,0,.12);
}

.icon {
  font-size: 34px;
}

.note {
  margin-top: 10px;
  font-size: 13px;
  color: #94a3b8;
  font-style: italic;
}

.note.error {
  color: #dc2626;
}

.note.success {
  color: #16a34a;
}

.note.warn {
  color: #b45309;
}
</style>
