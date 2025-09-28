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
  },
  setToggleMenu(state, id) {
    const toggle = (items) => items.map(item => ({
      ...item,
      open: item.id === id ? !item.open : item.open,
      sub_items: toggle(item.sub_items || [])
    }))
    state.menus = toggle(state.menus)
  }
}

const actions = {
    async getMenu({ commit, rootState }, payload) {
        try {
            if (state.menus && state.menus.length > 0) {
              return { data: { menus: state.menus, menu_roles: state.menuRoles, app: state.app } }
            }
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
    },
    setActiveMenu({ commit, state }, currentRoute) {
      function traverse(items) {
        return items.map(item => {
          // traverse children dulu
          const children = traverse(item.sub_items || [])

          // cek apakah current item aktif
          const isActive = item.route && item.route !== '#' && currentRoute.startsWith(item.route)

          // parent open kalau ada child aktif
          const hasActiveChild = children.some(c => c.active || c.open)

          return {
            ...item,
            active: isActive,
            open: (item.open ?? false) || isActive || hasActiveChild,
            sub_items: children
          }
        })
      }

      const updated = traverse(state.menus || [])
      commit('setMenus', updated)
  }


}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}