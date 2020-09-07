export default function(str = '') {
    return new Date(str).toISOString().substr(0, 10);
};
