import api from '../api';
import CrudList from '../components/crud/CrudList.vue';
import TokenForm from '../components/token/TokenForm.vue';
import ColumnOpenapi from '../components/token/ColumnOpenapi.vue';

export default {
    component: CrudList,
    props: {
        form: TokenForm,
        title: 'Tokens',
        api: api.tokens,
        defaultItem: {},
        columns: [
            { title: 'ID', key: 'id' },
            { title: 'Name', key: 'name' },
            { title: 'Api Key', key: 'apikey' },
            { title: 'OpenApi', key: 'openapi', sortable: false, component: ColumnOpenapi },
        ],
        editable: [
            { text: 'Name', value: 'name' },
        ],
    },
};
