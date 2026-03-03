<template>
  <aside class="admin-sidebar" :class="{ collapsed: isCollapsed }">
    <div class="sidebar-brand">
      <div class="brand-wrapper" v-if="!isCollapsed">
        <div class="logo-box">
          <i class="fas fa-building"></i>
        </div>
        <span class="brand-text">EcoHome</span>
      </div>
      <button
        class="toggle-btn"
        @click="toggleSidebar"
        :class="{ 'btn-centered': isCollapsed }"
      >
        <i class="fas fa-bars"></i>
      </button>
    </div>
    <div class="sidebar-menu">
      <ul>
        <li v-for="(item, index) in menuItems" :key="index">
          <router-link
            v-if="!item.children"
            :to="item.link"
            class="menu-item"
            exact-active-class="active"
            :title="isCollapsed ? item.title : ''"
          >
            <i :class="['item-icon', item.icon]"></i>
            <span class="item-text" v-if="!isCollapsed">{{ item.title }}</span>
          </router-link>

          <div
            v-else
            class="submenu-wrapper"
            :class="{ 'is-open': item.isOpen }"
          >
            <div
              class="menu-item parent"
              @click="toggleSubmenu(index)"
              :class="{ expanded: item.isOpen }"
              :title="isCollapsed ? item.title : ''"
            >
              <div class="label-group">
                <i :class="['item-icon', item.icon]"></i>
                <span class="item-text" v-if="!isCollapsed">{{
                  item.title
                }}</span>
              </div>
              <i
                v-if="!isCollapsed"
                class="fas fa-chevron-right arrow-icon"
                :class="{ rotated: item.isOpen }"
              ></i>
            </div>

            <ul class="submenu" v-show="item.isOpen && !isCollapsed">
              <li
                v-for="(child, childIndex) in item.children"
                :key="childIndex"
              >
                <router-link :to="child.link" class="submenu-link" exact-active-class="active-submenu">
                  <span class="dot"></span>
                  {{ child.title }}
                </router-link>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>

    <div class="sidebar-footer">
      <a href="#" class="logout-link" :title="isCollapsed ? 'Đăng xuất' : ''" @click.prevent="handleLogout">
        <i class="fas fa-sign-out-alt"></i>
        <span v-if="!isCollapsed">Đăng xuất</span>
      </a>
    </div>
  </aside>
</template>

<script>
import { useRouter } from 'vue-router';

export default {
  name: "AdminSidebar",
  setup() {
    const router = useRouter();
    function handleLogout() {
      if (confirm('Bạn có chắc muốn đăng xuất không?')) {
        localStorage.removeItem('admin_auth');
        localStorage.removeItem('admin_user');
        router.push('/admin/login');
      }
    }
    return { handleLogout };
  },
  data() {
    return {
      isCollapsed: false,
      menuItems: [
        {
          title: "Dashboard",
          icon: "fas fa-chart-line",
          link: "/admin",
          children: null,
        },
        {
          title: "Quản lý Cư Dân",
          icon: "fas fa-user-friends",
          isOpen: false,
          children: [
            { title: "Danh sách", link: "/admin/qlcd/danh-sach-nha" },
            { title: "Sơ đồ nhà", link: "/admin/qlcd/so-do-nha" },
          ],
        },
        {
          title: "Bảo Trì",
          icon: "fas fa-tools",
          isOpen: true,
          children: [
            { title: "Yêu cầu", link: "/admin/bao-tri/yeu-cau" },
            { title: "Phân công", link: "/admin/bao-tri/phan-cong" },
          ],
        },
        {
          title: "Tài Chính",
          icon: "fas fa-wallet",
          isOpen: false,
          children: [
            { title: "Hóa đơn", link: "/admin/tai-chinh/hoa-don" },
            { title: "Quản lý Chi Phí", link: "/admin/tai-chinh/chi-phi" },
          ],
        },
        {
          title: "Kho Vật Tư",
          icon: "fas fa-boxes",
          link: "/admin/kho-vat-tu",
          children: null,
        },
        {
          title: "Hệ thống",
          icon: "fas fa-cogs",
          link: "/admin/settings",
          children: null,
        },
      ],
    };
  },
  methods: {
    applySidebarWidth() {
      const width = this.isCollapsed ? "70px" : "260px";
      document.documentElement.style.setProperty("--admin-sidebar-width", width);
    },
    toggleSidebar() {
      this.isCollapsed = !this.isCollapsed;
      if (this.isCollapsed) {
        this.menuItems.forEach((item) => {
          if (item.children) item.isOpen = false;
        });
      }
      this.applySidebarWidth();
    },
    toggleSubmenu(index) {
      if (this.isCollapsed) {
        this.isCollapsed = false;
        this.applySidebarWidth();
      }
      this.menuItems[index].isOpen = !this.menuItems[index].isOpen;
    },
  },
  mounted() {
    this.applySidebarWidth();
  },
  beforeUnmount() {
    document.documentElement.style.removeProperty("--admin-sidebar-width");
  },
};
</script>
<style scoped>
/* --- 1. Tổng thể Sidebar (Deep Navy Theme) --- */
.admin-sidebar {
  width: 260px;
  height: 100vh;
  background: linear-gradient(180deg, #3b6cb7 0%, #2a539e 100%);
  color: #ffffff;
  /* Ép toàn bộ chữ mặc định là màu trắng */
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  transition: width 0.3s ease-in-out;
  z-index: 1000;
  box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}

.admin-sidebar.collapsed {
  width: 70px;
}

/* --- 2. Header Brand --- */
.sidebar-brand {
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 15px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  /* Viền trắng mờ */
}

.brand-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
  white-space: nowrap;
}

