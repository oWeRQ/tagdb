import Vue from 'vue';
import Vuex from 'vuex';

import axios from 'axios';
import api from './api';
import router from './router';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        isReady: false,
        isAuth: false,
        account: null,
        currentProject: null,
        projects: [],
        tags: [],
        fields: [],
        presets: [],
    },
    mutations: {
        isReady(state, value) {
            state.isReady = value;
        },
        isAuth(state, value) {
            state.isAuth = value;
        },
        account(state, value) {
            state.account = value;
        },
        currentProject(state, value) {
            state.currentProject = value;
        },
        projects(state, value) {
            state.projects = value;
        },
        tags(state, value) {
            state.tags = value;
        },
        fields(state, value) {
            state.fields = value;
        },
        presets(state, value) {
            state.presets = value;
        },
    },
    actions: {
        init({ dispatch }) {
            axios.interceptors.response.use(response => {
                return response;
            }, error => {
                if (error.response.status === 401) {
                    dispatch('logoutSuccess');
                    return new Promise(() => {});
                }
                return Promise.reject(error);
            });

            dispatch('fetchAccount').then(() => {
                dispatch('authSuccess');
            });
        },
        register({ dispatch }, data) {
            return api.auth.register(data).then(() => {
                dispatch('fetchAccount');
                dispatch('authSuccess');
            });
        },
        login({ dispatch }, data) {
            return api.auth.login(data).then(() => {
                dispatch('fetchAccount');
                dispatch('authSuccess');
            });
        },
        logout({ dispatch }) {
            return api.auth.logout().then(() => {
                dispatch('logoutSuccess');
            });
        },
        reloadContent({ commit }) {
            commit('isReady', false);
            Vue.nextTick(() => {
                commit('isReady', true);
            });
        },
        authSuccess({ dispatch, commit }) {
            commit('isReady', true);
            commit('isAuth', false);
            dispatch('fetchCurrentProject');
            dispatch('fetchProjects');
            dispatch('fetchProjectData');
        },
        logoutSuccess({ commit }) {
            commit('isReady', false);
            commit('isAuth', true);
            commit('account', null);
            commit('currentProject', null);
        },
        fetchProjectData({ dispatch }) {
            return Promise.all([
                dispatch('fetchTags'),
                dispatch('fetchFields'),
                dispatch('fetchPresets'),
            ]);
        },
        switchProject({ dispatch, commit }, project) {
            commit('currentProject', project)
            return api.account.switchProject(project).then(response => {
                dispatch('fetchProjectData');
                dispatch('reloadContent');
                if (router.history.current.name === 'preset') {
                    router.push({ name: 'index' });
                }
            });
        },
        fetchAccount({ commit }) {
            commit('account', null);
            return api.account.index().then(response => {
                commit('account', response.data);
            });
        },
        fetchCurrentProject({ commit }) {
            commit('currentProject', null);
            return api.account.currentProject().then(response => {
                commit('currentProject', response.data.data);
            });
        },
        fetchProjects({ commit }) {
            commit('projects', []);
            return api.account.projects().then(response => {
                commit('projects', response.data.data);
            });
        },
        fetchTags({ commit }) {
            commit('tags', []);
            return api.tags.index().then(response => {
                commit('tags', response.data.data);
            });
        },
        fetchFields({ commit }) {
            commit('fields', []);
            return api.fields.index().then(response => {
                commit('fields', response.data.data);
            });
        },
        fetchPresets({ commit }) {
            commit('presets', []);
            return api.presets.index().then(response => {
                commit('presets', response.data.data);
            });
        },
    },
});
