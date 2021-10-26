<template>
    <div>
        <TagsField
            v-model="query.tags"
            :rules="rules.tags"
            :autofocus="!query.tags.length"
            hyphen
        ></TagsField>
        <v-text-field v-model="query.search" label="Search"></v-text-field>
    </div>
</template>

<script>
    import Vue from 'vue';
    import TagsField from './TagsField';

    export default {
        components: {
            TagsField,
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
            rules() {
                return {
                    tags: [
                        v => !!v.length || 'Required',
                    ],
                };
            },
            tags() {
                return this.$root.tags;
            },
            query() {
                return Vue.observable(this.value ? JSON.parse(this.value) : {tags: [], search: ''});
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
