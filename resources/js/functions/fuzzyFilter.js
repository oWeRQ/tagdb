import fuzzyMatch from './fuzzyMatch';

export default function(pattern, arr, prop) {
    return arr.map(obj => ({
        obj,
        match: fuzzyMatch(pattern, obj[prop]),
    })).filter(item => item.match > 0).sort((a, b) => b.match - a.match).map(item => item.obj);
}
