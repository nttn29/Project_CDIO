<template>
  <div class="auth-wrapper">
    <div class="card">
      <!-- AVATAR CAT -->
      <div class="avatar">
        <svg viewBox="0 0 220 220" class="pet">
          <circle cx="110" cy="110" r="90" fill="#ffd3e2" stroke="#a43a66" stroke-width="6"/>

          <!-- HEAD -->
          <g id="head">
            <path d="M64 78 Q70 50 92 62 Q86 84 64 78 Z"
              fill="#fff5f8" stroke="#a43a66" stroke-width="4"/>
            <path d="M156 78 Q150 50 128 62 Q134 84 156 78 Z"
              fill="#fff5f8" stroke="#a43a66" stroke-width="4"/>

            <path d="M70 90 Q110 52 150 90
                     Q168 110 158 140
                     Q150 168 110 172
                     Q70 168 62 140
                     Q52 110 70 90 Z"
              fill="#fff5f8" stroke="#a43a66" stroke-width="5"/>

            <ellipse cx="78" cy="128" rx="14" ry="10" fill="#ffb3cd"/>
            <ellipse cx="142" cy="128" rx="14" ry="10" fill="#ffb3cd"/>

            <ellipse cx="92" cy="118" rx="16" ry="12" fill="#fff"/>
            <ellipse cx="128" cy="118" rx="16" ry="12" fill="#fff"/>

            <circle cx="92" cy="118" r="5" fill="#a43a66"/>
            <circle cx="128" cy="118" r="5" fill="#a43a66"/>

            <path d="M110 132 l-7 6 q7 6 14 0 Z"
              fill="#ff7fb0" stroke="#a43a66" stroke-width="3"/>

            <path d="M102 142 Q110 150 118 142"
              fill="none" stroke="#a43a66" stroke-width="4"
              stroke-linecap="round"/>
          </g>
        </svg>
      </div>

      <h2>Đăng ký</h2>
      <p class="subtitle">Tạo tài khoản EcoHome Resident</p>

      <form @submit.prevent="register">
        <input v-model="name" type="text" placeholder="Họ và tên" required />
        <input v-model="email" type="email" placeholder="Email" required />
        <input v-model="phone" type="text" placeholder="Số điện thoại" />

        <label>Căn hộ</label>
        <select v-model="apartmentId" required>
          <option disabled value="">-- Chọn căn hộ --</option>
          <option v-for="a in apartments" :key="a.id_can_ho" :value="a.id_can_ho">
            Căn {{ a.so_can_ho }} - Tầng {{ a.tang }}
          </option>
        </select>

        <input v-model="password" :type="showPassword ? 'text' : 'password'" placeholder="Mật khẩu" required />
        <input v-model="passwordConfirm" :type="showPassword ? 'text' : 'password'" placeholder="Nhập lại mật khẩu" required />

        <label class="toggle">
          <input v-model="showPassword" type="checkbox" />
          Hiện mật khẩu
        </label>

        <button :disabled="loading">{{ loading ? 'Đang đăng ký...' : 'Đăng ký' }}</button>

        <p v-if="error" class="error">{{ error }}</p>

        <p class="footer-text">
          Đã có tài khoản?
          <router-link to="/Client/login">Đăng nhập</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { register as apiRegister, user } from '@/services/api'
import api from '@/services/api'

const router = useRouter()

const name = ref('')
const email = ref('')
const phone = ref('')
const apartmentId = ref('')
const password = ref('')
const passwordConfirm = ref('')
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')
const apartments = ref([])

function translateError(message) {
  if (!message) return ''
  const lower = message.toLowerCase()
  if (lower.includes('email already registered')) {
    return 'Email đã được sử dụng'
  }
  if (lower.includes('the email has already been taken')) {
    return 'Email đã được sử dụng'
  }
  if (lower.includes('password confirmation does not match')) {
    return 'Xác nhận mật khẩu không khớp'
  }
  if (lower.includes('password must be at least')) {
    return 'Mật khẩu phải có ít nhất 6 ký tự'
  }
  return message
}

async function loadApartments() {
  try {
    const response = await api.get('/can-ho/available')
    apartments.value = response.data || []
  } catch (err) {
    error.value = err?.response?.data?.message || err?.message || 'Không tải được danh sách căn hộ'
  }
}

async function register() {
  loading.value = true
  error.value = ''
  try {
    const normalizedEmail = email.value.trim().toLowerCase()
    await apiRegister({
      email: normalizedEmail,
      ten: name.value,
      dien_thoai: phone.value || null,
      id_can_ho: apartmentId.value,
      mat_khau: password.value,
      mat_khau_confirmation: passwordConfirm.value,
    })
    // authService sets token and user
    router.push('/Client/home')
  } catch (err) {
    const errors = err?.response?.data?.errors || err?.errors
    if (errors) {
      const firstKey = Object.keys(errors)[0]
      const message = errors[firstKey]?.[0] || err?.response?.data?.message
      error.value = translateError(message) || 'Đăng ký thất bại'
    } else {
      const message = err?.response?.data?.message || err?.error || err?.message
      error.value = translateError(message) || 'Đăng ký thất bại'
    }
  } finally {
    loading.value = false
  }
}

onMounted(loadApartments)
</script>

<style scoped>
/* BACKGROUND */
.auth-wrapper {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background:
    radial-gradient(circle at top, #dbeafe, transparent 60%),
    linear-gradient(135deg, #e0f2fe, #f8fafc);
}

/* CARD */
.card {
  width: 460px;
  padding: 36px;
  background: #fff;
  border-radius: 22px;
  box-shadow: 0 30px 70px rgba(164, 58, 102, 0.25);
  text-align: center;
  animation: fadeUp .6s ease;
}

/* CAT */
.avatar {
  display: flex;
  justify-content: center;
  margin-bottom: 12px;
}

.pet {
  width: 160px;
}

/* TITLE */
h2 {
  font-size: 26px;
  font-weight: 800;
  margin-bottom: 6px;
  color: #7a244b;
}

.subtitle {
  font-size: 14px;
  color: #64748b;
  margin-bottom: 24px;
}

/* INPUT */
input,
select {
  width: 100%;
  padding: 14px 16px;
  margin-bottom: 16px;
  border-radius: 14px;
  border: 1.5px solid #f0b7cf;
  font-size: 15px;
  background: #fff;
  color: #333;
}

input:focus,
select:focus {
  outline: none;
  border-color: #a43a66;
  box-shadow: 0 0 0 4px rgba(164, 58, 102, 0.15);
}

/* BUTTON */
button {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #ff7fb0, #a43a66);
  border: none;
  border-radius: 14px;
  color: #fff;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* FOOTER */
.footer-text {
  margin-top: 18px;
  font-size: 14px;
  color: #475569;
}

.footer-text a {
  color: #a43a66;
  font-weight: 700;
  text-decoration: none;
}

.footer-text a:hover {
  text-decoration: underline;
}

.error {
  margin-top: 10px;
  color: #dc2626;
  font-size: 13px;
}

.toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #475569;
  user-select: none;
  margin-bottom: 16px;
}

.toggle input {
  width: 16px;
  height: 16px;
}

/* ANIM */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
