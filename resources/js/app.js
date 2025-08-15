import "./bootstrap";
import { createApp } from "vue"
import { createPinia } from 'pinia'
import App from "./App.vue"
import router from "./router"
import '../css/app.css';
import { useAuthStore } from './stores/auth'
const app = createApp(App)
app.use(router)
app.use(createPinia())
app.mount("#app")

const auth = useAuthStore()
if (auth.token) {
  auth.fetchUser()
}
