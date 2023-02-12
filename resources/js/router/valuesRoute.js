import api from '../api';
import Crud from '../components/crud/CrudList.vue';

export default {
    component: Crud,
    props: {
        title: 'Values',
        api: api.values,
        defaultItem: {},
        columns: [
            { title: 'ID', key: 'id' },
            { title: 'Entity ID', key: 'entity_id' },
            { title: 'Field ID', key: 'field_id' },
            { title: 'Tag ID', key: 'field.tag_id' },
            { title: 'Type', key: 'field.type' },
            { title: 'Name', key: 'field.name' },
            { title: 'Code', key: 'field.code' },
            { title: 'Content', key: 'content' },
        ],
        editable: [
            { text: 'Entity ID', value: 'entity_id' },
            { text: 'Field ID', value: 'field_id' },
            { text: 'Content', value: 'content' },
        ],
    },
};
