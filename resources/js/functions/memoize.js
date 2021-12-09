export default function(fn) {
    function memoized(key) {
        if (memoized.cache.has(key))
            return memoized.cache.get(key);

        const result = fn.apply(this, arguments);
        memoized.cache.set(key, result);
        return result;
    }

    memoized.cache = new Map;
    return memoized;
}
