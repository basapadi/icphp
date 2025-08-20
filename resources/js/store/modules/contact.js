import qs from 'qs'

const state = {
  rows: null,
  total: null,
  columns: null
}

const getters = {
  getRows: (state) => state.rows,
  getTotal: (state) => state.total,
  getColumns: (state) => state.columns,
}

const mutations = {
  setRows(state, rows) {
    state.rows = rows
  },
  setTotal(state, total) {
    state.total = total
  },
  setColumns(state, columns) {
      state.columns = columns
  }
}

const actions = {
    async grid({ commit, rootState }, payload) { 
        try {
            const resp = await axios.get('/api/contact/grid', {
                params: payload,
                paramsSerializer: params => {
                    return qs.stringify(params, { arrayFormat: 'repeat' })
                }
            });
            commit('setRows', resp.data.rows)
            commit('setTotal', resp.data.total)
            commit('setColumns', resp.data.columns)
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