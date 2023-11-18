<template>
    <v-chip :color="tag.color" :dark="!!tag.color" :small="small" class="lighten-2">
        <v-icon left v-if="!tag.id">
            mdi-plus
        </v-icon>
        {{ tag.name }}
        <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
    </v-chip>
</template>

<script>
export default {
    props: {
        tag: {
            type: Object,
            required: true,
        },
        small: {
            type: Boolean,
            default: false,
        },
    },
};
</script>

