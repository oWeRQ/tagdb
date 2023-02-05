import './bootstrap';

import { createApp, h } from 'vue';

import store from './store';
import router from './router';
import vuetify from './vuetify';

import App from './components/App.vue';
import AuthDialog from './components/common/AuthDialog.vue';
import ConfirmDialog from './components/common/ConfirmDialog.vue';
import DialogStack from './components/common/DialogStack.vue';

import title from './directives/title';
import autoselect from './directives/autoselect';

const app = createApp({
    render() {
        return h(App, [
            h(AuthDialog),
            h(DialogStack, { ref: 'dialogStack' }),
        ]);
    },
    mounted() {
        this.$store.dispatch('init');
    },
    methods: {
        confirm(title, text = null) {
            return this.showDialog(ConfirmDialog, {title, text});
        },
        showDialog(component, bind, on) {
            return this.$refs.dialogStack.show(component, bind, on);
        },
    },
});

app.use(store);
app.use(router);
app.use(vuetify);

app.directive('title', title);
app.directive('autoselect', autoselect);

app.mount('#app');
