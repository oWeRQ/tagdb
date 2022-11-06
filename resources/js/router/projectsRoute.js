import api from '../api';
import Crud from '../components/crud/CrudList.vue';
import ProjectDialog from '../components/project/ProjectDialog.vue';

export default {
    component: Crud,
    props: {
        dialog: ProjectDialog,
        title: 'Projects',
        api: api.projects,
        defaultItem: {},
        columns: [
            { text: 'ID', value: 'id' },
            { text: 'Name', value: 'name' },
            { text: 'Owner', value: 'owner.email' },
        ],
    },
};
