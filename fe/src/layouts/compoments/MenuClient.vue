<template>
    <nav class="navbar" :class="{ 'scrolled': isScrolled }">
        <div class="container">
            <div class="navbar-brand">
                <span class="logo-icon">🏙️</span>
                <span class="brand-name">EcoHome<span class="highlight">Resident</span></span>
            </div>

            <div class="navbar-menu" :class="{ 'is-active': isMobileMenuOpen }">
                <ul class="menu-list">
                    <li v-for="(item, index) in menuItems" :key="index" @click="closeMobileMenu">
                        <router-link :to="item.link" class="menu-link" exact-active-class="active"
                            @click="setActive(index)">
                            <i :class="item.icon"></i>
                            <span>{{ item.text }}</span>
                            <span v-if="item.badge" class="menu-badge">{{ item.badge }}</span>
                        </router-link>
                    </li>
                </ul>

                <div class="mobile-user-options" v-if="isMobileMenuOpen">
                    <a href="/profile" class="menu-link">Hồ sơ cá nhân</a>
                    <a href="#" @click="logout" class="menu-link text-danger">Đăng xuất</a>
                </div>
            </div>

            <div class="navbar-end">
                <div class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="dot" v-if="hasNotification"></span>
                </div>

                <div class="user-dropdown" @click="toggleUserDropdown">
                    <img src="https://i.pravatar.cc/150?img=3" alt="Avatar" class="avatar">
                    <span class="user-name">Anh Nam (P.802)</span>
                    <i class="fas fa-caret-down"></i>

                    <div class="dropdown-content" v-show="showUserDropdown">
                        <a href="/profile"><i class="fas fa-user"></i> Tài khoản</a>
                        <a href="/settings"><i class="fas fa-cog"></i> Cài đặt</a>
                        <div class="divider"></div>
                        <a href="#" @click="logout" class="logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>

                <button class="burger-btn" @click="toggleMobileMenu">
                    <i :class="isMobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
                </button>
            </div>
        </div>

        <div class="overlay" v-if="isMobileMenuOpen" @click="closeMobileMenu"></div>
    </nav>
</template>

<script>
export default {
  name: "HorizontalMenu",
    data() {
        return {
            activeIndex: 0,
            isMobileMenuOpen: false,
            showUserDropdown: false,
            isScrolled: false,
            hasNotification: true, // Giả lập có thông báo mới

            // Danh sách menu cho Cư dân
            menuItems: [
                { text: "Trang chủ", link: "/Client/home", icon: "fas fa-home" },
                { text: "Dashboard", link: "/Client/dashboard", icon: "fas fa-chart-pie" },
                { text: "Báo cáo sự cố", link: "/Client/services", icon: "fas fa-tools" },
                { text: "Lịch sử yêu cầu", link: "/Client/history", icon: "fas fa-history" },
                { text: "Đánh giá", link: "/Client/reviews", icon: "fas fa-star" },
            ]
        };
    },
    mounted() {
        // Thêm hiệu ứng shadow khi cuộn trang
        window.addEventListener('scroll', this.handleScroll);
        // Click outside để đóng dropdown
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeDestroy() {
        window.removeEventListener('scroll', this.handleScroll);
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        setActive(index) {
            this.activeIndex = index;
        },
        toggleMobileMenu() {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
            this.showUserDropdown = false;
        },
        closeMobileMenu() {
            this.isMobileMenuOpen = false;
        },
        toggleUserDropdown(e) {
            e.stopPropagation(); // Ngăn sự kiện click lan ra ngoài
            this.showUserDropdown = !this.showUserDropdown;
        },
        handleScroll() {
            this.isScrolled = window.scrollY > 20;
        },
        handleClickOutside(event) {
            // Nếu click ra ngoài vùng user-dropdown thì đóng nó lại
            if (!this.$el.querySelector('.user-dropdown').contains(event.target)) {
                this.showUserDropdown = false;
            }
        },
        logout() {
            alert("Đã đăng xuất!");
        }
    }
};
</script>

<style scoped>
/* --- Cấu trúc cơ bản --- */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 70px;
    background-color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    z-index: 1000;
    font-family: 'Segoe UI', sans-serif;
    transition: all 0.3s ease;
}

