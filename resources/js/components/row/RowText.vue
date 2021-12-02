<template>
    <span>
        {{ item | value(header.value) | truncate }}
    </span>
</template>

<script>
import truncate from '../../functions/truncate';
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

export default {
    filters: {
        truncate,
        value: getObjectValueByPath,
    },
    props: {
        header: {
            type: Object,
        },
        item: {
            type: Object,
        },
    },
};
</script>
