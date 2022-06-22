<template>
    <span v-text="value" @click="$emit('edit')" class="column"></span>
</template>

<script>
import truncate from '../../functions/truncate';
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

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
            return truncate(getObjectValueByPath(this.item, this.header.value));
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
