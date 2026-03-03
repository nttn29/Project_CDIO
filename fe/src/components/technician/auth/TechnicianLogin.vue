<template>
  <div class="auth-page">
    <div class="card">
      <div class="brand">
        <div class="logo">CM</div>
        <div>
          <h1>Kỹ thuật viên</h1>
          <p>{{ mode === "login" ? "Đăng nhập hệ thống" : "Tạo tài khoản kỹ thuật viên" }}</p>
        </div>
      </div>

      <div class="switcher">
        <button
          type="button"
          :class="['tab', mode === 'login' && 'active']"
          @click="setMode('login')"
        >
          Đăng nhập
        </button>
        <button
          type="button"
          :class="['tab', mode === 'register' && 'active']"
          @click="setMode('register')"
        >
          Đăng ký
        </button>
      </div>

      <form v-if="mode === 'login'" class="form" @submit.prevent="onLogin">
        <label class="field">
          <span>Tên đăng nhập / Email / Số điện thoại</span>
          <input
            v-model.trim="loginForm.identifier"
            type="text"
            placeholder="nguyenvana / email@example.com / 0909000111"
            :class="{ invalid: loginTouched.identifier && loginErrors.identifier }"
            @blur="loginTouched.identifier = true"
          />
          <small v-if="loginTouched.identifier && loginErrors.identifier" class="error">
            {{ loginErrors.identifier }}
          </small>
        </label>

        <label class="field">
          <span>Mật khẩu</span>
          <div class="password-row">
            <input
              v-model="loginForm.password"
              :type="showLoginPassword ? 'text' : 'password'"
              placeholder="••••••"
              :class="{ invalid: loginTouched.password && loginErrors.password }"
              @blur="loginTouched.password = true"
            />
            <button type="button" class="ghost" @click="showLoginPassword = !showLoginPassword">
              {{ showLoginPassword ? "Ẩn" : "Hiện" }}
            </button>
          </div>
          <small v-if="loginTouched.password && loginErrors.password" class="error">
            {{ loginErrors.password }}
          </small>
        </label>

        <button class="primary" type="submit" :disabled="submitting">
          {{ submitting ? "Đang xử lý..." : "Đăng nhập" }}
        </button>
        <p v-if="formMessage" class="message">{{ formMessage }}</p>
      </form>

      <form v-else class="form" @submit.prevent="onRegister">
        <label class="field">
          <span>Họ và tên</span>
          <input
            v-model.trim="registerForm.name"
            type="text"
            placeholder="Nguyễn Văn A"
            :class="{ invalid: registerTouched.name && registerErrors.name }"
            @blur="registerTouched.name = true"
          />
          <small v-if="registerTouched.name && registerErrors.name" class="error">{{ registerErrors.name }}</small>
        </label>

        <label class="field">
          <span>Tên đăng nhập</span>
          <input
            v-model.trim="registerForm.username"
            type="text"
            placeholder="nguyenvana"
            :class="{ invalid: registerTouched.username && registerErrors.username }"
            @blur="registerTouched.username = true"
          />
          <small v-if="registerTouched.username && registerErrors.username" class="error">
            {{ registerErrors.username }}
          </small>
        </label>

        <label class="field">
          <span>Email</span>
          <input
            v-model.trim="registerForm.email"
            type="email"
            placeholder="email@example.com"
            :class="{ invalid: registerTouched.email && registerErrors.email }"
            @blur="registerTouched.email = true"
          />
          <small v-if="registerTouched.email && registerErrors.email" class="error">{{ registerErrors.email }}</small>
        </label>

        <label class="field">
          <span>Số điện thoại</span>
          <input
            v-model.trim="registerForm.phone"
            type="tel"
            placeholder="0909000111"
            :class="{ invalid: registerTouched.phone && registerErrors.phone }"
            @blur="registerTouched.phone = true"
          />
          <small v-if="registerTouched.phone && registerErrors.phone" class="error">{{ registerErrors.phone }}</small>
        </label>

        <label class="field">
          <span>Mật khẩu</span>
          <div class="password-row">
            <input
              v-model="registerForm.password"
              :type="showRegisterPassword ? 'text' : 'password'"
              placeholder="••••••"
              :class="{ invalid: registerTouched.password && registerErrors.password }"
              @blur="registerTouched.password = true"
            />
            <button type="button" class="ghost" @click="showRegisterPassword = !showRegisterPassword">
              {{ showRegisterPassword ? "Ẩn" : "Hiện" }}
            </button>
          </div>
          <small v-if="registerTouched.password && registerErrors.password" class="error">
            {{ registerErrors.password }}
          </small>
        </label>

        <label class="field">
          <span>Nhập lại mật khẩu</span>
          <div class="password-row">
            <input
              v-model="registerForm.confirm"
              :type="showRegisterConfirm ? 'text' : 'password'"
              placeholder="••••••"
              :class="{ invalid: registerTouched.confirm && registerErrors.confirm }"
              @blur="registerTouched.confirm = true"
            />
            <button type="button" class="ghost" @click="showRegisterConfirm = !showRegisterConfirm">
              {{ showRegisterConfirm ? "Ẩn" : "Hiện" }}
            </button>
          </div>
          <small v-if="registerTouched.confirm && registerErrors.confirm" class="error">
            {{ registerErrors.confirm }}
          </small>
        </label>

        <button class="primary" type="submit" :disabled="submitting">
          {{ submitting ? "Đang xử lý..." : "Đăng ký" }}
        </button>
        <p v-if="formMessage" class="message">{{ formMessage }}</p>
      </form>

      <div class="links">
        <a v-if="mode === 'login'" href="#" @click.prevent="setMode('register')">Chưa có tài khoản? Đăng ký</a>
        <a v-else href="#" @click.prevent="setMode('login')">Đã có tài khoản? Đăng nhập</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import api from '@/services/api';

