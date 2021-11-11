export default function(value) {
    return value.toLowerCase().replace(/\W+/g, '_');
}
