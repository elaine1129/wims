import { useRoute } from "vue-router";
import { createStore } from "vuex";
import createPersistedState from 'vuex-persistedstate'
import Cookies from 'js-cookie'
import axiosClient from "../axios";
const store = createStore({
    state: {
        user: {
            data: {},
            token: sessionStorage.getItem('TOKEN')
        }
    },
    getters: {},
    actions: {
        login({ commit }, user) {
            return axiosClient.post('/login', user)
                .then(({ data }) => {
                    commit('setUser', data);
                    return data;
                })
        },
        logout({ commit }) {
            return axiosClient.post('/logout')
                .then(response => {
                    commit('logout');
                    return response;
                })
        }
    },
    mutations: {
        logout: state => {

            state.user.data = {};
            state.user.token = null;
            sessionStorage.removeItem("TOKEN");
        },
        setUser: (state, userData) => {
            state.user.token = userData.token;
            state.user.data = userData.user;
            sessionStorage.setItem("TOKEN", userData.token);
        }
    },
    modules: {},
    plugins: [createPersistedState({
        storage: window.sessionStorage,
    })],

})

export default store;