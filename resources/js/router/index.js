import { createRouter, createWebHistory } from "vue-router"
import Dashboard from "@/views/Dashboard.vue"
import Table from "@/views/Table.vue"
import POS from "@/views/POS.vue"
import Test from "@/views/Test.vue"

const routes = [
  {
    path: "/",
    name: "Dashboard",
    component: Dashboard,
  },
  {
    path: "/table",
    name: "Table",
    component: Table,
  },
  {
    path: "/pos",
    name: "POS",
    component: POS,
  },
  // {
  //   path: "/",
  //   name: "TEST",
  //   component: Test,
  // },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
