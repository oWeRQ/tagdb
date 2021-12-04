<template>
    <div>
        <v-text-field v-model="value.name" :rules="rules.name" label="Name" autofocus></v-text-field>
        <QueryField v-model="value.query" label="Query"></QueryField>
        <SortField v-model="value.sort" :fields="fields" :rules="rules.sort" label="Sort"></SortField>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import SortField from './SortField';
    import QueryField from './QueryField';

    export default {
        components: {
            SortField,
            QueryField,
        },
        props: {
            editable: {
                type: Array,
            },
            value: {
                type: Object,
            },
        },
        computed: {
            ...mapState([
                'tags',
            ]),
            rules: () => ({
                name: [
                    v => !!v || 'Required',
                ],
                sort: [
                    v => !!v || 'Required',
                ],
            }),
            queryTags() {
                try {
                    const tags = JSON.parse(this.value.query).tags;
                    return this.tags.filter(tag => tags.includes(tag.name));
                } catch (ex) {
                    return [];
                }
            },
            fields() {
                return this.queryTags.flatMap(item => item.fields);
            },
        },
    }
</script>
