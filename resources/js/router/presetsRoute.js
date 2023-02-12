import api from '../api';
import CrudList from '../components/crud/CrudList.vue';
import PresetDialog from '../components/preset/PresetDialog.vue';

export default {
    component: CrudList,
    props: {
        dialog: PresetDialog,
        title: 'Presets',
        api: api.presets,
        defaultItem: {},
        columns: [
            { title: 'ID', key: 'id' },
            { title: 'Name', key: 'name' },
            { title: 'Sort', key: 'sort' },
            { title: 'Query', key: 'query' },
        ],
    }
};
