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
            tags: [],
            presets: [],
            fields: [],
        };
    },
    mounted() {
        this.getTags();
        this.getPresets();
        this.getFields();
    },
    methods: {
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
