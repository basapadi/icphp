import qs from 'qs'

const state = {
    chat: null
}

const getters = {
    getChat: (state) => state.chat,
}

const mutations = {
    setChat(state, chat) {
        state.chat = chat
    }
}

const actions = {
    async saveChat({ commit, rootState }, payload) {
        try {
            const resp = await axios.post('/api/ai-query/ask', payload);
            commit('setChat', resp.data)
            return resp.data
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
