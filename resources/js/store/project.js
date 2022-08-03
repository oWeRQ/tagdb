import api from '../api';

export default {
    namespaced: false,
    state: {
        tags: [],
        fields: [],
        presets: [],
    },
    mutations: {
        presets(state, value) {
            state.presets = value;
        },
        tagsAndFields(state, { tags, fields }) {
            state.tags = tags;
            state.fields = fields;
        },
    },
    actions: {
        fetchProjectData({ dispatch }) {
            return Promise.all([
                dispatch('fetchPresets'),
                dispatch('fetchTagsAndFields'),
            ]);
        },
        fetchPresets({ commit }) {
            return api.presets.index({ sort: 'name' }).then(presets => {
                commit('presets', presets);
            });
        },
        fetchTagsAndFields({ commit }) {
            return Promise.all([
                api.tags.index(),
                api.fields.index(),
            ]).then(([tags, fields]) => {
                commit('tagsAndFields', { tags, fields });
            });
        },
        saveProject({ dispatch }, project) {
            dispatch('fetchProjects');
        },
        deleteProject({ dispatch }, project) {
            dispatch('fetchProjects');
        },
        savePreset({ dispatch }, preset) {
            dispatch('fetchPresets');
        },
        deletePreset({ dispatch }, preset) {
            dispatch('fetchPresets');
        },
        createTag({ dispatch }, tag) {
            return api.tags.store(tag).then((response) => {
                dispatch('saveTag', response);
                return response;
            });
        },
        saveTag({ dispatch }, tag) {
            dispatch('fetchTagsAndFields');
        },
        deleteTag({ dispatch }, tag) {
            dispatch('fetchTagsAndFields');
        },
        saveField({ dispatch }, field) {
            dispatch('fetchTagsAndFields');
        },
        deleteField({ dispatch }, field) {
            dispatch('fetchTagsAndFields');
        },
        saveEntity({ dispatch }, entity) {
            dispatch('fetchTagsAndFields');
        },
        deleteEntity({ dispatch }, entity) {
            dispatch('fetchTagsAndFields');
        },
    },
};
