<template>
    <span>
        {{ item | value(header.value) | date }}
    </span>
</template>

<script>
import date from '../../functions/date';
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

export default {
    filters: {
        date,
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
