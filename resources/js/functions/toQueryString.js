import flattenParams from './flattenParams';

export default function toQueryString(params) {
    return new URLSearchParams(flattenParams(params)).toString();
}

