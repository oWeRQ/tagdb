import Vue from 'vue';
import api, { setErrorHandler, setProjectId } from '../api';
import router from '../router';

export default {
    namespaced: false,
    state: {
        isReady: false,
        isAuth: false,
        account: null,
        currentProject: null,
        projects: [],
    },
    mutations: {
        currentProject(state, value) {
            state.currentProject = value;
            setProjectId(value?.id);
        },
        projects(state, value) {
            state.projects = value;
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
            setErrorHandler(error => {
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
            window.sessionStorage.setItem('projectId', project.id);
            commit('currentProject', project);
            return api.account.switchProject(project).then(response => {
                dispatch('fetchProjectData');
                dispatch('reloadContent');
                dispatch('redirectIfPreset');
            });
        },
        fetchAccount({ dispatch, commit }) {
            setProjectId(window.sessionStorage.getItem('projectId'));
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
            return api.account.projects().then(projects => {
                commit('projects', projects);
            });
        },
    },
};
