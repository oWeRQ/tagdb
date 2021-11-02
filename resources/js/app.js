import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import axios from 'axios';

import App from './components/App.vue';
import ConfirmDialog from './components/ConfirmDialog.vue';
import routes from './routes';

Vue.use(Vuetify);
Vue.use(VueRouter);

const app = new Vue({
    template: '<App><ConfirmDialog ref="confirm"></ConfirmDialog></App>',
    vuetify: new Vuetify({}),
    router: new VueRouter({
        routes,
    }),
    components: {
        App,
        ConfirmDialog,
    },
    data() {
        return {
            isGuest: true,
            currentProject: null,
            projects: [],
            tags: [],
            presets: [],
            fields: [],
        };
    },
    methods: {
        authSuccess() {
            this.isGuest = false;
            this.getCurrentProject();
            this.getAll();
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
