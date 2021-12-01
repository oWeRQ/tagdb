export default function(str = '', len = 30, clamp = '...') {
    if (str.length > len)
        return str.slice(0, Math.ceil(len / 2)) + clamp + str.slice(-Math.floor(len / 2));

    return str;
};
