import axios from 'axios';
import apiResource from './apiResource';

export default function tagsResource(resource) {
    return {
        ...apiResource(resource),
        attachEntities(id, data, config) {
            return axios.post(`${this.resourceId(id)}/entities`, data, config);
        },
        detachEntities(id, data, config) {
            return axios.delete(`${this.resourceId(id)}/entities`, { data, ...config });
        },
    };
};
