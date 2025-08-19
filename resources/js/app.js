import "./bootstrap";
import { createApp } from "vue"
import { createPinia } from 'pinia'
import App from "./App.vue"
import router from "./router"
import store from './store'
import '../css/app.css';
import * as helpers from "./helpers/datautils";

const app = createApp(App)
app.use(router)
app.use(store)
app.use(createPinia())

app.config.globalProperties.$helpers = helpers;

app.mount("#app")
