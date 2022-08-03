import axios from 'axios';
import apiResource from './apiResource';
import entitiesResource from './entitiesResource';
import fieldsResource from './fieldsResource';
import tagsResource from './tagsResource';
import accountResource from './accountResource';

export function setErrorHandler(handler) {
    axios.interceptors.response.use(response => response, handler);
}

export function setProjectId(value) {
    axios.defaults.headers.common['X-Project-Id'] = value;
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
