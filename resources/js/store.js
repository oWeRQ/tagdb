import Vue from 'vue';
import Vuex from 'vuex';

import axios from 'axios';
import api, { setProjectId } from './api';
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
            setProjectId(value?.id);
        },
        projects(state, value) {
            state.projects = value;
        },
        presets(state, value) {
            state.presets = value;
        },
        tagsAndFields(state, { tags, fields }) {
            state.tags = tags;
            state.fields = fields;
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
            this.commit('currentProject', currentProject);
        },
        authRejected(state) {
            state.isReady = false;
            state.isAuth = true;
            state.account = null;
            this.commit('currentProject', null);
        },
    },
    actions: {
        init({ dispatch, commit }) {
            axios.interceptors.response.use(response => {
                return response;
            }, error => {
                if (error.response?.status === 401) {
                    commit('authRejected');
                    return new Promise(() => {});
                }
                return Promise.reject(error);
            });

            return dispatch('fetchAccount');
        },
        redirectIfPreset() {
            if (router.history.current.name === 'preset') {
                router.push({ name: 'index' });
            }
        },
        reloadContent({ commit }) {
            commit('pending');
            Vue.nextTick(() => {
                commit('ready');
            });
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
        switchProject({ dispatch, commit }, project) {
            commit('currentProject', project);
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
        fetchProjectData({ dispatch }) {
            return Promise.all([
                dispatch('fetchPresets'),
                dispatch('fetchTagsAndFields'),
            ]);
        },
        fetchProjects({ commit }) {
            return api.account.projects().then(projects => {
                commit('projects', projects);
            });
        },
        fetchPresets({ commit }) {
            return api.presets.index({ sort: 'name' }).then(presets => {
                commit('presets', presets);
            });
        },
        fetchTagsAndFields({ commit }) {
            return Promise.all([
                api.tags.index(),
                api.fields.index(),
            ]).then(([tags, fields]) => {
                commit('tagsAndFields', { tags, fields });
            });
        },
        saveProject({ dispatch }, project) {
            dispatch('fetchProjects');
        },
        deleteProject({ dispatch }, project) {
            dispatch('fetchProjects');
        },
        savePreset({ dispatch }, preset) {
            dispatch('fetchPresets');
        },
        deletePreset({ dispatch }, preset) {
            dispatch('fetchPresets');
        },
        createTag({ dispatch }, tag) {
            return api.tags.store(tag).then((response) => {
                dispatch('saveTag', response);
                return response;
            });
        },
        saveTag({ dispatch }, tag) {
            dispatch('fetchTagsAndFields');
        },
        deleteTag({ dispatch }, tag) {
            dispatch('fetchTagsAndFields');
        },
        saveField({ dispatch }, field) {
            dispatch('fetchTagsAndFields');
        },
        deleteField({ dispatch }, field) {
            dispatch('fetchTagsAndFields');
        },
        saveEntity({ dispatch }, entity) {
            dispatch('fetchTagsAndFields');
        },
        deleteEntity({ dispatch }, entity) {
            dispatch('fetchTagsAndFields');
        },
    },
});
