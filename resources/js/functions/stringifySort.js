export default function (sortBy, sortDesc) {
    return sortBy && sortBy.map((v, i) => (sortDesc[i] ? '-' : '') + v).join(',');
}
