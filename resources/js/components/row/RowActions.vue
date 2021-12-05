<template>
    <i
        @click="$emit('edit')"
        :title="'ID: ' + item.id"
        class="v-icon notranslate mdi mdi-pencil theme--light grey--text"
        aria-hidden="true"
    ></i>
</template>

<script>
export default {
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
