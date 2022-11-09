import api from '../api';
import Crud from '../components/crud/CrudList.vue';
import TokenForm from '../components/token/TokenForm.vue';

export default {
    component: Crud,
    props: {
        form: TokenForm,
        title: 'Tokens',
        api: api.tokens,
        defaultItem: {},
        columns: [
            { text: 'ID', value: 'id' },
            { text: 'Name', value: 'name' },
            { text: 'Api Key', value: 'apikey' },
            { text: 'OpenApi', value: 'openapi' },
        ],
        editable: [
            { text: 'Name', value: 'name' },
        ],
    },
};
