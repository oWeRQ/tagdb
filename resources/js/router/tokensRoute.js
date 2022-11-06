import api from '../api';
import Crud from '../components/crud/CrudList.vue';

export default {
    component: Crud,
    props: {
        title: 'Tokens',
        api: api.tokens,
        defaultItem: {},
        columns: [
            { text: 'ID', value: 'id' },
            { text: 'Name', value: 'name' },
            { text: 'ApiKey', value: 'apikey' },
        ],
        editable: [
            { text: 'Name', value: 'name' },
        ],
    },
};
