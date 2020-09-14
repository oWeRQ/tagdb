<template>
    <div>
        <v-text-field v-model="value.name" label="Name" autofocus></v-text-field>
        <SortField v-model="value.sort" label="Sort"></SortField>
        <QueryField v-model="value.query" label="Query"></QueryField>
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
