<template>
    <span @click="$emit('edit')" class="column" v-text="value"></span>
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

<style lang="scss" scoped>
    .column {
        cursor: pointer;
        display: inline-block;
        min-width: 200px;

        &:hover {
            color: rgba(0, 0, 0, .7);
        }
    }
</style>
