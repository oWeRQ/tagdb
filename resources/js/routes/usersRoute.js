import api from '../api';
import Crud from '../components/crud/CrudList.vue';

export default {
    component: Crud,
    props: {
        title: 'Users',
        api: api.users,
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
};
