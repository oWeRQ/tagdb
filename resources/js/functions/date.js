export default function(str = '') {
    return str && new Date(str).toISOString().substr(0, 10);
};
