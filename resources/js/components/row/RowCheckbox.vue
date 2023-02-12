<template>
    <v-checkbox :value="isSelected" @input="select" class="v-data-table__checkbox"></v-checkbox>
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
