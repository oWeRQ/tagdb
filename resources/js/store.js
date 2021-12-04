import Vue from 'vue';
import Vuex from 'vuex';

import api from './api';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        isReady: false,
        isAuth: false,
        account: null,
        currentProject: null,
        projects: [],
        tags: [],
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
        presets(state, value) {
            state.presets = value;
        },
    },
    actions: {
        register(context, data) {
            return api.auth.register(data).then(() => {
                context.dispatch('authSuccess');
            })
        },
        login(context, data) {
            return api.auth.login(data).then(() => {
                context.dispatch('authSuccess');
            });
        },
        logout(context) {
            return api.auth.logout().then(() => {
                context.dispatch('logoutSuccess');
            });
        },
        reloadContent(context) {
            context.commit('isReady', false);
            Vue.nextTick(() => {
                context.commit('isReady', true);
            });
        },
        authSuccess(context) {
            context.commit('isReady', true);
            context.commit('isAuth', false);
            if (!context.state.account) {
                context.dispatch('getAccount');
            }
            context.dispatch('getCurrentProject');
            context.dispatch('getProjects');
            context.dispatch('getProjectData');
        },
        logoutSuccess(context) {
            context.commit('isReady', false);
            context.commit('isAuth', true);
            context.commit('account', null);
            context.commit('currentProject', null);
        },
        getProjectData(context) {
            return Promise.all([
                context.dispatch('getTags'),
                context.dispatch('getPresets'),
            ]);
        },
        switchProject(context, project) {
            context.commit('currentProject', project)
            return api.account.switchProject(project).then(response => {
                context.dispatch('getProjectData');
                context.dispatch('reloadContent');
            });
        },
        getAccount(context) {
            context.commit('account', null);
            return api.account.index().then(response => {
                context.commit('account', response.data);
            });
        },
        getCurrentProject(context) {
            context.commit('currentProject', null);
            return api.account.currentProject().then(response => {
                context.commit('currentProject', response.data.data);
            });
        },
        getProjects(context) {
            context.commit('projects', []);
            return api.account.projects().then(response => {
                context.commit('projects', response.data.data);
            });
        },
        getTags(context) {
            context.commit('tags', []);
            return api.tags.index().then(response => {
                context.commit('tags', response.data.data);
            });
        },
        getPresets(context) {
            context.commit('presets', []);
            return api.presets.index().then(response => {
                context.commit('presets', response.data.data);
            });
        },
    },
});
