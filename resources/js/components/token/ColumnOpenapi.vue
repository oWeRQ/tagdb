<template>
    <a :href="value" :download="item.name">Download {{ item.name }}</a>
</template>

<script>
export default {
    props: {
        item: {
            type: Object,
        },
        header: {
            type: Object,
        },
        value: {
            type: String,
            default: '',
        },
    },
};
</script>