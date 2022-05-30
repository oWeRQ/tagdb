import updateTitle from '../functions/updateTitle';

export default {
    inserted: (el, binding) => updateTitle(binding.value || el.textContent),
    update: (el, binding) => updateTitle(binding.value || el.textContent),
};
