export default function(value) {
    const sortBy = [];
    const sortDesc = [];

    if (typeof value === 'string') {
        for (let part of value.split(',')) {
            sortBy.push(part.replace(/^-/, ''));
            sortDesc.push(part[0] === '-');
        }
    }

    return { sortBy, sortDesc };
}
