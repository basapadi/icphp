import { createRouter, createWebHistory } from "vue-router"
import Dashboard from "@/views/Dashboard.vue"
import Table from "@/views/Table.vue"
import POS from "@/views/POS.vue"
import Login from "@/views/Login.vue"

const routes = [
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/",
    name: "Dashboard",
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: "/pos",
    name: "POS",
    component: POS,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token') // atau cek Vuex/Pinia store

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'Login' }) // redirect ke login
  } else {
    next() // lanjut
  }
})

export default router
