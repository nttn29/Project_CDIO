import { createRouter, createWebHistory } from "vue-router";
import Home from "../components/Client/Home.vue";
import AdminHome from "../components/Admin/TrangChu.vue";
import AdminSoDoNha from "../components/Admin/BANQUANLY/QuanLyCuDan/SoDoNha/index.vue";
import AdminDanhSachNha from "../components/Admin/BANQUANLY/QuanLyCuDan/DanhSachNha/index.vue";
import AdminYeuCauBT from "../components/Admin/BANQUANLY/BaoTri/YeuCauBT/index.vue";
import AdminPhanCongBT from "../components/Admin/BANQUANLY/BaoTri/PhanCong/index.vue";
import AdminHoaDon from "../components/Admin/BANQUANLY/TaiChinh/HoaDon/index.vue";
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
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
