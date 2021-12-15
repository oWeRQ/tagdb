<template>
    <div>
        <TagsField
            v-model="query.tags"
            :rules="rules.tags"
            hyphen
        ></TagsField>
        <EntityFilter v-model="query.filter" :fields="filterFields"></EntityFilter>
        <v-divider class="mb-4"></v-divider>
        <v-text-field v-model="query.search" label="Search"></v-text-field>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import Vue from 'vue';
    import TagsField from '../entity/TagsField';
    import EntityFilter from '../entity/EntityFilter';

    export default {
        components: {
            TagsField,
            EntityFilter,
        },
        props: {
            value: {
                type: String,
            },
            label: {
                type: String,
            },
        },
        computed: {
            ...mapState([
                'tags',
            ]),
            rules() {
                return {
                    tags: [
                        v => !!v.length || 'Required',
                    ],
                };
            },
            query() {
                return Vue.observable(this.value ? JSON.parse(this.value) : {tags: [], filter: {}, search: ''});
            },
            queryTags() {
                return this.tags.filter(tag => this.query.tags.includes(tag.name));
            },
            displayFields() {
                return this.queryTags.flatMap(item => item.fields);
            },
            filterFields() {
                return [
                    { text: 'Name', value: 'name', type: 'text' },
                    ...this.displayFields.map(field => ({ text: field.name, value: 'contents.' + field.id, type: field.type })),
                ];
            },
        },
        watch: {
            query: {
                deep: true,
                handler(value) {
                    this.$emit('input', JSON.stringify(value));
                },
            },
        },
    }
</script>
