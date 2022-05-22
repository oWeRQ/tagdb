import memoize from './memoize';

const patternRegExp = memoize(pattern => {
    return new RegExp(pattern.split('').join('.*'), 'i');
});

export default function(pattern, str) {
    return patternRegExp(pattern).test(str);
}
