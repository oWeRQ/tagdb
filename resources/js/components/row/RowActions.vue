<template>
    <v-icon @click="$emit('edit')" color="grey" :title="'ID: ' + item.id">
        mdi-pencil
    </v-icon>
</template>

<script>
export default {
    props: {
        item: {
            type: Object,
        },
    },
};
</script>
