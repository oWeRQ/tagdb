export default function (sortBy) {
    if (sortBy && sortBy.length) {
        return sortBy.map(({ key, order }) => (order === 'desc' ? '-' : '') + key).join(',');
    }

    return undefined;
}
