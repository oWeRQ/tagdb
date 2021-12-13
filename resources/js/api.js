import axios from 'axios';

function resolveData(response) {
    const data = response.data.data;
    data.meta = response.data.meta;
    return data;
}

function apiResource(resource) {
    return {
        resource,
        resourceId(id) {
            return `${this.resource}/${id}`;
        },
        index(params) {
            return axios.get(this.resource, { params }).then(resolveData);
        },
        show(id) {
            return axios.get(this.resourceId(id)).then(resolveData);
        },
        store(data) {
            return axios.post(this.resource, data).then(resolveData);
        },
        update(id, data) {
            return axios.put(this.resourceId(id), data).then(resolveData);
        },
        save(id, data) {
            return (id ? this.update(id, data) : this.store(data));
        },
        destroy(id) {
            return axios.delete(this.resourceId(id));
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
        resource: `/api/v1/account`,
        index() {
            return axios.get(this.resource).then(resolveData);
        },
        projects() {
            return axios.get(`${this.resource}/projects`).then(resolveData);
        },
        currentProject() {
            return axios.get(`${this.resource}/current-project`).then(resolveData).then(project => {
                return (project.length === 0 ? null : project);
            });
        },
        switchProject(data) {
            return axios.post(`${this.resource}/switch-project`, data);
        },
    },
    entities: {
        ...apiResource(`/api/v1/entities`),
        destroyMany(data) {
            return axios.delete(this.resource, { data });
        },
    },
    fields: apiResource(`/api/v1/fields`),
    import: apiResource(`/api/v1/import`),
    presets: apiResource(`/api/v1/presets`),
    projects: apiResource(`/api/v1/projects`),
    tags: {
        ...apiResource(`/api/v1/tags`),
        attachEntities(id, data) {
            return axios.post(`${this.resourceId(id)}/entities`, data);
        },
        detachEntities(id, data) {
            return axios.delete(`${this.resourceId(id)}/entities`, { data });
        },
    },
    users: apiResource(`/api/v1/users`),
    values: apiResource(`/api/v1/values`),
};
