<template>
    <div>
        <v-text-field v-model="value.name" label="Name" autofocus></v-text-field>
        <QueryField v-model="value.query" label="Query"></QueryField>
        <SortField v-model="value.sort" label="Sort"></SortField>
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
                default: () => {},
            },
        },
    }
</script>
