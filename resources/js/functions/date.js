export default function(str = '') {
    try {
        return str && new Date(str).toISOString().slice(0, 10);
    } catch(e) {
        return '';
    }
};
