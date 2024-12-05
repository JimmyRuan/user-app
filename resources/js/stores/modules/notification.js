const state = () => ({
    message: null,
    type: 'info',
    timeout: 3000
});

const mutations = {
    SET_MESSAGE(state, { message, type, timeout }) {
        state.message = message;
        state.type = type || 'info';
        state.timeout = timeout; //timeout could be null
    },
    CLEAR_MESSAGE(state) {
        state.message = null;
        state.type = 'info';
        state.timeout = 3000;
    },
};

const actions = {
    triggerNotification({ commit }, { message, type, timeout }) {
        commit('SET_MESSAGE', { message, type, timeout });
    },
    clearNotification({ commit }) {
        commit('CLEAR_MESSAGE');
    },
};

const getters = {
    message: (state) => state.message,
    type: (state) => state.type,
    timeout: (state) => state.timeout,
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};
