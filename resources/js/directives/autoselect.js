export default {
    inserted(el, { value }) {
        if (value) {
            setTimeout(() => {
                el.querySelector('input[type=text], textarea')?.select();
            }, 100);
        }
    },
};
