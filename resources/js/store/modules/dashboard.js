import qs from 'qs'

const state = {
  data: null,
}

const getters = {
  getData: (state) => state.rows,
}

const mutations = {
  setData(state, data) {
    state.data = data
  }
}

const actions = {
    async data({ commit, rootState }, payload) { 
        try {
            const resp = await axios.get('/api/dashboard/data', {
                params: payload,
                paramsSerializer: params => {
                    return qs.stringify(params, { arrayFormat: 'repeat' })
                }
            });
            commit('setData', resp.data.data)
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