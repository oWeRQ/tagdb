<template>
    <div>
        <v-text-field v-for="(field, i) in editable" :key="field.value"
            v-model="modelValue[field.value]"
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
            modelValue: {
                type: Object,
                default: () => {},
            },
        },
    }
</script>
