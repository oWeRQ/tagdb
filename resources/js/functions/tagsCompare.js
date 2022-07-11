function normalizeName(name) {
    return name.replace(/^-/, '');
}

export default function (a, b) {
    if ((a.color || b.color) && a.color != b.color) {
        if (!a.color)
            return 1;

        if (!b.color)
            return -1;

        return a.color.localeCompare(b.color);
    }

    return normalizeName(a.name).localeCompare(normalizeName(b.name));
}
