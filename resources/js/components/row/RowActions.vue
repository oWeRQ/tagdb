<template>
    <i
        @click="$emit('edit')"
        :title="'ID: ' + item.id"
        class="v-icon v-icon--size-default notranslate mdi mdi-pencil v-theme--light text-grey"
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