const mode = ref("login");
const submitting = ref(false);

const loginForm = reactive({
  identifier: "",
  password: "",
});

const registerForm = reactive({
  name: "",
  username: "",
  email: "",
  phone: "",
  password: "",
  confirm: "",
});

const loginTouched = reactive({
  identifier: false,
  password: false,
});

const registerTouched = reactive({
  name: false,
  username: false,
  email: false,
  phone: false,
  password: false,
  confirm: false,
});

const showLoginPassword = ref(false);
const showRegisterPassword = ref(false);
const showRegisterConfirm = ref(false);
const formMessage = ref("");

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phoneRegex = /^0\d{9,10}$/;
const usernameRegex = /^[a-zA-Z0-9._]{4,20}$/;
const nameRegex = /^[\p{L} ]{2,}$/u;

const loginErrors = reactive({
  identifier: "",
  password: "",
});

const registerErrors = reactive({
  name: "",
  username: "",
  email: "",
  phone: "",
  password: "",
  confirm: "",
});

function setMode(nextMode) {
  mode.value = nextMode;
  formMessage.value = "";
}

function validateLoginIdentifier(value) {
  if (!value) return "Vui lòng nhập email hoặc số điện thoại.";
  if (value.includes("@")) {
    return emailRegex.test(value) ? "" : "Email không hợp lệ.";
  }
  return phoneRegex.test(value) ? "" : "Số điện thoại không hợp lệ (bắt đầu bằng 0, 10-11 số).";
}

function validateLogin() {
  loginErrors.identifier = validateLoginIdentifier(loginForm.identifier);
  loginErrors.password = !loginForm.password
    ? "Vui lòng nhập mật khẩu."
    : loginForm.password.length < 6
      ? "Mật khẩu tối thiểu 6 ký tự."
      : "";

  return !loginErrors.identifier && !loginErrors.password;
}

function validateRegister() {
  registerErrors.name = !registerForm.name
    ? "Vui lòng nhập họ và tên."
    : nameRegex.test(registerForm.name)
      ? ""
      : "Họ tên chỉ gồm chữ cái và khoảng trắng.";

  registerErrors.username = !registerForm.username
    ? "Vui lòng nhập tên đăng nhập."
    : usernameRegex.test(registerForm.username)
      ? ""
      : "Tên đăng nhập 4-20 ký tự, chỉ gồm chữ, số, . hoặc _.";

  registerErrors.email = !registerForm.email
    ? "Vui lòng nhập email."
    : emailRegex.test(registerForm.email)
      ? ""
      : "Email không hợp lệ.";

  registerErrors.phone = !registerForm.phone
    ? "Vui lòng nhập số điện thoại."
    : phoneRegex.test(registerForm.phone)
      ? ""
      : "Số điện thoại không hợp lệ (bắt đầu bằng 0, 10-11 số).";

  registerErrors.password = !registerForm.password
    ? "Vui lòng nhập mật khẩu."
    : registerForm.password.length < 6
      ? "Mật khẩu tối thiểu 6 ký tự."
      : "";

  registerErrors.confirm = !registerForm.confirm
    ? "Vui lòng nhập lại mật khẩu."
    : registerForm.confirm !== registerForm.password
      ? "Mật khẩu nhập lại không khớp."
      : "";

  return (
    !registerErrors.name &&
    !registerErrors.username &&
    !registerErrors.email &&
    !registerErrors.phone &&
    !registerErrors.password &&
    !registerErrors.confirm
  );
}

