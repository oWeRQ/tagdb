import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { VDataTable, VDataTableServer } from 'vuetify/labs/VDataTable'

import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/dist/vuetify.min.css';

export default createVuetify({
    components: {
        ...components,
        VDataTable,
        VDataTableServer,
    },
    directives,
});
