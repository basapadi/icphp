import qs from 'qs'

const state = {
  menus:  null,
}

const getters = {
  getMenus: (state) => state.menus,
}

const mutations = {
  setMenus(state, menus) {
    state.menus = menus
  }
}

const actions = {
    async getMenu({ commit, rootState }, payload) {
        const token = rootState.auth.token
        try {
            const resp = await axios.get('/auth/menus', {
                params: payload,
                paramsSerializer: params => {
                    return qs.stringify(params, { arrayFormat: 'repeat' })
                },
                headers: {
                    Authorization: `Bearer ${token}` //set header hanya untuk initalize saja, mengambil state dari store auth
                }
            });
            commit('setMenus', resp.data)
            return resp
        } catch (err) {
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