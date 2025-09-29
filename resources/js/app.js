import "./bootstrap";
import { createApp } from "vue"
import { createPinia } from 'pinia'
import Vue3ConfirmDialog from 'vue3-confirm-dialog';
import App from "./App.vue"
import router from "./router"
import store from './store'
import '../css/app.css';
import * as helpers from "./helpers/datautils";
import 'vue3-confirm-dialog/style';
import "flatpickr/dist/flatpickr.css";
import * as icons from "./helpers/icons";

const app = createApp(App)
app.config.globalProperties.$helpers = helpers;
app.use(router)
app.use(store)
app.use(createPinia())
app.use(Vue3ConfirmDialog);
app.component('vue3-confirm-dialog', Vue3ConfirmDialog.default)

for (const [name, component] of Object.entries(icons)) {
  app.component(name, component);
}

app.mount("#app")
