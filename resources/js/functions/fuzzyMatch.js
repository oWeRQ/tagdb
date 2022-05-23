import memoize from './memoize';

const escapeChars = {
    '\\': '\\\\',
    '?': '\\?',
    '*': '\\*',
    '+': '\\+',
    '.': '\\.',
    '[': '\\[',
    ']': '\\]',
    '(': '\\(',
    ')': '\\)',
    '<': '\\<',
    '>': '\\>',
    '{': '\\{',
    '}': '\\}',
};

const patternRegExp = memoize(pattern => {
    return new RegExp('.?' + pattern.split('').map(chr => escapeChars[chr] || chr).join('.*?') + '.?', 'i');
});

export default function(pattern, str) {
    if (!pattern)
        return 1;

    const matches = str.match(patternRegExp(pattern));
    return (matches ? pattern.length / matches[0].length : 0);
}
