import qs from 'qs'

const state = {
  rows: null,
  total: null,
  columns: null,
  form: null,
  queries: null
}

const getters = {
  getRows: (state) => state.rows,
  getTotal: (state) => state.total,
  getColumns: (state) => state.columns,
  getForm: (state) => state.form,
  getQueries: (state) => state.queries,
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
  },
  setQueries(state, queries) {
    state.queries = queries
  }
}

const actions = {
    async grid({ commit, rootState }, payload) { 
        try {
            const resp = await axios.get('/api/report/grid', {
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
    async getQueryList({ commit, rootState }, payload) { 
        try {
            const resp = await axios.get('/api/report/queries', {
                params: payload,
                paramsSerializer: params => {
                    return qs.stringify(params, { arrayFormat: 'repeat' })
                }
            });
            commit('setQueries', resp.data.data)
            return resp
        } catch (err) {
            throw err
        }
    },
    async delete({ commit, rootState }, id) {
        try {
            const resp = await axios.delete('/api/report/'+id);
            return resp
        } catch (err) {
            throw err
        }
    },
    async getSchema({commit, rootState}, payload){
        try {
            const resp = await axios.get('/api/report/get-schemas', {
                params: payload,
                paramsSerializer: params => {
                    return qs.stringify(params, { arrayFormat: 'repeat' })
                }
            });
            commit('setQueries', resp.data.data)
            return resp
        } catch (err) {
            throw err
        }
    },
    async preview({commit, rootState}, payload){
        try {
            const resp = await axios.post('/api/report/preview', payload);
            return resp
        } catch (err) {
            throw err
        }
    },
    async saveQuery({ commit, rootState }, payload) {
        try {
            const resp = await axios.post('/api/report/save-query', payload);
            return resp
        } catch (err) {
            throw err
        }
    },
    async deleteQuery({ commit, rootState }, name) {
        try {
            const resp = await axios.delete('/api/report/delete-query/'+name);
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