<template>
    <div>
        <v-text-field v-model="value.name" label="Name" autofocus></v-text-field>
        <v-text-field v-model="value.sort" label="Sort"></v-text-field>
        <v-text-field v-model="value.query" label="Query"></v-text-field>
    </div>
</template>

<script>
    export default {
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
