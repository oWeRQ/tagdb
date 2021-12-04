import './bootstrap';

import Vue from 'vue';
import axios from 'axios';

import store from './store';
import router from './router';
import vuetify from './vuetify';

import App from './components/App.vue';
import AuthDialog from './components/AuthDialog.vue';
import ConfirmDialog from './components/ConfirmDialog.vue';

const app = new Vue({
    store,
    router,
    vuetify,
    template: `<App>
        <AuthDialog
            :visible="isAuth"
            @success="authSuccess"
        />
        <ConfirmDialog ref="confirm"></ConfirmDialog>
    </App>`,
    components: {
        App,
        AuthDialog,
        ConfirmDialog,
    },
    data() {
        return {
            isReady: false,
            isAuth: false,
            account: null,
            currentProject: null,
            projects: [],
            tags: [],
            presets: [],
        };
    },
    mounted() {
        axios.interceptors.response.use(response => {
            return response;
        }, error => {
            if (error.response.status === 401) {
                this.logoutSuccess();
                return new Promise(() => {});
            }
            return Promise.reject(error);
        });

        this.getAccount().then(() => {
            this.authSuccess();
        });
    },
    methods: {
        reloadContent() {
            this.isReady = false;
            this.$nextTick(() => {
                this.isReady = true;
            });
        },
        authSuccess() {
            this.isReady = true;
            this.isAuth = false;
            if (!this.account) {
                this.getAccount();
            }
            this.getCurrentProject();
            this.getProjects();
            this.getProjectData();
        },
        logoutSuccess() {
            this.isReady = false;
            this.isAuth = true;
            this.account = null;
            this.currentProject = null;
        },
        logout() {
            return axios.post('/logout').then(() => {
                this.logoutSuccess();
            });
        },
        getProjectData() {
            return Promise.all([
                this.getTags(),
                this.getPresets(),
            ]);
        },
        switchProject(project) {
            this.currentProject = project;
            return axios.post('/api/v1/account/switch-project', project).then(response => {
                this.getProjectData();
                this.reloadContent();
            });
        },
        getAccount() {
            this.account = null;
            return axios.get('/api/v1/account').then(response => {
                this.account = response.data;
            });
        },
        getCurrentProject() {
            this.currentProject = null;
            return axios.get('/api/v1/account/current-project').then(response => {
                this.currentProject = response.data.data;
            });
        },
        getProjects() {
            this.projects = [];
            return axios.get('/api/v1/account/projects').then(response => {
                this.projects = response.data.data;
            });
        },
        getTags() {
            this.tags = [];
            return axios.get('/api/v1/tags').then(response => {
                this.tags = response.data.data;
            });
        },
        getPresets() {
            this.presets = [];
            return axios.get('/api/v1/presets').then(response => {
                this.presets = response.data.data;
            });
        },
        confirm(title, text = null) {
            return this.$refs.confirm.show(title, text);
        },
    },
}).$mount('#app');
