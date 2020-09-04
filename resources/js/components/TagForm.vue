<template>
    <div>
        <h2>Tag Form</h2>
        <v-text-field v-for="(field, i) in editable" :key="field.value"
            v-model="editedItem[field.value]"
            :label="field.text"
            :autofocus="i === 0"
        ></v-text-field>
    </div>
</template>

<script>
    export default {
        props: {
            editable: {
                type: Array,
            },
            editedItem: {
                type: Object,
            },
        },
    }
</script>
