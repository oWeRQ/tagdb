export default function(str) {
    return str.replace(/\b\w/g, chr => chr.toUpperCase());
};
