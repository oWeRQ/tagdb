<template>
    <div class="d-flex">
        <TagsField
            :value="queryTags"
            @input="updateQueryTags"
            return-object
            :hidden-tags="hiddenTags"
            solo
            hyphen
            class="shrink mr-2"
            prepend-inner-icon="mdi-tag-multiple-outline"
        ></TagsField>
        <EntityFilter v-model="query.filter" :fields="fields"></EntityFilter>
        <EntitySearch v-model="query.search"></EntitySearch>
    </div>
</template>

<script>
    import { mapState } from 'vuex';

    import EntityFilter from './EntityFilter';
    import EntitySearch from './EntitySearch';
    import TagsField from './TagsField';

    export default {
        components: {
            TagsField,
            EntityFilter,
            EntitySearch,
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
                query: this.defaultQuery(),
                queryTags: [],
            };
        },
        computed: {
            ...mapState([
                'tags',
            ]),
        },
        watch: {
            value() {
                console.log('prop value', this.value);
                this.query = this.value;
            },
            tags() {
                this.fetchQueryTags();
            },
            'query.tags'() {
                this.fetchQueryTags();
            },
        },
        methods: {
            defaultQuery() {
                return {
                    tags: [],
                    filter: {},
                    search: '',
                };
            },
            fetchQueryTags() {
                this.queryTags = this.tags.filter(tag => this.query.tags.includes(tag.name));
            },
            updateQueryTags(value) {
                this.queryTags = value;
                this.query.tags = this.queryTags.map(tag => tag.name);
            },
        },
    };
</script>
