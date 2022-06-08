<template>
    <span v-text="value" @click="$emit('edit')"></span>
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
