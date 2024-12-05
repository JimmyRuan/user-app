// resources/js/store/index.js
import { createStore } from 'vuex';
import notification from "./modules/notification.js";
import loading from "./modules/loading.js";
import users from "./modules/users.js";

const store = createStore({
    modules: {
        notification,
        loading,
        users
    },
    state: {
        currentRoute: '',
        queryParams: {},
    },
    mutations: {
        SET_CURRENT_ROUTE(state, route) {
            state.currentRoute = route;
        },
        SET_QUERY_PARAMS(state, params) {
            state.queryParams = params;
        },
    },
});

export default store;
