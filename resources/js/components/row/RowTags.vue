<template>
    <div class="d-flex my-n1">
        <span
            v-if="invisibleTags.length"
            @click="$emit('edit')"
            tabindex="0"
            class="v-chip v-chip--link v-theme--light text-grey v-chip--density-default v-chip--size-small v-chip--variant-outlined mr-2 my-1"
        >
            {{ invisibleTags.length }}
        </span>
        <span
            v-for="tag in visibleTags"
            :key="tag.name"
            @click="$emit('click:tag', tag)"
            tabindex="0"
            class="v-chip v-chip--link v-chip--density-default v-chip--size-small mr-2 my-1"
            :class="tag.color ? `bg-${tag.color}-lighten-1` : `bg-grey-lighten-2`"
        >
            {{ tag.name }}
            <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
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
