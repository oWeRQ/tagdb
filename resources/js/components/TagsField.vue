<template>
    <v-autocomplete
        @input="$listeners.input"
        :value="value"
        :items="tags"
        :label="label"
        item-text="name"
        item-value="name"
        chips
        multiple
        clearable
        dense
        hide-selected
        hide-no-data
        deletable-chips
        :solo="solo"
        :hide-details="solo"
        :return-object="returnObject"
        :autofocus="autofocus"
    ></v-autocomplete>
</template>

<script>
    export default {
        props: {
            value: {
                type: Array,
            },
            rules: {
                type: Array,
            },
            label: {
                type: String,
                default: 'Tags',
            },
            returnObject: {
                type: Boolean,
                default: false,
            },
            autofocus: {
                type: Boolean,
                default: false,
            },
            solo: {
                type: Boolean,
                default: false,
            },
        },
        computed: {
            tags() {
                return this.$root.tags;
            },
        },
    }
</script>
