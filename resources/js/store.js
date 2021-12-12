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
        ready(state) {
            state.isReady = true;
        },
        pending(state) {
            state.isReady = false;
        },
        authResolved(state, { account, currentProject }) {
            state.isReady = !!currentProject;
            state.isAuth = false;
            state.account = account;
            state.currentProject = currentProject;
        },
        authRejected(state) {
            state.isReady = false;
            state.isAuth = true;
            state.account = null;
            state.currentProject = null;
        },
    },
    actions: {
        init({ dispatch, commit }) {
            axios.interceptors.response.use(response => {
                return response;
            }, error => {
                if (error.response.status === 401) {
                    commit('authRejected');
                    return new Promise(() => {});
                }
                return Promise.reject(error);
            });

            return dispatch('fetchAccount');
        },
        register({ dispatch }, data) {
            return api.auth.register(data).then(() => {
                return dispatch('fetchAccount');
            });
        },
        login({ dispatch }, data) {
            return api.auth.login(data).then(() => {
                return dispatch('fetchAccount');
            });
        },
        logout({ commit }) {
            return api.auth.logout().then(() => {
                commit('authRejected');
            });
        },
        reloadContent({ commit }) {
            commit('pending');
            Vue.nextTick(() => {
                commit('ready');
            });
        },
        redirectIfPreset() {
            if (router.history.current.name === 'preset') {
                router.push({ name: 'index' });
            }
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
                dispatch('redirectIfPreset');
            });
        },
        fetchAccount({ dispatch, commit }) {
            return Promise.all([
                api.account.index(),
                api.account.currentProject(),
            ]).then(([account, currentProject]) => {
                commit('authResolved', { account, currentProject });
                dispatch('fetchProjects');
                if (currentProject) {
                    dispatch('fetchProjectData');
                }
            });
        },
        fetchProjects({ commit }) {
            commit('projects', []);
            return api.account.projects().then(projects => {
                commit('projects', projects);
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
