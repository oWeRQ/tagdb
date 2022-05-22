import fuzzyMatch from './fuzzyMatch';

export default function(pattern, arr, prop) {
    if (!pattern)
        return arr;

    const result = arr.filter(item => fuzzyMatch(pattern, item[prop]));
    return [...result].sort((a, b) => a[prop].length - b[prop].length);
}
