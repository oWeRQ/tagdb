<template>
    <div class="d-flex align-center">
        <TagsField
            v-model="tags"
            @update:modelValue="onInput"
            @click:plus="showTag"
            return-object
            :hidden-tags="hiddenTags"
            solo
            hyphen
            class="shrink mr-2"
            prepend-inner-icon="mdi-tag-multiple-outline"
        ></TagsField>
        <EntityFilter v-model="filter" @update:modelValue="onInput" :fields="fields"></EntityFilter>
        <ToolbarSearch v-model="search" @update:modelValue="onInput"></ToolbarSearch>
    </div>
</template>

<script>
    import { mapState } from 'vuex';

    import EntityFilter from './EntityFilter.vue';
    import ToolbarSearch from '../common/ToolbarSearch.vue';
    import TagsField from './TagsField.vue';
    import TagDialog from '../tag/TagDialog.vue';

    export default {
        components: {
            TagsField,
            EntityFilter,
            ToolbarSearch,
        },
        props: {
            modelValue: {
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
                allTags: state => state.project.tags,
            }),
        },
        watch: {
            modelValue(query) {
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
                    save: this.saveTag,
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
                this.$emit('update:modelValue', {
                    tags: this.tags.map(tag => tag.name),
                    filter: this.filter,
                    search: this.search,
                });
            },
        },
    };
</script>
