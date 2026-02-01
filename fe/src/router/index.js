import { createRouter, createWebHistory } from 'vue-router'
import Home from '../components/Client/Home.vue'
import AdminHome from '../components/Admin/TrangChu.vue'

const routes = [
  {
    path: '/',
    component: Home,
    meta: { layout: 'Client' }
  },
  {
    path: '/admin',
    component: AdminHome,
    meta: { layout: 'Admin' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
