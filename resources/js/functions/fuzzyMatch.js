import memoize from './memoize';

const escapeCharSet = new Set('\\?*+.[]()<>{}');
const escapeChar = chr => (escapeCharSet.has(chr) ? '\\' + chr : chr);

const patternRegExp = memoize(pattern => {
    return new RegExp('.?' + pattern.split('').map(escapeChar).join('.*?') + '.?', 'i');
});

export default function(pattern, str) {
    if (!pattern)
        return 1;

    const matches = str.match(patternRegExp(pattern));
    return (matches ? pattern.length / matches[0].length : 0);
}
