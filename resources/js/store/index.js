import { createStore } from 'vuex'
import auth from './modules/auth'
import menu from './modules/menu'

export default createStore({
  modules: {
    auth,
    menu
  }
})