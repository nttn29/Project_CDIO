<template>
    <header class="app-header" :class="{ 'scrolled': isScrolled }">
        <div class="header-container">
            <div class="brand" @click="goHome">
                <div class="logo-wrapper">
                    <span class="logo-icon">🏢</span>
                </div>
                <div class="brand-info">
                    <h1 class="brand-name">Condo<span class="highlight">Maint</span></h1>
                    <span v-if="userRole === 'admin'" class="role-badge">Admin Portal</span>
                </div>
            </div>

            <nav class="desktop-nav">
                <ul>
                    <li v-for="(item, index) in currentMenuItems" :key="index">
                        <a :href="item.link" class="nav-link" :class="{ 'active': activeLink === item.link }">
                            {{ item.text }}
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="action-group">
                <button @click="toggleRole" class="btn-role-switch" title="Chuyển đổi giao diện">
                    <i class="fas fa-sync-alt"></i>
                    <span>{{ userRole === 'admin' ? 'View Client' : 'View Admin' }}</span>
                </button>

                <div class="divider"></div>

                <template v-if="isLoggedIn">
                    <div class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot" v-if="notificationCount > 0">{{ notificationCount }}</span>
                    </div>

                    <div class="user-profile">
                        <div class="avatar-wrapper">
                            <img :src="userAvatar" alt="User" />
                        </div>
                        <div class="user-info">
                            <span class="name">{{ userName }}</span>
                            <span class="logout-text" @click="logout">Đăng xuất</span>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <button class="btn-primary">Đăng nhập</button>
                </template>

                <button class="mobile-toggle" @click="isMobileMenuOpen = !isMobileMenuOpen">
                    <i :class="isMobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
                </button>
            </div>
        </div>

        <transition name="fade-slide">
            <div v-if="isMobileMenuOpen" class="mobile-menu">
                <nav class="mobile-nav-list">
                    <a v-for="(item, index) in currentMenuItems" :key="index" :href="item.link" class="mobile-link"
                        @click="isMobileMenuOpen = false">
                        {{ item.text }}
                    </a>
                    <div class="mobile-divider"></div>
                    <a v-if="isLoggedIn" @click="logout" class="mobile-link logout">Đăng xuất</a>
                </nav>
            </div>
        </transition>
    </header>
</template>

<script>
export default {
    name: "ModernHeader",
    data() {
        return {
            isLoggedIn: true,
            userRole: "resident",
            isMobileMenuOpen: false,
            activeLink: "/",
            notificationCount: 3,
            isScrolled: false,

            residentMenu: [
                { text: "Trang chủ", link: "/Client/home" },
                { text: "Báo cáo sự cố", link: "/Client/dashboard" },
                { text: "Lịch sử", link: "/Client/history" },
                { text: "Dịch vụ", link: "/Client/services" },
            ],
            adminMenu: [
                { text: "Trang chủ", link: "/admin" },
                { text: "Danh sách Cư dân", link: "/admin/qlcd/danh-sach-nha" },
                { text: "Yêu cầu", link: "/admin/bao-tri/yeu-cau" },
                { text: "Phân công", link: "/admin/bao-tri/phan-cong" },
                { text: "Hóa đơn", link: "/admin/tai-chinh/hoa-don" },
            ],
        };
    },
    computed: {
        currentMenuItems() {
            return this.userRole === "admin" ? this.adminMenu : this.residentMenu;
        },
        userName() {
            if (this.userRole === 'admin') {
              try {
                const u = JSON.parse(localStorage.getItem('admin_user') || '{}');
                return u.ten || u.name || 'Quản trị viên';
              } catch { return 'Quản trị viên'; }
            }
            return 'Nguyễn Văn Nam';
        },
        userAvatar() {
            return "https://i.pravatar.cc/150?img=11"; // Ảnh demo đẹp hơn
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
    },
    destroyed() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        goHome() { window.location.href = "/"; },
        logout() {
            const isAdminPage = window.location.pathname.startsWith('/admin');
            if (isAdminPage) {
                localStorage.removeItem('admin_auth');
                localStorage.removeItem('admin_user');
                window.location.href = '/admin/login';
            } else {
                localStorage.removeItem('user');
                localStorage.removeItem('token');
                window.location.href = '/Client/login';
            }
        },
        toggleRole() {
            this.userRole = this.userRole === "resident" ? "admin" : "resident";
            this.isMobileMenuOpen = false;
        },
        handleScroll() {
            this.isScrolled = window.scrollY > 10;
        }
    },
};
</script>

