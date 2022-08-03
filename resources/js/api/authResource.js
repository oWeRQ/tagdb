import axios from 'axios';

export default function(resource) {
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
};
