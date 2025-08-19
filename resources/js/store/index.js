import { createStore } from 'vuex'
import auth from './modules/auth'
import menu from './modules/menu'
import user from './modules/user'
import unit from './modules/unit'
import role from './modules/role'

export default createStore({
  modules: {
    auth,
    menu,
    user,
    unit,
    role
  }
})