<style scoped>
/* --- 1. Base Structure --- */
.app-header {
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    /* Hiệu ứng kính mờ */
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 900;
    position: sticky;
    top: 0;
    z-index: 1000;
    height: 70px;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    transition: all 0.3s ease;
}

.app-header.scrolled {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    height: 64px;
    /* Thu nhỏ nhẹ khi cuộn */
}

.header-container {
    max-width: 1200px;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* --- 2. Brand Styling --- */
.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    user-select: none;
}

.logo-wrapper {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
}

.logo-icon {
    font-size: 22px;
}

.brand-info {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.brand-name {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    letter-spacing: -0.5px;
}

.brand-name .highlight {
    color: #3498db;
}

.role-badge {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #e67e22;
    letter-spacing: 0.5px;
    background: rgba(230, 126, 34, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    width: fit-content;
}

/* --- 3. Navigation --- */
.desktop-nav ul {
    display: flex;
    list-style: none;
    gap: 8px;
    margin: 0;
    padding: 0;
}

.nav-link {
    text-decoration: none;
    color: #64748b;
    font-weight: 500;
    font-size: 15px;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.nav-link:hover {
    background-color: #f1f5f9;
    color: #3498db;
}

.nav-link.active {
    background-color: #e0f2fe;
    color: #0284c7;
    font-weight: 600;
}

/* --- 4. Right Actions --- */
.action-group {
    display: flex;
    align-items: center;
    gap: 16px;
}

.divider {
    height: 24px;
    width: 1px;
    background-color: #e2e8f0;
}

/* Toggle Role Button (Clean Style) */
.btn-role-switch {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid #cbd5e1;
    background: transparent;
    border-radius: 20px;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-role-switch:hover {
    border-color: #3498db;
    color: #3498db;
    background-color: #f8fafc;
}

/* Notification */
.notification-btn {
    position: relative;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    color: #64748b;
    transition: background 0.2s;
}

.notification-btn:hover {
    background-color: #f1f5f9;
    color: #3498db;
}

.notification-dot {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 8px;
    height: 8px;
    background-color: #ef4444;
    border-radius: 50%;
    border: 1px solid #fff;
}

/* User Profile */
.user-profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-left: 5px;
}

.avatar-wrapper img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-info .name {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.logout-text {
    font-size: 11px;
    color: #94a3b8;
    cursor: pointer;
    transition: color 0.2s;
}

.logout-text:hover {
    color: #ef4444;
    text-decoration: underline;
}

/* Login Button */
.btn-primary {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-primary:hover {
    background-color: #2980b9;
}

/* --- Mobile Styling --- */
.mobile-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 24px;
    color: #334155;
    cursor: pointer;
}

.mobile-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.mobile-nav-list {
    display: flex;
    flex-direction: column;
    padding: 10px 0;
}

.mobile-link {
    padding: 12px 24px;
    text-decoration: none;
    color: #475569;
    font-weight: 500;
}

.mobile-link:hover {
    background-color: #f8fafc;
    color: #3498db;
}

.mobile-link.logout {
    color: #ef4444;
}

.mobile-divider {
    height: 1px;
    background-color: #e2e8f0;
    margin: 8px 24px;
}

/* --- Responsive --- */
@media (max-width: 900px) {

    .desktop-nav,
    .user-info,
    .divider,
    .btn-role-switch {
        display: none;
    }

    .mobile-toggle {
        display: block;
    }
}

/* Animations */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}

.fade-slide-enter,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>