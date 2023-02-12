import api from '../api';
import CrudList from '../components/crud/CrudList.vue';
import ProjectDialog from '../components/project/ProjectDialog.vue';

export default {
    component: CrudList,
    props: {
        dialog: ProjectDialog,
        title: 'Projects',
        api: api.projects,
        defaultItem: {},
        columns: [
            { title: 'ID', key: 'id' },
            { title: 'Name', key: 'name' },
            { title: 'Owner', key: 'owner.email' },
        ],
    },
};
