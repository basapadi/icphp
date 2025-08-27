import { createRouter, createWebHistory } from "vue-router"
import Dashboard from "@/views/Dashboard.vue"
import POS from "@/views/POS.vue"
import Login from "@/views/Login.vue"
import Contact from "@/views/master/Contact.vue"
import User from "@/views/master/User.vue"
import Item from "@/views/master/Item.vue"
import Unit from "@/views/master/Unit.vue"
import Menu from "@/views/setting/Menu.vue"
import Role from "@/views/setting/Role.vue"
import Database from "@/views/setting/Database.vue"
import General from "@/views/setting/General.vue"
import Received from "@/views/transaction/Received.vue"
import Sale from "@/views/transaction/Sale.vue"
import Stock from "@/views/transaction/warehouse/Stock.vue"
import Adjustment from "@/views/transaction/warehouse/Adjustment.vue"
import Payable from "@/views/finance/Payable.vue"
import Receivable from "@/views/finance/Receivable.vue"
import Expense from "@/views/finance/Expense.vue"

const routes = [
  { path: "/login", name: "Login", component: Login },
  { path: "/", name: "Dashboard", component: Dashboard, meta: { requiresAuth: true } },
  { path: "/pos", name: "POS", component: POS, meta: { requiresAuth: true } },
  { path: "/master/user", name: "Master.User", component: User, meta: { requiresAuth: true } },
  { path: "/master/contact", name: "Master.Contact", component: Contact, meta: { requiresAuth: true } },
  { path: "/master/item", name: "Master.Item", component: Item, meta: { requiresAuth: true } },
  { path: "/master/unit", name: "Master.Unit", component: Unit, meta: { requiresAuth: true } },
  { path: "/setting/menu", name: "Setting.Menu", component: Menu, meta: { requiresAuth: true } },
  { path: "/setting/role", name: "Setting.Role", component: Role, meta: { requiresAuth: true } },
  { path: "/setting/general", name: "Setting.General", component: General, meta: { requiresAuth: true } },
  { path: "/setting/database", name: "Setting.Database", component: Database, meta: { requiresAuth: true } },
  { path: "/transaction/receive", name: "Transaction.Receive", component: Received, meta: { requiresAuth: true } },
  { path: "/transaction/sale", name: "Transaction.Sale", component: Sale, meta: { requiresAuth: true } },
  { path: "/transaction/warehouse/stock", name: "Transaction.Warehouse.Stock", component: Stock, meta: { requiresAuth: true } },
  { path: "/transaction/warehouse/adjustment", name: "Transaction.Warehouse.Adjustment", component: Adjustment, meta: { requiresAuth: true } },
  { path: "/finance/payable", name: "Finance.Payable", component: Payable, meta: { requiresAuth: true } },
  { path: "/finance/receivable", name: "Finance.Receivable", component: Receivable, meta: { requiresAuth: true } },
  { path: "/finance/expense", name: "Finance.Expense", component: Expense, meta: { requiresAuth: true } }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem("token"); // atau cek Vuex/Pinia store

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: "Login" }); // redirect ke login
    } else {
        next(); // lanjut
    }
});

export default router;
