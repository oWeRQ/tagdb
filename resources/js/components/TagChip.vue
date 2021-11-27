<template>
    <v-chip @click="$emit('click')" :color="tag.color" :dark="!!tag.color" small class="lighten-2">
        {{ tag.name }}
        <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
    </v-chip>
</template>

<script>
export default {
    props: {
        tag: Object,
    },
};
</script>

