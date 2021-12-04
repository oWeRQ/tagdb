import EntityList from './components/entity/EntityList.vue';
import EntityPreset from './components/entity/EntityPreset.vue';
import Crud from './components/crud/CrudList.vue';
import TagDialog from './components/tag/TagDialog.vue';
import FieldDialog from './components/field/FieldDialog.vue';
import PresetDialog from './components/preset/PresetDialog.vue';
import ProjectDialog from './components/project/ProjectDialog.vue';

export default [
    {
        path: '',
        name: 'index',
        component: EntityList,
    },
    {
        path: '/presets/:name',
        name: 'preset',
        component: EntityPreset,
    },
    {
        path: '/tags',
        component: Crud,
        props: {
            dialog: TagDialog,
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
            dialog: FieldDialog,
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
            dialog: PresetDialog,
            title: 'Presets',
            resource: '/api/v1/presets',
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
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
            dialog: ProjectDialog,
            title: 'Projects',
            resource: '/api/v1/projects',
            defaultItem: {},
            columns: [
                { text: 'ID', value: 'id' },
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
                { text: 'Name', value: 'name' },
                { text: 'Email', value: 'email' },
            ],
            editable: [
                { text: 'Name', value: 'name' },
                { text: 'Email', value: 'email' },
            ],
        },
    },
];
