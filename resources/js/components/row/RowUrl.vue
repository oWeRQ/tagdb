<template>
    <a :href="value" :title="value" target="_blank" class="text-decoration-none">{{ truncateUrl(value) }}</a>
</template>

<script>
import getPath from '../../functions/getPath';
import truncateUrl from '../../functions/truncateUrl';

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
            return getPath(this.item, this.header.key);
        },
    },
    methods: {
        truncateUrl,
    },
};
</script>
