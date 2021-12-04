<template>
    <div>
        <TagsField
            v-model="query.tags"
            :rules="rules.tags"
            hyphen
        ></TagsField>
        <v-text-field v-model="query.search" label="Search"></v-text-field>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import Vue from 'vue';
    import TagsField from '../entity/TagsField';

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
