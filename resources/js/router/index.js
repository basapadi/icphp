import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "@/views/Dashboard.vue";
import POS from "@/views/POS.vue";
import Login from "@/views/Login.vue";
import Contact from "@/views/master/Contact.vue";
import User from "@/views/master/User.vue";
import Item from "@/views/master/Item.vue";
import Unit from "@/views/master/Unit.vue";
import Menu from "@/views/master/Menu.vue";

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
        meta: { requiresAuth: true },
    },
    {
        path: "/pos",
        name: "POS",
        component: POS,
        meta: { requiresAuth: true },
    },
    {
        path: "/master/user",
        name: "Master.User",
        component: User,
        meta: { requiresAuth: true },
    },
    {
        path: "/master/contact",
        name: "Master.Contact",
        component: Contact,
        meta: { requiresAuth: true },
    },
    {
        path: "/master/item",
        name: "Master.Item",
        component: Item,
        meta: { requiresAuth: true },
    },
    {
        path: "/master/unit",
        name: "Master.Unit",
        component: Unit,
        meta: { requiresAuth: true },
    },
    {
        path: "/master/menu",
        name: "Master.Menu",
        component: Menu,
        meta: { requiresAuth: true },
    },
];

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
