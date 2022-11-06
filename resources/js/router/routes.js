import EntityList from '../components/entity/EntityList.vue';
import TagList from '../components/tag/TagList.vue';
import FieldList from '../components/field/FieldList.vue';

import valuesRoute from './valuesRoute';
import presetsRoute from './presetsRoute';
import projectsRoute from './projectsRoute';
import tokensRoute from './tokensRoute';
import usersRoute from './usersRoute';

export default [
    {
        path: '',
        name: 'index',
        component: EntityList,
    },
    {
        path: '/presets/:name',
        name: 'preset',
        component: EntityList,
    },
    {
        path: '/tags',
        component: TagList,
    },
    {
        path: '/fields',
        component: FieldList,
    },
    {
        path: '/values',
        ...valuesRoute,
    },
    {
        path: '/presets',
        ...presetsRoute,
    },
    {
        path: '/projects',
        ...projectsRoute,
    },
    {
        path: '/tokens',
        ...tokensRoute,
    },
    {
        path: '/users',
        ...usersRoute,
    },
];
