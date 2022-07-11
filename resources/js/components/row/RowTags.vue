<template>
    <div class="d-flex my-n1">
        <span
            v-if="invisibleTags.length"
            @click="$emit('edit')"
            tabindex="0"
            class="grey--text v-chip v-chip--clickable v-chip--no-color v-chip--outlined theme--light v-size--small mr-2 my-1"
        >
            <span class="v-chip__content">{{ invisibleTags.length }}</span>
        </span>
        <span
            v-for="tag in visibleTags"
            :key="tag.name"
            @click="$emit('click:tag', tag)"
            tabindex="0"
            class="lighten-2 v-chip v-chip--clickable v-size--small mr-2 my-1"
            :class="tag.color ? `theme--dark ${tag.color}` : `theme--light`"
        >
            <span class="v-chip__content">
                {{ tag.name }}
                <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
            </span>
        </span>
    </div>
</template>

<script>
import tagsCompare from '../../functions/tagsCompare';

export default {
    props: {
        tags: {
            type: Array,
            default: () => [],
        },
        item: {
            type: Object,
        },
    },
    computed: {
        visibleTags() {
            return this.item.tags.filter(tag => !this.tags.includes(tag.name)).sort(tagsCompare);
        },
        invisibleTags() {
            return this.item.tags.filter(tag => this.tags.includes(tag.name));
        },
    },
};
</script>
