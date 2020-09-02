import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';

import App from './components/App.vue';
import Index from './components/Index.vue';

const routes = [
    {
        path: '/',
        component: Index,
    },
];

Vue.use(VueRouter);

const router = new VueRouter({ routes });

const app = new Vue({
    template: '<App></App>',
    router,
    components: {
        App,
    },
}).$mount('#app');
