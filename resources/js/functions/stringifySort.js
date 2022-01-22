export default function (sortBy, sortDesc) {
    if (sortBy && sortBy.length) {
        return sortBy.map((v, i) => (sortDesc[i] ? '-' : '') + v).join(',');
    }

    return undefined;
}
