import { createRouter, createWebHistory } from "vue-router";
import Home from "../components/Client/Home.vue";
import AdminHome from "../components/Admin/TrangChu.vue";
import AdminSoDoNha from "../components/Admin/BANQUANLY/QuanLyCuDan/SoDoNha/index.vue";
import AdminDanhSachNha from "../components/Admin/BANQUANLY/QuanLyCuDan/DanhSachNha/index.vue";
import AdminYeuCauBT from "../components/Admin/BANQUANLY/BaoTri/YeuCauBT/index.vue";
import AdminPhanCongBT from "../components/Admin/BANQUANLY/BaoTri/PhanCong/index.vue";
import AdminHoaDon from "../components/Admin/BANQUANLY/TaiChinh/HoaDon/index.vue";

import ResidentHome from "../components/Resident/Home/index.vue";
import ResidentDashboard from "../components/Resident/Dashboard/index.vue";
import ResidentHistory from "../components/Resident/History/index.vue";
import ResidentReviews from "../components/Resident/Reviews/index.vue";
import ResidentServices from "../components/Resident/Services/index.vue";
import Login from "../components/Resident/Auth/Login.vue";
import Register from "../components/Resident/Auth/Register.vue";
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
    path: "/resident",
    component: ResidentHome,
    meta: { layout: "Client" },
  },
  {
    path: "/resident/dashboard",
    component: ResidentDashboard,
    meta: { layout: "Client" },
  },
  {
    path: "/resident/history",
    component: ResidentHistory,
    meta: { layout: "Client" },
  },
  {
    path: "/resident/reviews",
    component: ResidentReviews,
    meta: { layout: "Client" },
  },
  {
    path: "/resident/services",
    component: ResidentServices,
    meta: { layout: "Client" },
  },
  {
    path: "/resident/login",
    component: Login,
    meta: { layout: "Client" },
  },
  {
    path: "/resident/register",
    component: Register,
    meta: { layout: "Client" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;