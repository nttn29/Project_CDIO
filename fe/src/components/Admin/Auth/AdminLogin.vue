<template>
  <div class="login-page">
    <div class="login-right">
      <div class="login-card">
        <div class="card-header">
          <h2>Đăng nhập quản trị</h2>
          <p>Vui lòng nhập thông tin tài khoản của bạn</p>
        </div>

        <form @submit.prevent="handleLogin">
          <div class="field" :class="{ error: errors.email }">
            <label for="email">Email</label>
            <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="admin@ecohome.vn"
                autocomplete="username"
                @focus="errors.email = ''"
              />
            </div>
            <span v-if="errors.email" class="err">{{ errors.email }}</span>
          </div>

          <div class="field" :class="{ error: errors.password }">
            <label for="password">Mật khẩu</label>
            <div class="input-group">
              <i class="fas fa-lock"></i>
              <input
                id="password"
                v-model="form.password"
                :type="showPw ? 'text' : 'password'"
                placeholder="Nhập mật khẩu"
                autocomplete="current-password"
                @focus="errors.password = ''"
              />
              <button type="button" class="pw-toggle" @click="showPw = !showPw" tabindex="-1">
                <i :class="showPw ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <span v-if="errors.password" class="err">{{ errors.password }}</span>
          </div>

          <div v-if="loginError" class="alert-err">
            <i class="fas fa-exclamation-triangle"></i>
            {{ loginError }}
          </div>

          <button type="submit" class="btn-login" :disabled="loading">
            <span v-if="loading"><i class="fas fa-spinner fa-spin"></i> Đang xử lý...</span>
            <span v-else>Đăng nhập</span>
          </button>
        </form>

        <p class="secure-note">
          <i class="fas fa-shield-alt"></i>
          Chỉ tài khoản quản trị viên mới có thể đăng nhập
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const form = reactive({ email: '', password: '' })
const errors = reactive({ email: '', password: '' })
const loginError = ref('')
const loading = ref(false)
const showPw = ref(false)

function validate() {
  errors.email = ''
  errors.password = ''
  let ok = true
  if (!form.email.trim()) { errors.email = 'Vui lòng nhập email'; ok = false }
  if (!form.password) { errors.password = 'Vui lòng nhập mật khẩu'; ok = false }
  return ok
}

async function handleLogin() {
  if (!validate()) return
  loading.value = true
  loginError.value = ''

  try {
    const res = await api.post('/login', {
      email: form.email.trim().toLowerCase(),
      mat_khau: form.password,
    })

    const data = res.data
    const userData = data.user || data

    if (!['admin', 'quan_tri', 'staff'].includes(userData.vai_tro)) {
      loginError.value = 'Tài khoản này không có quyền truy cập trang quản trị.'
      return
    }

    localStorage.setItem('admin_auth', '1')
    localStorage.setItem('admin_user', JSON.stringify(userData))

    router.push('/admin')
  } catch (err) {
    const msg = err?.response?.data?.message || err?.response?.data?.error || ''
    if (msg.toLowerCase().includes('password') || msg.toLowerCase().includes('mật khẩu')) {
      loginError.value = 'Sai mật khẩu, vui lòng thử lại.'
    } else if (msg.toLowerCase().includes('email') || msg.toLowerCase().includes('not found')) {
      loginError.value = 'Email không tồn tại trong hệ thống.'
    } else {
      loginError.value = msg || 'Đăng nhập thất bại, vui lòng thử lại.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.login-page {
  min-height: 100vh;
  font-family: 'Be Vietnam Pro', 'Segoe UI', sans-serif;
  background: #f5f6fa;
  display: flex;
}

/* ── RIGHT PANEL (full width now) ── */
.login-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
}

.login-card {
  width: 100%;
  max-width: 420px;
  background: #ffffff;
  border-radius: 16px;
  padding: 40px 36px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  border: 1px solid #e8eaf0;
}

.card-header {
  margin-bottom: 28px;
}

.card-header h2 {
  font-size: 22px;
  font-weight: 700;
  color: #1a1f36;
  margin-bottom: 6px;
}

.card-header p {
  font-size: 13px;
  color: #6b7280;
}

/* Fields */
.field {
  margin-bottom: 20px;
}

.field label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 7px;
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-group > i:first-child {
  position: absolute;
  left: 13px;
  color: #9ca3af;
  font-size: 14px;
  pointer-events: none;
}

.input-group input {
  width: 100%;
  padding: 11px 42px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  color: #1a1f36;
  background: #fafafa;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.input-group input::placeholder {
  color: #c4c9d4;
}

.input-group input:focus {
  border-color: #2563eb;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.field.error .input-group input {
  border-color: #ef4444;
}

.pw-toggle {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  cursor: pointer;
  color: #9ca3af;
  font-size: 14px;
  padding: 4px;
  transition: color 0.2s;
}
.pw-toggle:hover { color: #374151; }

.err {
  display: block;
  font-size: 12px;
  color: #ef4444;
  margin-top: 5px;
}

/* Alert */
.alert-err {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 11px 14px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  color: #dc2626;
  font-size: 13px;
  margin-bottom: 18px;
}

/* Button */
.btn-login {
  width: 100%;
  padding: 13px;
  background: #1d4ed8;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  margin-top: 4px;
}

.btn-login:hover:not(:disabled) {
  background: #1e40af;
  transform: translateY(-1px);
}

.btn-login:active:not(:disabled) {
  transform: translateY(0);
}

.btn-login:disabled {
  background: #93c5fd;
  cursor: not-allowed;
}

/* Secure note */
.secure-note {
  text-align: center;
  font-size: 12px;
  color: #9ca3af;
  margin-top: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}
</style>
