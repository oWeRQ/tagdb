<template>
    <div>
        <v-text-field v-model="modelValue.name" :rules="rules.name" label="Name" autofocus></v-text-field>
        <QueryField v-model="modelValue.query" label="Query"></QueryField>
        <SortField v-model="modelValue.sort" :fields="fields" :rules="rules.sort" label="Sort"></SortField>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import SortField from './SortField.vue';
    import QueryField from './QueryField.vue';

    export default {
        components: {
            SortField,
            QueryField,
        },
        props: {
            modelValue: {
                type: Object,
            },
        },
        computed: {
            ...mapState({
                tags: state => state.project.tags,
            }),
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
                    const tags = JSON.parse(this.modelValue.query).tags;
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
