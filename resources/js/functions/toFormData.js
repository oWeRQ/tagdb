import flattenParams from './flattenParams';

export default function toFormData(params) {
    const data = new FormData;
    const flatten = flattenParams(params);
    for (const param in flatten) {
        data.append(param, flatten[param]);
    }
    return data;
}
