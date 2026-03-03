import { createRouter, createWebHistory } from "vue-router";
import ClientHome from "../components/Client/Home/ClientHome.vue";
import Home from "../components/Client/Home.vue";
import AdminHome from "../components/Admin/TrangChu.vue";
import AdminSoDoNha from "../components/Admin/BANQUANLY/QuanLyCuDan/SoDoNha/index.vue";
import AdminDanhSachNha from "../components/Admin/BANQUANLY/QuanLyCuDan/DanhSachNha/index.vue";
import AdminYeuCauBT from "../components/Admin/BANQUANLY/BaoTri/YeuCauBT/index.vue";
import AdminPhanCongBT from "../components/Admin/BANQUANLY/BaoTri/PhanCong/index.vue";
import AdminHoaDon from "../components/Admin/BANQUANLY/TaiChinh/HoaDon/index.vue";
import AdminChiPhi from "../components/Admin/BANQUANLY/TaiChinh/ChiPhi/index.vue";
import AdminSettings from "../components/Admin/HeThong/index.vue";
import AdminKhoVatTu from "../components/Admin/BANQUANLY/KhoVatTu/index.vue";
import AdminLogin from "../components/Admin/Auth/AdminLogin.vue";

import ResidentDashboard from "../components/Client/Dashboard/ClientDashboard.vue";
import ResidentHistory from "../components/Client/History/ClientHistory.vue";
import ResidentReviews from "../components/Client/Reviews/ClientReview.vue";
import ResidentServices from "../components/Client/Services/ClientServices.vue";
import Login from "../components/Client/Auth/Login.vue";
import Register from "../components/Client/Auth/Register.vue";

import TechnicianHome from "../components/technician/home/TechnicianHome.vue";

const routes = [
  {
    path: "/",
    redirect: "/Client/home",
  },
  {
    path: "/home",
    component: Home,
    meta: { layout: "Client" },
  },
  {
    path: "/settings",
    redirect: "/Client/dashboard",
  },
  {
    path: "/Client/home",
    component: ClientHome,
    meta: { layout: "Client" },
  },
  {
    path: "/Client/dashboard",
    component: ResidentDashboard,
    meta: { layout: "Client" },
  },
  {
    path: "/Client/history",
    component: ResidentHistory,
    meta: { layout: "Client" },
  },
  {
    path: "/Client/reviews",
    component: ResidentReviews,
    meta: { layout: "Client" },
  },
  {
    path: "/Client/services",
    component: ResidentServices,
    meta: { layout: "Client" },
  },
  {
    path: "/Client/login",
    component: Login,
    meta: { layout: "Client" },
  },
  {
    path: "/Client/register",
    component: Register,
    meta: { layout: "Client" },
  },
  {
    path: "/admin/login",
    component: AdminLogin,
    meta: { layout: "blank", isAdminAuth: true },
  },
  {
    path: "/admin",
    component: AdminHome,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/qlcd/so-do-nha",
    component: AdminSoDoNha,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/qlcd/danh-sach-nha",
    component: AdminDanhSachNha,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/bao-tri/yeu-cau",
    component: AdminYeuCauBT,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/bao-tri/phan-cong",
    component: AdminPhanCongBT,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/tai-chinh/hoa-don",
    component: AdminHoaDon,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/tai-chinh/chi-phi",
    component: AdminChiPhi,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/kho-vat-tu",
    component: AdminKhoVatTu,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/admin/settings",
    component: AdminSettings,
    meta: { layout: "Admin", requiresAdminAuth: true },
  },
  {
    path: "/login-tech",
    component: () => import("../components/technician/auth/TechnicianLogin.vue"),
  },
  {
    path: "/technician-login",
    component: () => import("../components/technician/auth/TechnicianLogin.vue"),
  },
  {
    path: "/technician-register",
    component: () => import("../components/technician/auth/TechnicianLogin.vue"),
  },
  {
    path: "/technician",
    component: TechnicianHome,
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/jobs",
    component: () => import("../components/technician/features/TechnicianJobs.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/job-detail",
    component: () => import("../components/technician/features/TechnicianJobDetail.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/status",
    component: () => import("../components/technician/features/TechnicianStatus.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/worklog",
    component: () => import("../components/technician/features/TechnicianWorkLog.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/history",
    component: () => import("../components/technician/features/TechnicianHistory.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/profile",
    component: () => import("../components/technician/features/TechnicianProfile.vue"),
    meta: { layout: "Technician" },
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  // ── TECHNICIAN GUARD ──
  const isTechnicianRoute = to.path === "/technician" || to.path.startsWith("/technician/");
  const isTechAuthPage =
    to.path === "/login-tech" ||
    to.path === "/technician-login" ||
    to.path === "/technician-register";
  const isTechAuthed = localStorage.getItem("tech_auth") === "1";

  if (isTechnicianRoute && !isTechAuthed) return next("/login-tech");
  if (isTechAuthPage && isTechAuthed) return next("/technician");

  // ── ADMIN GUARD ──
  const isAdminAuthed = localStorage.getItem("admin_auth") === "1";

  if (to.meta.requiresAdminAuth && !isAdminAuthed) {
    return next("/admin/login");
  }
  if (to.meta.isAdminAuth && isAdminAuthed) {
    return next("/admin");
  }

  next();
});

export default router;
