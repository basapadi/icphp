import qs from 'qs'

const state = {
  menus: null,
  menuRoles: null,
  app: null
}

const getters = {
  getMenus: (state) => state.menus,
  getMenuRoles: (state) => state.menuRoles,
  getApp: (state) => state.app,
}

const mutations = {
  setMenus(state, menus) {
    state.menus = menus
  },
  setMenuRoles(state, menuRoles) {
    state.menuRoles = menuRoles
  },
  setApp(state, app) {
    state.app = app
  }
}

const actions = {
  async getMenu({ commit, rootState }, payload) {
        try {
            const resp = await axios.get('/api/auth/menus', {
                params: payload,
                paramsSerializer: params => {
                    return qs.stringify(params, { arrayFormat: 'repeat' })
                }
            });
            commit('setMenus', resp.data.menus)
            commit('setMenuRoles', resp.data.menu_roles)
            commit('setApp', resp.data.app)
            return resp
        } catch (err) {
          if (err.response && err.response.status === 401) {
            window.location.reload()
            // hapus token & user dari store/localStorage
            commit('auth/setToken', null, { root: true })
            commit('auth/setUser', null, { root: true })
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            // redirect ke login
            router.push('/login')
          }
          throw err
        }
    }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}