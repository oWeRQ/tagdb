<template>
    <v-chip-group>
        <v-chip v-if="invisibleTags.length" small outlined class="grey--text">
            {{ invisibleTags.length }}
        </v-chip>
        <v-chip v-for="tag in visibleTags" :key="tag.name" @click="$emit('click:tag', tag)" :color="tag.color" :dark="!!tag.color" small class="lighten-2">
            {{ tag.name }}
            <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
        </v-chip>
    </v-chip-group>
</template>

<script>
export default {
    props: {
        query: {
            type: Object,
            default: () => ({tags: []}),
        },
        item: {
            type: Object,
        },
    },
    computed: {
        visibleTags() {
            return this.item.tags.filter(tag => !this.query.tags.includes(tag.name));
        },
        invisibleTags() {
            return this.item.tags.filter(tag => this.query.tags.includes(tag.name));
        },
    },
};
</script>
