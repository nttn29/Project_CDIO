import { createRouter, createWebHistory } from "vue-router";
import ClientHome from "../components/Client/Home/ClientHome.vue";
import AdminHome from "../components/Admin/TrangChu.vue";
import AdminSoDoNha from "../components/Admin/BANQUANLY/QuanLyCuDan/SoDoNha/index.vue";
import AdminDanhSachNha from "../components/Admin/BANQUANLY/QuanLyCuDan/DanhSachNha/index.vue";
import AdminYeuCauBT from "../components/Admin/BANQUANLY/BaoTri/YeuCauBT/index.vue";
import AdminPhanCongBT from "../components/Admin/BANQUANLY/BaoTri/PhanCong/index.vue";
import AdminHoaDon from "../components/Admin/BANQUANLY/TaiChinh/HoaDon/index.vue";

import ResidentDashboard from "../components/Client/Dashboard/ClientDashboard.vue";
import ResidentHistory from "../components/Client/History/ClientHistory.vue";
import ResidentReviews from "../components/Client/Reviews/ClientReview.vue";
import ResidentServices from "../components/Client/Services/ClientServices.vue";
import Login from "../components/Client/Auth/Login.vue";
import Register from "../components/Client/Auth/Register.vue";

const routes = [
  {
  path: "/", redirect: "/admin",},
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

// ADMIN
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
