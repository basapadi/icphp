const state = {
  user: JSON.parse(localStorage.getItem('user')) || null,
  token: localStorage.getItem('token') || null
}

const getters = {
  getUser: (state) => state.user,
  getToken: (state) => state.token
}

const mutations = {
  setUser(state, user) {
    state.user = user
  },
  setToken(state, token) {
    state.token = token
  }
}

const actions = {
  async login({commit},payload) {
    try {
      const resp = await axios.post('/api/auth/login', payload)
      // this.token = resp.data.access_token
      // this.user = resp.data.user || null
      commit('setToken', resp.data.access_token)
      commit('setUser', resp.data.user || null)

      localStorage.setItem('token', resp.data.access_token)
      localStorage.setItem('user', JSON.stringify(resp.data.user))
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${resp.data.access_token}`; //inisiasi token
      return true
    } catch (err) {
      throw err
    }
  },
  async getInit({commit}) {
    try {
      const resp = await axios.get('/api/auth/init')
      return resp
    } catch (err) {
      throw err
    }
  },
  async testDb({ commit, rootState }, payload) { 
    try {
        const resp = await axios.post('/api/auth/test',payload);
        return resp
    } catch (err) {
        throw err
    }
  },

  async saveLocalConfig({ commit, rootState }, payload) { 
    try {
        const resp = await axios.post('/api/auth/save-local-config',payload);
        return resp
    } catch (err) {
        throw err
    }
  },
  async fetchUser() {
    if (!state.token) return
    try {
      const resp = await axios.get('/api/auth/me', {
        headers: {
          Authorization: `Bearer ${state.token}`
        }
      })
        state.user = resp.data
    } catch {
      // this.logout()
    }
    },
    async logout({commit}) {
      if (!state.token) return
      await axios.post('/api/auth/logout', {
        headers: {
          Authorization: `Bearer ${state.token}`
        }
      })
      commit('setToken', null)
      commit('setUser', null)
      localStorage.removeItem('token')
      localStorage.removeItem('user')
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}