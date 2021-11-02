import Index from './components/Index.vue';
import Crud from './components/Crud.vue';
import TagForm from './components/TagForm.vue';
import PresetForm from './components/PresetForm.vue';
import Preset from './components/Preset.vue';

export default [
    {
        path: '',
        name: 'index',
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
                { text: 'Color', value: 'color' },
                { text: 'Fields', value: 'fields.length', sortable: false },
                { text: 'Entities Count', value: 'entities_count' },
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
    {
        path: '/presets',
        component: Crud,
        props: {
            form: PresetForm,
            title: 'Presets',
            resource: '/api/v1/presets',
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
                { text: 'Name', value: 'name' },
                { text: 'Sort', value: 'sort' },
                { text: 'Query', value: 'query' },
            ],
            editable: [
                { text: 'Name', value: 'name' },
                { text: 'Sort', value: 'sort' },
                { text: 'Query', value: 'query' },
            ],
        },
    },
    {
        path: '/projects',
        component: Crud,
        props: {
            title: 'Projects',
            resource: '/api/v1/projects',
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
                { text: 'Name', value: 'name' },
            ],
            editable: [
                { text: 'Name', value: 'name' },
            ],
        },
    },
    {
        path: '/users',
        component: Crud,
        props: {
            title: 'Users',
            resource: '/api/v1/users',
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
                { text: 'Email', value: 'email' },
            ],
            editable: [
                { text: 'Email', value: 'email' },
            ],
        },
    },
    {
        path: '/presets/:name',
        name: 'preset',
        component: Preset,
        props: {
            resource: '/api/v1/entities',
        },
    }
];
