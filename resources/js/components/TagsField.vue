<template>
    <v-combobox
        @input="input"
        :value="value"
        :rules="rules"
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
    ></v-combobox>
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
        methods: {
            input(value) {
                this.$emit('input', value.map(v => {
                    const tag = this.getOrCreateTag(typeof v === 'string' ? v : v.name);
                    return this.returnObject ? tag : tag.name;
                }));
            },
            getOrCreateTag(name) {
                let tag = this.tags.find(tag => tag.name === name);

                if (!tag) {
                    tag = {
                        name,
                        fields: [],
                    };
                    this.tags.push(tag);
                }

                return tag;
            },
        },
    }
</script>
