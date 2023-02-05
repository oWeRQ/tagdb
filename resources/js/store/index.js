import { createStore } from 'vuex';

import auth from './auth';
import project from './project';

export default createStore({
    modules: {
        auth,
        project,
    },
});
