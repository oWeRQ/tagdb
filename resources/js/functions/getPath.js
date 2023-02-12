export default function getPath(obj, path) {
    if (typeof path === 'string') {
        path = path.split('.');
    }

    for (const key of path) {
        if (obj == null)
            return obj;

        obj = obj[key];
    }

    return obj;
}