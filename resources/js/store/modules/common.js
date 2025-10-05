import qs from 'qs'

const state = {
  columns: null
}

const getters = {
  getColumns: (state) => state.columns,
}

const mutations = {
  setColumns(state, columns) {
      state.columns = columns
  }
}

const actions = {
  async saveColumns({ commit, rootState }, payload) {
    try {
        const resp = await axios.post('/api/common/save-columns', payload);
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