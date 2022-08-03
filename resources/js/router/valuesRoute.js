import api from '../api';
import Crud from '../components/crud/CrudList.vue';

export default {
    component: Crud,
    props: {
        title: 'Values',
        api: api.values,
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
};
