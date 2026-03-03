<template>
  <nav class="navbar" :class="{ scrolled: isScrolled }">
    <div class="container">

      <!-- BRAND -->
      <div class="navbar-brand">
        <span class="logo-icon">🏙️</span>
        <span class="brand-name">
          EcoHome <span class="highlight">Resident</span>
        </span>
      </div>

      <!-- MENU -->
      <div class="navbar-menu" :class="{ 'is-active': isMobileMenuOpen }">
        <ul class="menu-list">
          <li v-for="(item, index) in menuItems" :key="index">
            <router-link
              :to="item.link"
              class="menu-link"
              :class="{ active: route.path === item.link }"
              @click="closeMobileMenu"
            >
              <i :class="item.icon"></i>
              <span>{{ item.text }}</span>
            </router-link>
          </li>
        </ul>
      </div>

      <!-- RIGHT -->
      <div class="navbar-end">
        <!-- USER -->
        <div class="user-dropdown-wrapper">
          <div class="user-dropdown" @click.stop="toggleUserDropdown">
            <div class="avatar-wrapper">
              <img :src="userAvatar" class="avatar" />
            </div>
            <i
              class="fas fa-chevron-down caret-icon"
              :class="{ rotate: showUserDropdown }"
            ></i>
          </div>

          <transition name="slide-fade">
            <div class="dropdown-content" v-show="showUserDropdown">
              <div class="dropdown-header user-info-center">
                <div class="avatar-wrapper big">
                  <img :src="userAvatar" class="avatar" />
                </div>
                <div class="user-name big">
                  {{ isAuthenticated ? userName : 'Chưa đăng nhập' }}
                </div>
                <div v-if="isAuthenticated && userRoom" class="user-room">
                  Căn hộ: {{ userRoom }}
                </div>
                <div class="user-role">Cư dân</div>
              </div>

             

              

              <router-link to="/settings" class="dropdown-item">
                <i class="fas fa-cog"></i> Cài đặt
              </router-link>

              <div class="divider"></div>

              <a
                v-if="isAuthenticated"
                href="#"
                class="dropdown-item logout-item"
                @click="logout"
              >
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
              </a>

              <router-link
                v-else
                to="/Client/login"
                class="dropdown-item"
              >
                <i class="fas fa-sign-in-alt"></i> Đăng nhập
              </router-link>
            </div>
          </transition>
        </div>

        <!-- BURGER -->
        <button
          class="burger-btn"
          :class="{ 'is-open': isMobileMenuOpen }"
          @click="toggleMobileMenu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>

    <!-- OVERLAY -->
    <transition name="fade">
      <div
        v-if="isMobileMenuOpen"
        class="overlay"
        @click="closeMobileMenu"
      ></div>
    </transition>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { user, isAuthenticated, logout as authLogout } from '@/services/api'

const route = useRoute()
const router = useRouter()

/* ===== USER ===== */
const isAuthenticatedLocal = isAuthenticated

const userAvatar = computed(() => user.value?.avatar || 'https://cdn-icons-png.flaticon.com/512/149/149071.png')

const userName = computed(() => user.value?.ten || user.value?.name || 'Resident')

const userRoom = computed(() =>
  user.value?.can_ho?.so_can_ho ||
  user.value?.canHo?.so_can_ho ||
  user.value?.so_can_ho ||
  user.value?.so_phong ||
  (user.value?.id_can_ho ? `#${user.value.id_can_ho}` : '')
)

function logout(e) {
  e.preventDefault()
  authLogout()
  router.push('/Client/login')
}

/* ===== MENU ===== */
const menuItems = [
  { text: 'Trang chủ', link: '/Client/home', icon: 'fas fa-home' },
  { text: 'Báo cáo sự cố', link: '/Client/dashboard', icon: 'fas fa-exclamation-triangle' },
  { text: 'Dịch vụ', link: '/Client/services', icon: 'fas fa-concierge-bell' },
  { text: 'Lịch sử', link: '/Client/history', icon: 'fas fa-clock' },
  { text: 'Đánh giá', link: '/Client/reviews', icon: 'fas fa-star' },
]