async function onLogin() {
  loginTouched.identifier = true;
  loginTouched.password = true;
  formMessage.value = "";

  if (!validateLogin()) return;

  try {
    submitting.value = true;
    const res = await api.post("/technician/login", {
      identifier: loginForm.identifier,
      password: loginForm.password,
    });
    
    const data = res.data;
    localStorage.setItem("tech_auth", "1");
    // Some APIs drop data inside data.data or data.user
    localStorage.setItem("tech_user", JSON.stringify(data?.data || data?.user || null));
    window.location.href = "/technician";
  } catch (err) {
    const data = err.response?.data;
    if (data) {
       formMessage.value = data?.message || data?.errors?.identifier?.[0] || "Đăng nhập thất bại.";
    } else {
       formMessage.value = "Không kết nối được server.";
    }
  } finally {
    submitting.value = false;
  }
}

async function onRegister() {
  registerTouched.name = true;
  registerTouched.username = true;
  registerTouched.email = true;
  registerTouched.phone = true;
  registerTouched.password = true;
  registerTouched.confirm = true;
  formMessage.value = "";

  if (!validateRegister()) return;

  try {
    submitting.value = true;
    const res = await api.post("/technician/register", {
      name: registerForm.name,
      username: registerForm.username,
      email: registerForm.email,
      phone: registerForm.phone,
      password: registerForm.password,
      password_confirmation: registerForm.confirm
    });

    const data = res.data;
    localStorage.setItem("tech_auth", "1");
    localStorage.setItem("tech_user", JSON.stringify(data?.data || data?.user || null));
    window.location.href = "/technician";
  } catch (err) {
    const data = err.response?.data;
    if (data) {
       formMessage.value =
        data?.message ||
        data?.errors?.email?.[0] ||
        data?.errors?.phone?.[0] ||
        data?.errors?.username?.[0] ||
        "Đăng ký thất bại.";
    } else {
       formMessage.value = "Không kết nối được server.";
    }
  } finally {
    submitting.value = false;
  }
}
</script>

<style scoped>
.auth-page {
  position: fixed;
  inset: 0;
  z-index: 9999;
  min-height: 100vh;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #f4f8ff 0%, #eef4ff 55%, #f7f9fc 100%);
  font-family: "Poppins", "Segoe UI", Tahoma, Arial, sans-serif;
  padding: 24px;
  overflow: auto;
}

.card {
  width: min(440px, 100%);
  background: #fff;
  border-radius: 16px;
  padding: 22px;
  box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
  display: grid;
  gap: 16px;
}

.brand {
  display: flex;
  gap: 12px;
  align-items: center;
}

.logo {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: linear-gradient(135deg, #3b82f6, #22d3ee);
  display: grid;
  place-items: center;
  font-weight: 700;
  color: #fff;
}

.brand h1 {
  margin: 0;
  font-size: 16px;
}

.brand p {
  margin: 4px 0 0;
  font-size: 12px;
  color: #64748b;
}

.switcher {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.tab {
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 10px;
  padding: 8px 10px;
  font-size: 12px;
  cursor: pointer;
  color: #1e293b;
  font-weight: 600;
}

.tab.active {
  background: #e0ebff;
  border-color: #93c5fd;
  color: #1d4ed8;
}

.form {
  display: grid;
  gap: 12px;
}

.field {
  display: grid;
  gap: 6px;
  font-size: 12px;
  color: #475569;
}

.field input {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-size: 13px;
}

.password-row {
  display: flex;
  gap: 8px;
  align-items: center;
}

.password-row input {
  flex: 1;
}

.ghost {
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 10px;
  padding: 8px 10px;
  font-size: 12px;
  cursor: pointer;
}

.primary {
  border: none;
  background: #2563eb;
  color: #fff;
  border-radius: 10px;
  padding: 10px 12px;
  font-weight: 600;
  cursor: pointer;
}

.primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.links {
  display: flex;
  justify-content: center;
  font-size: 12px;
}

.links a {
  color: #2563eb;
  text-decoration: none;
}

.links a:hover {
  text-decoration: underline;
}

.error {
  color: #dc2626;
  font-size: 11px;
}

.message {
  color: #0f172a;
  font-size: 12px;
  margin: 0;
  background: #e0f2fe;
  border: 1px solid #bae6fd;
  padding: 8px 10px;
  border-radius: 10px;
}

.invalid {
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}
</style>
