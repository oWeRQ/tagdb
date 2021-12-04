<template>
    <div>
        <v-text-field v-for="(field, i) in editable" :key="field.value"
            v-model="value[field.value]"
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
                default: () => [
                    { text: 'Name', value: 'name' },
                ],
            },
            value: {
                type: Object,
                default: () => {},
            },
        },
    }
</script>
