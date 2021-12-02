<template>
    <v-chip>
        <v-avatar left :color="item | value(header.value)"></v-avatar>
        {{ item | value(header.value) }}
    </v-chip>
</template>

<script>
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

export default {
    filters: {
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
