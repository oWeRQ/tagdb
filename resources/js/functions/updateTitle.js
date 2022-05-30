const TITLE_BASE = 'TagDB';
const TITLE_SEPARATOR = ' | ';

export default function(value) {
    document.title = (value ? value + TITLE_SEPARATOR : '') + TITLE_BASE;
}
