import api from '../api';
import CrudList from '../components/crud/CrudList.vue';

export default {
    component: CrudList,
    props: {
        title: 'Users',
        api: api.users,
        defaultItem: {},
        columns: [
            { title: 'ID', key: 'id' },
            { title: 'Name', key: 'name' },
            { title: 'Email', key: 'email' },
        ],
        editable: [
            { text: 'Name', value: 'name' },
            { text: 'Email', value: 'email' },
        ],
    },
};
