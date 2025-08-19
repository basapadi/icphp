import { createStore } from 'vuex'
import auth from './modules/auth'
import menu from './modules/menu'
import user from './modules/user'
import unit from './modules/unit'
import item from './modules/item'
import dataMenu from './modules/dataMenu'

export default createStore({
  modules: {
    auth,
    menu,
    user,
    unit,
    item,
    dataMenu,
  }
})