/* ===== UI STATE ===== */
const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)
const showUserDropdown = ref(false)

/* ===== METHODS ===== */
function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

function closeMobileMenu() {
  isMobileMenuOpen.value = false
}

function toggleUserDropdown() {
  showUserDropdown.value = !showUserDropdown.value
}

function handleScroll() {
  isScrolled.value = window.scrollY > 10
}

/* ===== LIFECYCLE ===== */
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
:global(:root) {
  --primary: #6ec1e4;
  --primary-soft: #eaf7fd;
  --text-main: #2c3e50;
  --text-light: #5f6c7b;
}

/* NAVBAR */
.navbar {
  position: fixed;
  top: 0;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  width: 100vw;
  height: 72px;
  background: linear-gradient(90deg, rgba(240,250,255,1), rgba(240,250,255,0.95));
  backdrop-filter: blur(14px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  z-index: 1000;
  transition: 0.3s;
}

.navbar.scrolled {
  height: 64px;
  background: linear-gradient(90deg, #ffffff, #ffffffcc);
  box-shadow: 0 6px 24px rgba(110, 193, 228, 0.25);
}

.container {
  max-width: 1280px;
  margin: auto;
  padding: 0 24px;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* BRAND */
.navbar-brand {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo-icon {
  font-size: 22px;
}

.brand-name {
  font-size: 20px;
  font-weight: 800;
}

.highlight {
  color: var(--primary);
}

/* MENU */
.menu-list {
  display: flex;
  gap: 10px;
  list-style: none;
}

.menu-link {
  padding: 10px 18px;
  border-radius: 14px;
  color: var(--text-main);
  font-weight: 600;
  display: flex;
  gap: 8px;
  align-items: center;
  text-decoration: none;
  transition: 0.25s;
}

.menu-link:hover {
  background: var(--primary-soft);
  color: var(--primary);
}

.menu-link.active {
  background: var(--primary);
  color: #fff;
}

.menu-link:active,
.menu-link:focus-visible {
  opacity: 1;
  color: var(--primary);
  background: var(--primary-soft);
}

.menu-link.active:active,
.menu-link.active:focus-visible {
  color: #fff;
  background: var(--primary);
}

/* USER */
.user-dropdown {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 14px 6px 6px;
  background: #f1faff;
  border-radius: 50px;
  cursor: pointer;
}

.avatar-wrapper {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  overflow: hidden;
}

.avatar-wrapper.big {
  width: 64px;
  height: 64px;
  margin: 0 auto 8px;
}

.avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.dropdown-content {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  width: 230px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
  padding: 8px;
}

.dropdown-header {
  text-align: center;
  padding: 12px 8px 6px;
}

.user-info-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.user-name.big {
  font-weight: 700;
}

.user-room {
  font-size: 12px;
  color: var(--text-light);
}

.user-role {
  font-size: 12px;
  color: var(--text-light);
  background: var(--primary-soft);
  padding: 4px 10px;
  border-radius: 999px;
}

.dropdown-item {
  padding: 12px 16px;
  display: flex;
  gap: 12px;
  border-radius: 10px;
  text-decoration: none;
  color: var(--text-main);
}

.dropdown-item:hover {
  background: var(--primary-soft);
}

.dropdown-item:active,
.dropdown-item:focus-visible {
  opacity: 1;
  color: var(--text-main);
  background: var(--primary-soft);
}

.logout-item {
  color: #ff6b6b;
}

.divider {
  height: 1px;
  background: #eee;
  margin: 6px 0;
}

/* BURGER */
.burger-btn {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
}

.burger-btn span {
  width: 26px;
  height: 2px;
  background: var(--text-main);
}

/* MOBILE */
@media (max-width: 992px) {
  .burger-btn {
    display: flex;
  }

  .menu-list {
    display: none;
  }

  .navbar-menu {
    position: fixed;
    top: 72px;
    left: -100%;
    width: 260px;
    height: calc(100vh - 72px);
    background: #fff;
    padding: 24px;
    transition: 0.3s;
  }

  .navbar-menu.is-active {
    left: 0;
  }

  .menu-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
}

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.2);
}
</style>
