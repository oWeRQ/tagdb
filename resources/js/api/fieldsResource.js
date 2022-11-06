import axios from 'axios';
import apiResource from './apiResource';

export default function(resource) {
    return {
        ...apiResource(resource),
        updateValues(id, data, config) {
            return axios.put(`${this.resourceId(id)}/values`, data, config);
        },
    };
};
