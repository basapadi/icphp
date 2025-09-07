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
    async get({ commit, rootState }, payload) { 
          try {
              const resp = await axios.get('/api/database/form', {
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
    }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}