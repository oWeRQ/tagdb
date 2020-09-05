import Index from './components/Index.vue';
import Crud from './components/Crud.vue';
import TagForm from './components/TagForm.vue';

export default [
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
            form: TagForm,
            defaultItem: {
                name: '',
                fields: [],
            },
            title: 'Tags',
            resource: '/api/v1/tags',
            columns: [
                { text: 'ID', value: 'id' },
                { text: 'Name', value: 'name' },
                { text: 'Fields', value: 'fields.length' },
            ],
        },
    },
    {
        path: '/fields',
        component: Crud,
        props: {
            title: 'Fields',
            resource: '/api/v1/fields',
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
                { text: 'Tag ID', value: 'tag_id' },
                { text: 'Type', value: 'type' },
                { text: 'Name', value: 'name' },
                { text: 'Code', value: 'code' },
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
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
                { text: 'Entity ID', value: 'entity_id' },
                { text: 'Field ID', value: 'field_id' },
                { text: 'Tag ID', value: 'field.tag_id' },
                { text: 'Type', value: 'field.type' },
                { text: 'Name', value: 'field.name' },
                { text: 'Code', value: 'field.code' },
                { text: 'Content', value: 'content' },
            ],
            editable: [
                { text: 'Entity ID', value: 'entity_id' },
                { text: 'Field ID', value: 'field_id' },
                { text: 'Content', value: 'content' },
            ],
        },
    },
];
