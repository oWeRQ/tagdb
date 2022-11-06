import axios from 'axios';
import apiResource from './apiResource';

export default function(resource) {
    return {
        ...apiResource(resource),
        destroyMany(data, config) {
            return axios.delete(this.resource, { data, ...config });
        },
    };
};
