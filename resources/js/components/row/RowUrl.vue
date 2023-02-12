<template>
    <a :href="value" :title="value" target="_blank" class="text-decoration-none">{{ value | truncateUrl }}</a>
</template>

<script>
import getPath from '../../functions/getPath';
import truncateUrl from '../../functions/truncateUrl';

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
            return getPath(this.item, this.header.value);
        },
    },
};
</script>
