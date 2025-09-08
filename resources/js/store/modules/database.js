import qs from 'qs'

const state = {
  form: null
}

const getters = {
  getForm: (state) => state.form,
}

const mutations = {
  setForm(state, form) {
    state.form = form
  }
}

const actions = {
    async edit({ commit, rootState }, payload) { 
          try {
              const resp = await axios.get('/api/database/edit', {
                  params: payload,
                  paramsSerializer: params => {
                      return qs.stringify(params, { arrayFormat: 'repeat' })
                  }
              });
              commit('setForm', resp.data)
              return resp
          } catch (err) {
              throw err
          }
    },

    async test({ commit, rootState }, payload) { 
          try {
              const resp = await axios.post('/api/database/test',payload);
              return resp
          } catch (err) {
              throw err
          }
    },

    async saveLocalConfig({ commit, rootState }, payload) { 
          try {
              const resp = await axios.post('/api/database/save-local-config',payload);
              return resp
          } catch (err) {
              throw err
          }
    },
    async runCommand({ commit, rootState }, payload) { 
          try {
              const resp = await axios.post('/api/database/run-command',payload);
              return resp
          } catch (err) {
              throw err
          }
    },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}