export default function(a, b) {
    for (const key in a) {
        if (JSON.stringify(a[key]) !== JSON.stringify(b[key]))
            return true;
    }

    return false;
}
