import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import axios from 'axios';

import App from './components/App.vue';
import AuthDialog from './components/AuthDialog.vue';
import ConfirmDialog from './components/ConfirmDialog.vue';
import routes from './routes';

Vue.use(Vuetify);
Vue.use(VueRouter);

const app = new Vue({
    template: `<App>
        <AuthDialog
            :visible="isAuth"
            @success="authSuccess"
        />
        <ConfirmDialog ref="confirm"></ConfirmDialog>
    </App>`,
    vuetify: new Vuetify({}),
    router: new VueRouter({
        routes,
    }),
    components: {
        App,
        AuthDialog,
        ConfirmDialog,
    },
    data() {
        return {
            isAuth: false,
            account: null,
            currentProject: null,
            projects: [],
            tags: [],
            presets: [],
            fields: [],
        };
    },
    mounted() {
        this.getAccount().then(() => {
            this.authSuccess();
        }).catch(error => {
            if (error.response.status === 401) {
                this.isAuth = true;
            } else {
                console.error('account', error.response);
            }
        });
    },
    methods: {
        authSuccess() {
            this.isAuth = false;
            if (!this.account) {
                this.getAccount();
            }
            this.getCurrentProject();
            this.getAll();
        },
        logout() {
            return axios.post('/logout').then(response => {
                this.isAuth = true;
                this.account = null;
                this.currentProject = null;
            }).catch(error => {
                console.error('logout', error.response);
            });
        },
        getAccount() {
            return axios.get('/api/v1/account').then(response => {
                this.account = response.data;
            });
        },
        setCurrentProject(project) {
            this.currentProject = null;
            return axios.put('/api/v1/current-project', project).then(response => {
                this.currentProject = project;
                this.getAll();
            });
        },
        getCurrentProject() {
            return axios.get('/api/v1/current-project').then(response => {
                this.currentProject = response.data.data;
            });
        },
        getAll() {
            this.getProjects();
            this.getTags();
            this.getPresets();
            this.getFields();
        },
        getProjects() {
            return axios.get('/api/v1/projects').then(response => {
                this.projects = response.data.data;
            });
        },
        getTags() {
            return axios.get('/api/v1/tags').then(response => {
                this.tags = response.data.data;
            });
        },
        getPresets() {
            return axios.get('/api/v1/presets').then(response => {
                this.presets = response.data.data;
            });
        },
        getFields() {
            return axios.get('/api/v1/fields').then(response => {
                this.fields = response.data.data;
            });
        },
        confirm(title, text = null) {
            return this.$refs.confirm.show(title, text);
        },
    },
}).$mount('#app');
