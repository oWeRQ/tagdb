import './bootstrap';

import Vue from 'vue';
import { mapActions } from 'vuex';
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
        <AuthDialog />
        <ConfirmDialog ref="confirm" />
    </App>`,
    components: {
        App,
        AuthDialog,
        ConfirmDialog,
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
        ...mapActions([
            'logoutSuccess',
            'getAccount',
            'authSuccess',
        ]),
        confirm(title, text = null) {
            return this.$refs.confirm.show(title, text);
        },
    },
}).$mount('#app');
