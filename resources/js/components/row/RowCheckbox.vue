<template>
    <v-checkbox-btn :modelValue="isSelected" @update:modelValue="select" class="v-data-table__checkbox"></v-checkbox-btn>
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
