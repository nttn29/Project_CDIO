<!-- Bot Bar - Footer & Chat Widget -->
<template>
    <footer class="maintenance-footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3 class="logo-text">CondoMaint<span class="highlight">System</span></h3>
                <p>
                    Hệ thống quản lý vận hành và bảo trì chung cư thông minh.
                    Tiếp nhận sự cố 24/7 và đảm bảo chất lượng sống tốt nhất cho cư dân.
                </p>
                <div class="socials">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-section links">
                <h3>Tiện Ích Cư Dân</h3>
                <ul>
                    <li><a href="/bao-cao-su-co">🚨 Báo cáo sự cố</a></li>
                    <li><a href="/lich-bao-tri">📅 Lịch bảo trì định kỳ</a></li>
                    <li><a href="/hoa-don">🧾 Tra cứu phí dịch vụ</a></li>
                    <li><a href="/quy-dinh">📜 Quy định chung cư</a></li>
                </ul>
            </div>

            <div class="footer-section contact">
                <h3>Liên Hệ Ban Quản Lý</h3>
                <div class="contact-item">
                    <span class="icon">📞</span>
                    <div class="info">
                        <span class="label">Hotline Kỹ Thuật (24/7):</span>
                        <span class="highlight-text">1900 123 456</span>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="icon">📧</span>
                    <div class="info">
                        <span class="label">Email hỗ trợ:</span>
                        <span>support@condosystem.vn</span>
                    </div>
                </div>
                <div class="contact-item">
                    <span class="icon">📍</span>
                    <div class="info">
                        <span class="label">Văn phòng:</span>
                        <span>Tầng 1, Tòa nhà A, Chung cư BlueSky</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ currentYear }} Ban Quản Lý Chung cư. All rights reserved.</p>
        </div>
    </footer>
</template>

<script>
export default {
    name: "MainFooter",
    data() {
        return {
            currentYear: new Date().getFullYear(), // Tự động cập nhật năm
        };
    },
};
</script>

<style scoped>
/* Reset cơ bản cho footer */
.maintenance-footer {
    background-color: #1a252f;
    /* Màu xanh đen chuyên nghiệp */
    color: #ecf0f1;
    padding-top: 40px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px 40px;
}

/* Style cho từng cột */
.footer-section {
    flex: 1;
    min-width: 250px;
    margin-right: 20px;
    margin-bottom: 20px;
}

.footer-section:last-child {
    margin-right: 0;
}

h3 {
    color: #fff;
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
    border-bottom: 2px solid #3498db;
    display: inline-block;
    padding-bottom: 5px;
}

.logo-text {
    font-size: 24px;
    border: none;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.highlight {
    color: #3498db;
}

p {
    line-height: 1.6;
    color: #bdc3c7;
}

/* Links List */
ul {
    list-style: none;
    padding: 0;
}

ul li {
    margin-bottom: 12px;
}

ul li a {
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

ul li a:hover {
    color: #3498db;
    padding-left: 5px;
    /* Hiệu ứng di chuyển nhẹ khi hover */
}

/* Contact Section */
.contact-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
}

.contact-item .icon {
    font-size: 18px;
    margin-right: 12px;
    margin-top: 2px;
}

.contact-item .info {
    display: flex;
    flex-direction: column;
}

.contact-item .label {
    font-weight: bold;
    color: #95a5a6;
    margin-bottom: 2px;
}

.highlight-text {
    color: #e74c3c;
    /* Màu đỏ cho Hotline để nổi bật */
    font-weight: bold;
    font-size: 16px;
}

/* Footer Bottom */
.footer-bottom {
    background-color: #141d26;
    text-align: center;
    padding: 15px 0;
    margin-top: 20px;
    border-top: 1px solid #2c3e50;
}

.footer-bottom p {
    margin: 0;
    font-size: 13px;
}

/* Responsive: Chuyển sang 1 cột trên mobile */
@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
    }

    .footer-section {
        margin-right: 0;
        margin-bottom: 30px;
    }
}
</style>
