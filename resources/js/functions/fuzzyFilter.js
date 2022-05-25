import filterAndSort from './filterAndSort';
import fuzzyMatch from './fuzzyMatch';

export default function(pattern, arr, prop) {
    return filterAndSort(arr, item => fuzzyMatch(pattern, item[prop]), -1);
}
