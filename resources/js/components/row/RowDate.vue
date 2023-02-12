<template>
    <span v-text="value" @click="$emit('edit')" class="column"></span>
</template>

<script>
import getPath from '../../functions/getPath';
import date from '../../functions/date';

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
            return date(getPath(this.item, this.header.key));
        },
    },
};
</script>

<style lang="scss" scoped>
    .column {
        cursor: pointer;
        display: inline-block;

        &:hover {
            color: rgba(0, 0, 0, .7);
        }
    }
</style>
