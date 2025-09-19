import qs from 'qs'

const state = {
  rows: null,
  total: null,
  columns: null,
  form: null
}

const getters = {
  getRows: (state) => state.rows,
  getTotal: (state) => state.total,
  getColumns: (state) => state.columns,
  getForm: (state) => state.form,
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
  },
  setForm(state, form) {
    state.form = form
  }
}

const actions = {
    async grid({ commit, rootState }, payload) { 
        try {
            const resp = await axios.get('/api/sale/order/grid', {
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
    },
    async form({ commit, rootState }, payload) { 
        try {
            const resp = await axios.get('/api/sale/order/form', {
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
    async delete({ commit, rootState }, id) {
        try {
            const resp = await axios.delete('/api/sale/order/'+id);
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