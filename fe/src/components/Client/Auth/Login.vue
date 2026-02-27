<template>
  <div class="wrap">
    <section class="card">
      <!-- AVATAR MÈO -->
      <div class="avatar">
        <svg viewBox="0 0 220 220" class="pet">
          <circle cx="110" cy="110" r="90" fill="#ffd3e2" stroke="#a43a66" stroke-width="6"/>

          <!-- HEAD -->
          <g :style="{ transform: `rotate(${headRotate}deg)` }">
            <path d="M70 90
              Q110 52 150 90
              Q168 110 158 140
              Q150 168 110 172
              Q70 168 62 140
              Q52 110 70 90 Z"
              fill="#fff5f8" stroke="#a43a66" stroke-width="5"/>

            <!-- EYES -->
            <ellipse cx="92" cy="118" rx="16" ry="12" fill="#fff"/>
            <ellipse cx="128" cy="118" rx="16" ry="12" fill="#fff"/>

            <circle :cx="92 + eyeOffset" cy="118" r="5" fill="#a43a66"/>
            <circle :cx="128 + eyeOffset" cy="118" r="5" fill="#a43a66"/>

            <!-- NOSE -->
            <path d="M110 132 l-7 6 q7 6 14 0 Z"
              fill="#ff7fb0" stroke="#a43a66" stroke-width="3"/>
          </g>

          <!-- HANDS CHE MẮT -->
          <g v-if="coverEyes">
            <path d="M50 150 Q80 120 110 130 Q80 160 50 150 Z"
              fill="#fff5f8" stroke="#a43a66" stroke-width="4"/>
            <path d="M170 150 Q140 120 110 130 Q140 160 170 150 Z"
              fill="#fff5f8" stroke="#a43a66" stroke-width="4"/>
          </g>
        </svg>
      </div>

      <h1>EcoHome Login</h1>
      <p class="subtitle">Đăng nhập cư dân</p>

      <form @submit.prevent="login" autocomplete="off">
        <label>Email</label>
        <input
          v-model="email"
          type="email"
          name="resident_email"
          autocomplete="off"
          autocapitalize="none"
          spellcheck="false"
          placeholder="resident@ecohome.vn"
          @input="handleEmail"
          @focus="coverEyes = false"
          required
        />

        <label>Mật khẩu</label>
        <input
          v-model="password"
          :type="showPassword ? 'text' : 'password'"
          name="resident_password"
          autocomplete="new-password"
          placeholder="••••••••"
          @focus="coverEyes = true"
          @blur="coverEyes = false"
          required
        />

        <label class="toggle">
          <input v-model="showPassword" type="checkbox" />
          Hiện mật khẩu
        </label>

        <button :disabled="loading">{{ loading ? 'Đang đăng nhập...' : 'Đăng nhập' }}</button>

        <p v-if="error" class="error">{{ error }}</p>

        <p class="register-text">
          Chưa có tài khoản?
          <router-link to="/Client/register">Đăng ký</router-link>
        </p>
      </form>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login as apiLogin, user } from '@/services/api'
import api from '@/services/api'

const router = useRouter()

const email = ref('')
const password = ref('')

const eyeOffset = ref(0)
const headRotate = ref(0)
const coverEyes = ref(false)
const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

function handleEmail() {
  const len = email.value.length
  eyeOffset.value = Math.min(len * 1.1, 8)
  headRotate.value = Math.min(len * 0.6, 6)
}

async function login() {
  loading.value = true
  error.value = ''
  try {
    const normalizedEmail = email.value.trim().toLowerCase()
    await apiLogin(normalizedEmail, password.value)
    // user and token are set by api.login
    router.push('/')
  } catch (err) {
    const rawMessage =
      err?.error ||
      err?.message ||
      err?.response?.data?.error ||
      err?.response?.data?.message ||
      ''

    if (rawMessage) {
      const lower = rawMessage.toLowerCase()
      if (lower.includes('email not found') || lower.includes('email không tồn tại')) {
        error.value = 'Email không tồn tại'
      } else if (lower.includes('wrong password') || lower.includes('sai mật khẩu')) {
        error.value = 'Sai mật khẩu'
      } else if (lower.includes('invalid email or password')) {
        error.value = 'Sai mật khẩu'
      } else {
        error.value = rawMessage || 'Đăng nhập thất bại'
      }
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.wrap {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background:
    radial-gradient(circle at top, #dbeafe, transparent 60%),
    linear-gradient(135deg, #e0f2fe, #f8fafc);
}

.card {
  width: 460px;
  background: #fff;
  border-radius: 22px;
  padding: 36px;
  text-align: center;
  box-shadow: 0 30px 70px rgba(164, 58, 102, 0.25);
}

.pet {
  width: 160px;
}

h1 {
  margin-top: 10px;
  color: #7a244b;
  font-weight: 800;
}

.subtitle {
  margin-bottom: 22px;
  font-size: 14px;
  color: #64748b;
}

form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

label {
  text-align: left;
  font-size: 14px;
  font-weight: 600;
  color: #7a244b;
}

input {
  padding: 14px 16px;
  border-radius: 14px;
  border: 1.5px solid #f0b7cf;
  font-size: 14px;
  color: #333;
  background: #fff;
}

input:focus {
  outline: none;
  border-color: #a43a66;
  box-shadow: 0 0 0 4px rgba(164, 58, 102, 0.15);
}

button {
  margin-top: 14px;
  padding: 14px;
  border-radius: 16px;
  border: none;
  background: linear-gradient(135deg, #ff7fb0, #a43a66);
  color: white;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.register-text {
  margin-top: 18px;
  font-size: 14px;
  color: #475569;
}

.register-text a {
  color: #a43a66;
  font-weight: 700;
  text-decoration: none;
}

.register-text a:hover {
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
}

.toggle input {
  width: 16px;
  height: 16px;
}
</style>
