<template>
    <span @click="$emit('edit')" style="cursor: pointer; display: inline-block; min-width: 200px;" v-text="value"></span>
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
            return truncate(this.item.name, 90);
        },
    },
};
</script>
