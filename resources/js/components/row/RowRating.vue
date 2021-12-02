<template>
    <v-rating
        :value="item | value(header.value) | number"
        readonly
        half-increments
        color="orange"
        background-color="grey lighten-1"
        size="18"
        class="d-inline-block"
    ></v-rating>
</template>

<script>
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

export default {
    filters: {
        number: Number,
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
