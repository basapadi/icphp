import qs from 'qs'

const state = {
  settings: null
}

const getters = {
  settings: (state) => state.settings,
}

const mutations = {
  setSettings(state, form) {
    state.settings = form
  }
}

const actions = {
  async all({ commit, rootState }, payload) { 
    try {
        const resp = await axios.get('/api/setting/all', {
            params: payload,
            paramsSerializer: params => {
                return qs.stringify(params, { arrayFormat: 'repeat' })
            }
        });
        commit('setSettings', resp.data)
        return resp
    } catch (err) {
        throw err
    }
  },
  async save({ commit, rootState }, payload) {
        try {
            const formData = new FormData();
            for (const key in payload) {
                if (Object.hasOwnProperty.call(payload, key)) {
                    const value = payload[key];

                    // Jika value bukan File / Blob, skip (misal string URL lama)
                    if (key === 'gambar' && typeof value === 'string') continue;

                    // Untuk multiple file, bisa array File[]
                    if (Array.isArray(value)) {
                    value.forEach(v => formData.append(key, v));
                    } else {
                    formData.append(key, value);
                    }
                }
            }
            const resp = await axios.post('/api/setting/save', formData);
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
