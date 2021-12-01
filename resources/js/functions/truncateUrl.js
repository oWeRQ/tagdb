import truncateCenter from './truncateCenter';

export default function(str = '', len = 20, clamp = '...') {
    const url = new URL(str);

    url.host = truncateCenter(url.pathname, len, clamp);
    url.pathname = truncateCenter(url.pathname, len, clamp);
    url.search = truncateCenter(url.search, len, clamp);
    url.hash = truncateCenter(url.hash, len, clamp);

    return url.toString().replace(/^\w+:\/\/(www\.)?/, '').replace(/[/?#]$/, '');
};
