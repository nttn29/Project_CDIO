import { createRouter, createWebHistory } from "vue-router";
import Home from "../components/Client/Home.vue";
import AdminHome from "../components/Admin/TrangChu.vue";
import AdminSoDoNha from "../components/Admin/BANQUANLY/QuanLyCuDan/SoDoNha/index.vue";
import AdminDanhSachNha from "../components/Admin/BANQUANLY/QuanLyCuDan/DanhSachNha/index.vue";
import AdminYeuCauBT from "../components/Admin/BANQUANLY/BaoTri/YeuCauBT/index.vue";
import AdminPhanCongBT from "../components/Admin/BANQUANLY/BaoTri/PhanCong/index.vue";
import AdminHoaDon from "../components/Admin/BANQUANLY/TaiChinh/HoaDon/index.vue";
import TechnicianHome from "../technician/home/TechnicianHome.vue";
const routes = [
  {
    path: "/",
    component: Home,
    meta: { layout: "Client" },
  },
  {
    path: "/admin",
    component: AdminHome,
    meta: { layout: "Admin" },
  },
  {
    path: "/admin/qlcd/so-do-nha",
    component: AdminSoDoNha,
    meta: { layout: "Admin" },
  },
  {
    path: "/admin/qlcd/danh-sach-nha",
    component: AdminDanhSachNha,
    meta: { layout: "Admin" },
  },
  {
    path: "/admin/bao-tri/yeu-cau",
    component: AdminYeuCauBT,
    meta: { layout: "Admin" },
  },
  {
    path: "/admin/bao-tri/phan-cong",
    component: AdminPhanCongBT,
    meta: { layout: "Admin" },
  },
  {
    path: "/admin/tai-chinh/hoa-don",
    component: AdminHoaDon,
    meta: { layout: "Admin" },
  },
  {
    path: "/login-tech",
    component: () => import("../technician/auth/TechnicianLogin.vue"),
  },
  {
    path: "/technician-login",
    component: () => import("../technician/auth/TechnicianLogin.vue"),
  },
  {
    path: "/technician-register",
    component: () => import("../technician/auth/TechnicianLogin.vue"),
  },
  {
    path: "/technician",
    component: TechnicianHome,
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/jobs",
    component: () => import("../technician/features/TechnicianJobs.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/job-detail",
    component: () => import("../technician/features/TechnicianJobDetail.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/schedule",
    component: () => import("../technician/features/TechnicianSchedule.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/status",
    component: () => import("../technician/features/TechnicianStatus.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/history",
    component: () => import("../technician/features/TechnicianHistory.vue"),
    meta: { layout: "Technician" },
  },
  {
    path: "/technician/profile",
    component: () => import("../technician/features/TechnicianProfile.vue"),
    meta: { layout: "Technician" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const isTechnicianRoute = to.path.startsWith("/technician");
  const isTechAuthPage = to.path === "/login-tech";
  const isAuthed = localStorage.getItem("tech_auth") === "1";

  if (isTechnicianRoute && !isAuthed) {
    return next("/login-tech");
  }

  if (isTechAuthPage && isAuthed) {
    return next("/technician");
  }

  next();
});

export default router;
