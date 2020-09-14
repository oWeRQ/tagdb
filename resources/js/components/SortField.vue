<template>
    <v-text-field :value="value" @input="$emit('input', $event)" :label="label"></v-text-field>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
            },
            label: {
                type: String,
            },
        },
    }
</script>
