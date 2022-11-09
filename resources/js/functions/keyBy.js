export default function keyBy(arr, mapper = v => v) {
    const obj = {};

    for (const item of arr) {
        obj[mapper(item)] = item;
    }

    return obj;
}
