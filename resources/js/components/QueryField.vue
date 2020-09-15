<template>
    <div>
        <v-autocomplete v-if="tags"
            v-model="query.tags"
            :rules="rules.tags"
            :items="tags"
            color="blue darken-1"
            label="Tags"
            item-text="name"
            item-value="name"
            chips
            multiple
            hide-selected
            hide-no-data
            deletable-chips
            :autofocus="!query.tags.length"
        ></v-autocomplete>
        <v-text-field v-model="query.search" label="Search"></v-text-field>
    </div>
</template>

<script>
    import Vue from 'vue';

    export default {
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
                return Vue.observable(JSON.parse(this.value));
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
