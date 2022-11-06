import axios from 'axios';
import resolveData from './resolveData';

export default function(resource) {
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
};
