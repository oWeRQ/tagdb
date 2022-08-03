import axios from 'axios';
import resolveData from './resolveData';

export default function(resource) {
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
};
