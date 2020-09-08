import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import axios from 'axios';

import App from './components/App.vue';
import routes from './routes';

Vue.use(Vuetify);
Vue.use(VueRouter);

const app = new Vue({
    template: '<App></App>',
    vuetify: new Vuetify({}),
    router: new VueRouter({
        routes,
    }),
    components: {
        App,
    },
    data() {
        return {
            tags: [],
            presets: [],
        };
    },
    mounted() {
        this.getTags();
        this.getPresets();
    },
    methods: {
        getTags() {
            axios.get('/api/v1/tags').then(response => {
                this.tags = response.data.data;
            });
        },
        getPresets() {
            axios.get('/api/v1/presets').then(response => {
                this.presets = response.data.data;
            });
        },
    },
}).$mount('#app');
