import api from '../api';
import Crud from '../components/crud/CrudList.vue';
import PresetDialog from '../components/preset/PresetDialog.vue';

export default {
    component: Crud,
    props: {
        dialog: PresetDialog,
        title: 'Presets',
        api: api.presets,
        defaultItem: {},
        columns: [
            { text: 'ID', value: 'id' },
            { text: 'Name', value: 'name' },
            { text: 'Sort', value: 'sort' },
            { text: 'Query', value: 'query' },
        ],
    }
};
