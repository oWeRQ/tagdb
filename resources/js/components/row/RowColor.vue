<template>
    <v-chip v-if="value" @click="$emit('edit')">
        <v-avatar left :color="value"></v-avatar>
        {{ value }}
    </v-chip>
</template>

<script>
import getPath from '../../functions/getPath';

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
};
</script>
