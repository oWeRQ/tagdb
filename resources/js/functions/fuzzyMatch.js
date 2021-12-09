import memoize from './memoize';

const patternRegExp = memoize(pattern => {
    return new RegExp(pattern.split('').join('.*'), 'i');
});

export default function(str, pattern) {
    return patternRegExp(pattern).test(str);
}
