<template>
    <div>
        <v-text-field v-model="value.name" :rules="rules.name" label="Name" autofocus></v-text-field>
        <QueryField v-model="value.query" label="Query"></QueryField>
        <SortField v-model="value.sort" :fields="fields" :rules="rules.sort" label="Sort"></SortField>
    </div>
</template>

<script>
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
            rules: () => ({
                name: [
                    v => !!v || 'Required',
                ],
                sort: [
                    v => !!v || 'Required',
                ],
            }),
            tags() {
                try {
                    const tags = JSON.parse(this.value.query).tags;
                    return this.$root.tags.filter(tag => tags.includes(tag.name));
                } catch (ex) {
                    return [];
                }
            },
            fields() {
                return this.tags.flatMap(item => item.fields);
            },
        },
    }
</script>
