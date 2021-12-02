<template>
    <a :href="value" :title="value" target="_blank" class="text-decoration-none">{{ value | truncateUrl }}</a>
</template>

<script>
import truncateUrl from '../../functions/truncateUrl';
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

export default {
    filters: {
        truncateUrl,
    },
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
            return getObjectValueByPath(this.item, this.header.value);
        },
    },
};
</script>
