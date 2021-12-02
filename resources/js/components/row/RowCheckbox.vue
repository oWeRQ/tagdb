<template>
    <v-simple-checkbox :value="isSelected" @input="select" class="v-data-table__checkbox"></v-simple-checkbox>
</template>

<script>
export default {
    props: {
        isSelected: {
            type: Boolean,
        },
        select: {
            type: Function,
        },
    },
};
</script>
