<template>
    <span @click="$emit('edit')" style="cursor: pointer;">
        {{ item.name | truncate(120) }}
    </span>
</template>

<script>
import truncate from '../../functions/truncate';

export default {
    filters: {
        truncate,
    },
    props: {
        item: {
            type: Object,
        },
    },
};
</script>
