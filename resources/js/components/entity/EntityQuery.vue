<template>
    <div class="d-flex align-center">
        <TagsField
            v-model="tags"
            @input="onInput"
            @click:plus="showTag"
            return-object
            :hidden-tags="hiddenTags"
            solo
            hyphen
            class="shrink mr-2"
            prepend-inner-icon="mdi-tag-multiple-outline"
        ></TagsField>
        <EntityFilter v-model="filter" @input="onInput" :fields="fields"></EntityFilter>
        <ToolbarSearch v-model="search" @input="onInput"></ToolbarSearch>
    </div>
</template>

<script>
    import { mapState } from 'vuex';

    import EntityFilter from './EntityFilter';
    import ToolbarSearch from '../common/ToolbarSearch';
    import TagsField from './TagsField';
    import TagDialog from '../tag/TagDialog';

    export default {
        components: {
            TagsField,
            EntityFilter,
            ToolbarSearch,
        },
        props: {
            value: {
                type: Object,
                default: () => ({}),
            },
            hiddenTags: {
                type: Array,
                default: () => [],
            },
            fields: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                tags: [],
                filter: {},
                search: '',
            };
        },
        computed: {
            ...mapState({
                allTags: 'tags',
            }),
        },
        watch: {
            value(query) {
                this.tags = query.tags.map(this.getTag);
                this.filter = query.filter;
                this.search = query.search;
            },
            allTags() {
                this.tags = this.tags.map(tag => this.getTag(tag.name));
            },
        },
        methods: {
            showTag(tag) {
                this.originalTag = tag;
                this.$root.showDialog(TagDialog, {
                    value: tag,
                }, {
                    input: this.saveTag,
                });
            },
            saveTag(tag) {
                Object.assign(this.originalTag, tag);
            },
            getTag(item) {
                const name = item.replace(/^[+-]/, '');
                const tag = this.allTags.find(tag => tag.name === name);
                return { fields: [], ...tag, name: item };
            },
            onInput() {
                this.$emit('input', {
                    tags: this.tags.map(tag => tag.name),
                    filter: this.filter,
                    search: this.search,
                });
            },
        },
    };
</script>
