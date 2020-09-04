import './bootstrap';

import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

import App from './components/App.vue';
import Index from './components/Index.vue';
import Crud from './components/Crud.vue';

Vue.use(Vuetify);
Vue.use(VueRouter);

const app = new Vue({
    template: '<App></App>',
    vuetify: new Vuetify({}),
    router: new VueRouter({
        routes: [
            {
                path: '',
                component: Index,
                props: {
                    title: 'Entities',
                    resource: '/api/v1/entities',
                },
            },
            {
                path: '/tags',
                component: Crud,
                props: {
                    title: 'Tags',
                    resource: '/api/v1/tags',
                    headers: [
                        { text: 'ID', value: 'id' },
                        { text: 'Name', value: 'name' },
                        { text: 'Fields', value: 'fields.length' },
                        { text: 'Actions', value: 'actions', sortable: false },
                    ],
                },
            },
            {
                path: '/fields',
                component: Crud,
                props: {
                    title: 'Fields',
                    resource: '/api/v1/fields',
                    headers: [
                        { text: 'ID', value: 'id' },
                        { text: 'Tag ID', value: 'tag_id' },
                        { text: 'Type', value: 'type' },
                        { text: 'Name', value: 'name' },
                        { text: 'Code', value: 'code' },
                        { text: 'Actions', value: 'actions', sortable: false },
                    ],
                    editable: [
                        { text: 'Tag ID', value: 'tag_id' },
                        { text: 'Type', value: 'type' },
                        { text: 'Name', value: 'name' },
                        { text: 'Code', value: 'code' },
                    ],
                },
            },
            {
                path: '/values',
                component: Crud,
                props: {
                    title: 'Values',
                    resource: '/api/v1/values',
                    headers: [
                        { text: 'ID', value: 'id' },
                        { text: 'Entity ID', value: 'entity_id' },
                        { text: 'Field ID', value: 'field_id' },
                        { text: 'Tag ID', value: 'field.tag_id' },
                        { text: 'Type', value: 'field.type' },
                        { text: 'Name', value: 'field.name' },
                        { text: 'Code', value: 'field.code' },
                        { text: 'Content', value: 'content' },
                        { text: 'Actions', value: 'actions', sortable: false },
                    ],
                    editable: [
                        { text: 'Entity ID', value: 'entity_id' },
                        { text: 'Field ID', value: 'field_id' },
                        { text: 'Content', value: 'content' },
                    ],
                },
            },
        ],
    }),
    components: {
        App,
    },
}).$mount('#app');
