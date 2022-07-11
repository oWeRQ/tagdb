export default function (a, b) {
    if (a.color !== b.color) {
        if (!a.color)
            return 1;

        if (!b.color)
            return -1;

        return a.color.localeCompare(b.color);
    }

    return a.name.localeCompare(b.name);
}
