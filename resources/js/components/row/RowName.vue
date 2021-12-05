<template>
    <span @click="$emit('edit')" style="cursor: pointer;" v-text="value"></span>
</template>

<script>
import truncate from '../../functions/truncate';

export default {
    props: {
        header: {
            type: Object,
        },
        item: {
            type: Object,
        },
    },
    computed: {
        value() {
            return truncate(this.item.name, 120);
        },
    },
};
</script>
