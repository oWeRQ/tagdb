export default function flattenParams(params) {
    const flatten = {};
    for (const param in params) {
        const value = params[param];
        if (typeof value === 'object' && !(params[param] instanceof Blob)) {
            const flattenValue = flattenParams(value);
            for (const key in flattenValue) {
                const flattenKey = param + key.replace(/^([^[]+)/, '[$1]');
                flatten[flattenKey] = flattenValue[key];
            }
        } else if (value != null) {
            flatten[param] = value;
        }
    }
    return flatten;
}
