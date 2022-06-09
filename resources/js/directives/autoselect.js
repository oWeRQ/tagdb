function hook(el, { value }) {
    if (value) {
        setTimeout(() => {
            el.querySelector('input[type=text], textarea')?.select();
        }, 100);
    }
}

export default {
    inserted: hook,
    componentUpdated: hook,
};
