export default function(str = '', len = 30, clamp = '...') {
    if (str.length > len + clamp.length)
        return str.substr(0, len) + clamp;

    return str;
};
