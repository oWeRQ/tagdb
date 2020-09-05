import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

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
}).$mount('#app');
