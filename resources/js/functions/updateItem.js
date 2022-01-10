export default function(items, result) {
    const item = items.find(item => item.id === result.id);
    if (item) {
        Object.assign(item, result);
    } else {
        items.unshift(result);
    }
};
