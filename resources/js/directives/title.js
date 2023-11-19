import updateTitle from '../functions/updateTitle';

export default {
    created: (el, binding) => updateTitle(binding.value || el.textContent),
    update: (el, binding) => updateTitle(binding.value || el.textContent),
};
