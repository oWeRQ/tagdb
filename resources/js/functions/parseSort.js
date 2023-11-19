export default function(value) {
    if (typeof value !== 'string') {
        return [];
    }

    return value.split(',').map(part => ({
        key: part.replace(/^-/, ''),
        order: part[0] === '-' ? 'desc' : 'asc',
    }));
}
