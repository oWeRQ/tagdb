<template>
    <span v-text="value"></span>
</template>

<script>
import date from '../../functions/date';
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
            return date(getObjectValueByPath(this.item, this.header.value));
        },
    },
};
</script>
