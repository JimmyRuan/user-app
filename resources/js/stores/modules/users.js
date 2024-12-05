import axios from 'axios';
import httpClient from "../../services/HttpClient.js";

const state = {
    usersPages: {},
    userDetails: {},
};

const mutations = {
    SET_USERS_PAGE(state, { page, pageData }) {
        state.usersPages = {
            ...state.usersPages,
            [page]: pageData,
        };
    },
    REMOVE_USER_FROM_PAGE(state, { userId, currentPage }) {
        if (state.usersPages[currentPage]) {
            const updatedData = state.usersPages[currentPage].data.filter(user => user.id !== userId);
            state.usersPages[currentPage] = {
                ...state.usersPages[currentPage],
                data: updatedData,
            };
        }
    },
    SET_USER_DETAILS(state, { userId, userDetails }) {
        state.userDetails = {
            ...state.userDetails,
            [userId]: userDetails,
        };

        // Update the corresponding user in usersPages
        Object.keys(state.usersPages).forEach(page => {
            const pageData = state.usersPages[page]?.data || [];
            const userIndex = pageData.findIndex(user => user.id === userId);
            if (userIndex !== -1) {
                pageData[userIndex] = { ...pageData[userIndex], ...userDetails };
                state.usersPages[page] = {
                    ...state.usersPages[page],
                    data: pageData,
                };
            }
        });
    },
    CLEAR_USERS_PAGES(state) {
        state.usersPages = {};
    },
};

const getters = {
    getUsersPage: (state) => (page) => {
        return state.usersPages[page] || null;
    },
    getUserDetails: (state) => (userId) => {
        return state.userDetails[userId] || null;
    },
};

const actions = {
    async fetchUsersPage({ commit, state }, page = 1) {
        // Check if the page is already cached
        if (state.usersPages[page]) {
            return state.usersPages[page];
        }

        try {
            const response = await httpClient.get(`/users?page=${page}`);
            const pageData = response.data;

            commit('SET_USERS_PAGE', { page, pageData });

            return pageData;
        } catch (error) {
            console.error(`Failed to fetch users for page ${page}:`, error);
            throw error;
        }
    },
    async deleteUserInfo({ commit }, { userId, currentPage }) {
        try {
            await axios.delete(`/users/${userId}`);
            // Remove the user from the local state
            commit('REMOVE_USER_FROM_PAGE', { userId, currentPage });
        } catch (error) {
            console.error(`Failed to delete user with ID ${userId}:`, error);
            throw error;
        }
    },
    async fetchUserDetails({ commit, state }, userId) {
        if (state.userDetails[userId]) {
            return state.userDetails[userId];
        }

        try {
            const response = await httpClient.get(`/users/${userId}`);
            const userDetails = response.data;

            commit('SET_USER_DETAILS', { userId, userDetails });

            return userDetails;
        } catch (error) {
            console.error(`Failed to fetch details for user with ID ${userId}:`, error);
            throw error;
        }
    },
    async updateUserDetails({ commit }, { id, data }) {
        try {
            const response = await httpClient.patch(`/users/${id}`, data);
            commit('SET_USER_DETAILS', { userId: id, userDetails: response.data });
            return response.data
        } catch (error) {
            console.error(`Failed to update user with ID ${id}:`, error);
            throw error;
        }
    },
    async createUserDetails({ commit }, data) {
        try {
            const response = await httpClient.post('/users', data);
            commit('CLEAR_USERS_PAGES');
            return response.data;
        } catch (error) {
            console.error('Failed to create user:', error);
            throw error;
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