.navbar.scrolled {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    /* Đổ bóng đậm hơn khi cuộn */
    height: 60px;
    /* Thu nhỏ lại chút khi cuộn */
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* --- 1. Branding --- */
.navbar-brand {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.highlight {
    color: #3498db;
}

/* --- 2. Menu Links (Desktop) --- */
.menu-list {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 5px;
}

.menu-link {
    text-decoration: none;
    color: #57606f;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 20px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    position: relative;
}

.menu-link i {
    font-size: 14px;
}

/* Hover & Active */
.menu-link:hover {
    background-color: #f1f2f6;
    color: #3498db;
}

.menu-link.active {
    background-color: #e8f4fc;
    /* Màu nền xanh nhạt */
    color: #3498db;
    font-weight: 600;
}

/* Badge trên menu (ví dụ số hóa đơn) */
.menu-badge {
    background-color: #e74c3c;
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(20%, -20%);
}

/* --- 3. Right Section (User & Notify) --- */
.navbar-end {
    display: flex;
    align-items: center;
    gap: 20px;
}

.notification-btn {
    font-size: 20px;
    color: #7f8c8d;
    cursor: pointer;
    position: relative;
}

.dot {
    position: absolute;
    top: 0;
    right: 2px;
    width: 8px;
    height: 8px;
    background-color: #e74c3c;
    border-radius: 50%;
    border: 1px solid white;
}

/* User Dropdown */
.user-dropdown {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    position: relative;
    padding: 5px;
    border-radius: 25px;
    transition: background 0.2s;
}

.user-dropdown:hover {
    background-color: #f5f6fa;
}

.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
}

/* Dropdown Content */
.dropdown-content {
    position: absolute;
    top: 120%;
    right: 0;
    background-color: white;
    min-width: 180px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
    padding: 8px 0;
    overflow: hidden;
    animation: fadeIn 0.2s;
}

.dropdown-content a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #2c3e50;
    font-size: 14px;
    transition: background 0.2s;
}

.dropdown-content a:hover {
    background-color: #f1f2f6;
}

.dropdown-content .logout {
    color: #e74c3c;
}

.divider {
    height: 1px;
    background-color: #ecf0f1;
    margin: 5px 0;
}

/* --- Mobile Specific --- */
.burger-btn {
    display: none;
    /* Ẩn trên desktop */
    background: none;
    border: none;
    font-size: 24px;
    color: #2c3e50;
    cursor: pointer;
}

.mobile-user-options {
    display: none;
}

.overlay {
    display: none;
}

/* --- Responsive Media Query --- */
@media (max-width: 992px) {
    .burger-btn {
        display: block;
    }

    .user-name,
    .user-dropdown .fa-caret-down {
        display: none;
        /* Ẩn tên user trên mobile cho gọn */
    }

    /* Chuyển menu thành sidebar trượt ra trên mobile */
    .navbar-menu {
        position: fixed;
        top: 70px;
        /* Bắt đầu dưới navbar */
        left: -100%;
        /* Giấu sang trái */
        width: 250px;
        height: calc(100vh - 70px);
        background-color: white;
        flex-direction: column;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        transition: left 0.3s ease;
        overflow-y: auto;
    }

    .navbar-menu.is-active {
        left: 0;
        /* Trượt ra */
    }

    .menu-list {
        flex-direction: column;
        width: 100%;
    }

    .menu-link {
        padding: 12px;
        border-radius: 8px;
        width: 100%;
    }

    .mobile-user-options {
        display: block;
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }

    /* Overlay đen mờ phía sau */
    .overlay {
        display: block;
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
