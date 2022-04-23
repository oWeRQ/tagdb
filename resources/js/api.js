import axios from 'axios';

function resolveData(response) {
    if (response.data) {
        const data = response.data.data;
        data.meta = response.data.meta;
        return data;
    }
}

function apiResource(resource) {
    return {
        resource,
        resourceId(id) {
            return `${this.resource}/${id}`;
        },
        index(params, config) {
            return axios.get(this.resource, { params, ...config }).then(resolveData);
        },
        show(id, config) {
            return axios.get(this.resourceId(id), config).then(resolveData);
        },
        store(data, config) {
            return axios.post(this.resource, data, config).then(resolveData);
        },
        update(id, data, config) {
            return axios.put(this.resourceId(id), data, config).then(resolveData);
        },
        save(id, data, config) {
            return (id ? this.update(id, data, config) : this.store(data, config));
        },
        destroy(id, config) {
            return axios.delete(this.resourceId(id), config);
        },
    };
}

function entitiesResource(resource) {
    return {
        ...apiResource(resource),
        destroyMany(data, config) {
            return axios.delete(this.resource, { data, ...config });
        },
    };
}

function fieldsResource(resource) {
    return {
        ...apiResource(resource),
        updateValues(id, data, config) {
            return axios.put(`${this.resourceId(id)}/values`, data, config);
        },
    };
}

function tagsResource(resource) {
    return {
        ...apiResource(resource),
        attachEntities(id, data, config) {
            return axios.post(`${this.resourceId(id)}/entities`, data, config);
        },
        detachEntities(id, data, config) {
            return axios.delete(`${this.resourceId(id)}/entities`, { data, ...config });
        },
    };
}

function authResource(resource) {
    return {
        resource,
        register(data) {
            return axios.post(`${this.resource}/register`, data, {
                headers: { Accept: 'application/json' },
            });
        },
        login(data) {
            return axios.post(`${this.resource}/login`, data, {
                headers: { Accept: 'application/json' },
            });
        },
        logout() {
            return axios.post(`${this.resource}/logout`);
        },
    };
}

function accountResource(resource) {
    return {
        resource,
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
    };
}

export default {
    auth: authResource(''),
    account: accountResource(`/api/v1/account`),
    entities: entitiesResource(`/api/v1/entities`),
    fields: fieldsResource(`/api/v1/fields`),
    import: apiResource(`/api/v1/import`),
    presets: apiResource(`/api/v1/presets`),
    projects: apiResource(`/api/v1/projects`),
    tags: tagsResource(`/api/v1/tags`),
    tagsImport: apiResource(`/api/v1/tags-import`),
    users: apiResource(`/api/v1/users`),
    values: apiResource(`/api/v1/values`),
};
