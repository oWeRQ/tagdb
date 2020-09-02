import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

import App from './components/App.vue';
import Index from './components/Index.vue';
import Tags from './components/Tags.vue';

Vue.use(Vuetify);
Vue.use(VueRouter);

const app = new Vue({
    template: '<App></App>',
    vuetify: new Vuetify({}),
    router: new VueRouter({
        routes: [
            { path: '/', component: Index },
            { path: '/tags', component: Tags },
        ],
    }),
    components: {
        App,
    },
}).$mount('#app');
