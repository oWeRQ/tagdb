import './bootstrap';

import Vue from 'vue';

import store from './store';
import router from './router';
import vuetify from './vuetify';

import App from './components/App.vue';
import AuthDialog from './components/common/AuthDialog.vue';
import ConfirmDialog from './components/common/ConfirmDialog.vue';

const app = new Vue({
    store,
    router,
    vuetify,
    render(h) {
        return h(App, [
            h(AuthDialog),
            h(ConfirmDialog, { ref: 'confirm' }),
        ]);
    },
    mounted() {
        this.$store.dispatch('init');
    },
    methods: {
        confirm(title, text = null) {
            return this.$refs.confirm.show(title, text);
        },
    },
}).$mount('#app');
