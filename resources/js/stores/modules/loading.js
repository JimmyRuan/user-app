// resources/js/store/modules/loading.js
const state = () => ({
    isLoading: false
});

const mutations = {
    SET_LOADING(state, status) {
        state.isLoading = status;
    },
};

const actions = {
    showLoading({ commit }) {
        commit('SET_LOADING', true);
    },
    hideLoading({ commit }) {
        commit('SET_LOADING', false);
    }
};

const getters = {
    isLoading: (state) => state.isLoading
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,
};