.logo-box {
  width: 34px;
  height: 34px;
  background: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #3b6cb7;
}

.brand-text {
  font-size: 19px;
  font-weight: 800;
  color: #ffffff !important;
  /* Bắt buộc trắng */
}

.toggle-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  cursor: pointer;
  padding: 5px;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.admin-sidebar.collapsed .sidebar-brand {
  justify-content: center;
  padding: 0;
}

.btn-centered {
  margin: 0 auto;
}

/* --- 3. Menu Items --- */
.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  padding-top: 10px;
}

.sidebar-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  /* QUAN TRỌNG: Chữ menu bình thường là màu trắng */
  color: #ffffff !important;
  text-decoration: none;
  transition: 0.2s;
  cursor: pointer;
  height: 50px;
  white-space: nowrap;
  font-weight: 500;
  border-left: 4px solid transparent;
}

/* Icon bình thường cũng màu trắng */
.item-icon {
  font-size: 18px;
  width: 20px;
  text-align: center;
  margin-right: 14px;
  color: rgba(255, 255, 255, 0.9) !important;
}

/* Hover State */
.menu-item:hover {
  background-color: rgba(255, 255, 255, 0.15);
}

/* --- Active State (Đảo màu: Nền trắng - Chữ navy) --- */
.menu-item.active {
  background-color: #ffffff !important;
  color: #3b6cb7 !important;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.menu-item.active .item-icon {
  color: #3b6cb7 !important;
}

/* Collapsed Logic */
.admin-sidebar.collapsed .menu-item {
  padding: 12px 0;
  justify-content: center;
}

.admin-sidebar.collapsed .item-icon {
  margin-right: 0;
}

.menu-item.parent {
  justify-content: space-between;
}

.admin-sidebar.collapsed .menu-item.parent {
  justify-content: center;
}

/* Submenu Styling */
.arrow-icon {
  font-size: 10px;
  transition: 0.3s;
  color: rgba(255, 255, 255, 0.7);
}

.arrow-icon.rotated {
  transform: rotate(90deg);
}

.submenu {
  background-color: rgba(0, 0, 0, 0.25);
}

.submenu-link {
  display: flex;
  align-items: center;
  padding: 10px 10px 10px 54px;
  color: rgba(255, 255, 255, 0.9) !important;
  /* Chữ menu con trắng */
  text-decoration: none;
  font-size: 13px;
  transition: 0.2s;
}

.submenu-link.active-submenu,
.submenu-link:hover {
  color: #ffffff !important;
  background-color: rgba(255, 255, 255, 0.1);
}

.dot {
  width: 6px;
  height: 6px;
  background: rgba(255, 255, 255, 0.6);
  border-radius: 50%;
  margin-right: 10px;
}

.submenu-link:hover .dot {
  background: #ffffff;
  transform: scale(1.2);
}

/* --- 5. Footer --- */
.sidebar-footer {
  padding: 15px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.logout-link {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ff4d4f !important;
  /* Màu hồng phấn nhạt */
  text-decoration: none;
  padding: 10px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  transition: 0.2s;
  font-weight: 600;
}

.logout-link:hover {
  background: rgba(255, 255, 255, 0.25);
  color: #a73131 !important;
}

.admin-sidebar.collapsed .logout-link {
  padding: 10px 0;
  background: transparent;
}

/* Scrollbar */
.sidebar-menu::-webkit-scrollbar {
  width: 4px;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}
</style>
