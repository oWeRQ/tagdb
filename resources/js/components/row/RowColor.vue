<template>
    <v-chip v-if="value" @click="$emit('edit')">
        <v-avatar left :color="value"></v-avatar>
        {{ value }}
    </v-chip>
</template>

<script>
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
            return getObjectValueByPath(this.item, this.header.value);
        },
    },
};
</script>
