<template>
  <div class="review-page">
    <h1>⭐ Đánh giá dịch vụ</h1>
    <p class="desc">
      Chia sẻ trải nghiệm của bạn để giúp ban quản lý cải thiện chất lượng dịch vụ.
    </p>

    <div v-if="!userId" class="note">Vui lòng đăng nhập để đánh giá.</div>

    <div v-else class="layout">
      <!-- FORM -->
      <div class="card">
        <h3>Gửi đánh giá</h3>

        <label>Yêu cầu đã hoàn thành</label>
        <select v-model="form.requestId">
          <option disabled value="">-- Chọn yêu cầu --</option>
          <option v-for="r in completedRequests" :key="r.id_yeu_cau" :value="r.id_yeu_cau">
            #{{ r.id_yeu_cau }} - {{ r.loai_su_co?.ten_loai || 'Dịch vụ' }}
          </option>
        </select>

        <label>Đánh giá</label>
        <div class="stars">
          <span
            v-for="i in 5"
            :key="i"
            @click="form.rating = i"
            :class="{ active: i <= form.rating }"
          >
            ★
          </span>
        </div>

        <label>Nội dung</label>
        <textarea
          rows="4"
          placeholder="Nhập đánh giá của bạn..."
          v-model="form.comment"
        />

        <button :disabled="loading" @click="submitReview">
          {{ loading ? 'Đang gửi...' : 'Gửi đánh giá' }}
        </button>

        <p v-if="formError" class="note error">{{ formError }}</p>
        <p v-if="success" class="note success">{{ success }}</p>
      </div>

      <!-- REVIEW LIST -->
      <div class="card">
        <h3>Đánh giá gần đây</h3>

        <div v-if="loadingList" class="note">Đang tải dữ liệu...</div>
        <div v-if="listError" class="note error">{{ listError }}</div>

        <div
          v-for="item in reviews"
          :key="item.id_phan_hoi"
          class="review-item"
        >
          <div class="top">
            <strong>{{ item.cu_dan?.ten || 'Cư dân' }}</strong>
            <span class="service">{{ item.yeu_cau?.loai_su_co?.ten_loai || 'Dịch vụ' }}</span>
          </div>

          <div class="rating">
            <span
              v-for="i in 5"
              :key="i"
              :class="{ active: i <= item.danh_gia }"
            >
              ★
            </span>
          </div>

          <p>{{ item.binh_luan || 'Không có bình luận' }}</p>
        </div>
        <p v-if="!loadingList && reviews.length === 0" class="note">
          Chưa có đánh giá nào.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { user } from '@/services/api'
import api from '@/services/api'

const userId = computed(() => user.value?.id_nguoi_dung || user.value?.id || null)
const requests = ref([])

const form = ref({
  requestId: '',
  rating: 0,
  comment: '',
})

const loading = ref(false)
const loadingList = ref(false)
const formError = ref('')
const listError = ref('')
const success = ref('')

const reviews = ref([])

const reviewedRequestIds = computed(() =>
  new Set(
    (reviews.value || [])
      .map((r) => r.id_yeu_cau)
      .filter((id) => id !== null && id !== undefined)
  )
)

function isCompletedRequest(request) {
  const normalize = (value) => String(value || '').trim().toLowerCase()
  const doneStatuses = new Set(['hoan_thanh', 'completed', 'done', 'da_hoan_thanh'])
  const requestStatus = normalize(request?.trang_thai)
  const assignmentStatus = normalize(request?.phan_cong?.trang_thai)
  return doneStatuses.has(requestStatus) || doneStatuses.has(assignmentStatus)
}

const completedRequests = computed(() =>
  (requests.value || []).filter(
    (r) =>
      isCompletedRequest(r) &&
      !reviewedRequestIds.value.has(r.id_yeu_cau)
  )
)

async function loadReviews() {
  if (!userId.value) return
  loadingList.value = true
  listError.value = ''
  try {
    const response = await api.get('/phan_hoi', { params: { id_cu_dan: userId.value } })
    const allReviews = response.data || []
    reviews.value = allReviews.filter(
      (item) => String(item?.id_cu_dan) === String(userId.value)
    )
  } catch (err) {
    listError.value = err?.error || err?.message || 'Không tải được đánh giá'
  } finally {
    loadingList.value = false
  }
}

async function submitReview() {
  if (!userId.value) return
  if (!form.value.requestId || !form.value.rating) {
    formError.value = 'Vui lòng chọn yêu cầu và đánh giá'
    return
  }
  loading.value = true
  formError.value = ''
  success.value = ''
  try {
    const response = await api.post('/phan_hoi', {
      id_yeu_cau: form.value.requestId,
      id_cu_dan: userId.value,
      danh_gia: form.value.rating,
      binh_luan: form.value.comment || null,
    })
    const json = response.data
    if (!response || response.status >= 400) throw json
    form.value.comment = ''
    form.value.rating = 0
    form.value.requestId = ''
    success.value = 'Đã gửi đánh giá thành công!'
    await loadReviews()
  } catch (err) {
    if (err?.existing_id || err?.error?.includes?.('exists')) {
      formError.value = 'Yêu cầu này đã được đánh giá.'
    } else if (err?.errors) {
      const firstKey = Object.keys(err.errors)[0]
      formError.value = err.errors[firstKey]?.[0] || 'Gửi đánh giá thất bại'
    } else {
      formError.value = err?.error || err?.message || 'Gửi đánh giá thất bại'
    }
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (!userId.value) return
  try {
    const response = await api.get('/yeu-cau-bao-tri', { params: { id_cu_dan: userId.value } })
    requests.value = response.data || []
  } catch (err) {
    try {
      const fallback = await api.get('/yeu_cau', { params: { id_cu_dan: userId.value } })
      requests.value = fallback.data || []
    } catch (fallbackErr) {
      listError.value =
        fallbackErr?.error ||
        fallbackErr?.message ||
        err?.error ||
        err?.message ||
        'Không tải được yêu cầu'
    }
  }
  await loadReviews()
})
</script>

<style scoped>
.review-page {
  padding: 24px;
}

.desc {
  color: #64748b;
  margin-bottom: 24px;
}

.layout {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 24px;
}

.card {
  background: white;
  padding: 20px;
  border-radius: 18px;
  box-shadow: 0 18px 40px rgba(0,0,0,.08);
}

label {
  display: block;
  margin-top: 12px;
  margin-bottom: 6px;
  font-weight: 600;
}

select,
textarea {
  width: 100%;
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #cbd5e1;
}

.stars {
  font-size: 26px;
  cursor: pointer;
}

.stars span {
  color: #cbd5e1;
}

.stars span.active {
  color: #facc15;
}

button {
  margin-top: 16px;
  padding: 10px 18px;
  border-radius: 999px;
  background: #7dd3fc;
  border: none;
  font-weight: 600;
  cursor: pointer;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
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

/* Review list */
.review-item {
  padding: 14px 0;
  border-bottom: 1px solid #e5e7eb;
}

.review-item:last-child {
  border-bottom: none;
}

.top {
  display: flex;
  justify-content: space-between;
}

.service {
  font-size: 13px;
  color: #64748b;
}

.rating span {
  color: #cbd5e1;
}

.rating span.active {
  color: #facc15;
}
</style>
