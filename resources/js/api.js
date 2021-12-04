import axios from 'axios';

function apiResource(resource) {
    return {
        index(params) {
            return axios.get(resource, { params });
        },
        show(id) {
            return axios.get(`${resource}/${id}`);
        },
        store(data) {
            return axios.post(resource, data);
        },
        update(id, data) {
            return axios.put(`${resource}/${id}`, data);
        },
        destroy(id) {
            return axios.delete(`${resource}/${id}`);
        },
    };
};

export default {
    auth: {
        register(data) {
            return axios.post('/register', data, {
                headers: { Accept: 'application/json' },
            });
        },
        login(data) {
            return axios.post('/login', data, {
                headers: { Accept: 'application/json' },
            });
        },
        logout() {
            return axios.post('/logout');
        },
    },
    account: {
        index() {
            return axios.get(`/api/v1/account`);
        },
        projects() {
            return axios.get(`/api/v1/account/projects`);
        },
        currentProject() {
            return axios.get(`/api/v1/account/current-project`);
        },
        switchProject(data) {
            return axios.post(`/api/v1/account/switch-project`, data);
        },
    },
    entities: apiResource(`/api/v1/entities`),
    fields: apiResource(`/api/v1/fields`),
    import: apiResource(`/api/v1/import`),
    presets: apiResource(`/api/v1/presets`),
    projects: apiResource(`/api/v1/projects`),
    tags: {
        ...apiResource(`/api/v1/tags`),
        attachEntities(id, data) {
            return axios.post(`/api/v1/tags/${id}/entities`, data);
        },
        detachEntities(id, data) {
            return axios.delete(`/api/v1/tags/${id}/entities`, { data });
        },
    },
    users: apiResource(`/api/v1/users`),
    values: apiResource(`/api/v1/values`),
};
