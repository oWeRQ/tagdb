export default function(fn) {
    const cache = new Map;

    function memoized(key) {
        if (cache.has(key))
            return cache.get(key);

        const result = fn.apply(this, arguments);
        cache.set(key, result);
        return result;
    }

    memoized.cache = cache;
    return memoized;
}
