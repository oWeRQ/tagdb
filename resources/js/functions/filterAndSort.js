export default function(arr, fn, direction = 1) {
    const map = new Map;
    for (const item of arr) {
        const result = fn(item);
        if (result) {
            map.set(item, result);
        }
    }
    return [...map.keys()].sort((a, b) => (map.get(a) - map.get(b)) * direction);
}
