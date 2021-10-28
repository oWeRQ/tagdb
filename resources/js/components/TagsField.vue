<template>
    <v-combobox
        @input="input"
        :search-input.sync="search"
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
    >
        <template v-slot:selection="{ item, index }">
            <v-chip close @click="click(index, item)" @click:close="remove(index)">
                <span :style="{'text-decoration': isHyphen(item) ? 'line-through' : 'none'}">
                    {{ itemText(item) }}
                </span>
            </v-chip>
        </template>
        <template v-slot:item="{ item }">
            {{ item.name }}
            <v-spacer></v-spacer>
            <span class="caption grey--text text--darken-1">{{ item.entities_count }}</span>
        </template>
    </v-combobox>
</template>

<script>
    import axios from 'axios';

    function toggleHyphen(value) {
        if (value[0] === '-') {
            return value.slice(1);
        } else {
            return '-' + value;
        }
    }

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
            hyphen: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                tags: [],
                search: '',
            };
        },
        watch: {
            value: 'getTags',
        },
        mounted() {
            this.getTags();
        },
        methods: {
            isHyphen(item) {
                return (this.returnObject ? item.name : item)[0] === '-';
            },
            itemText(item) {
                return (this.returnObject ? item.name : item).replace(/^[+-]/, '');
            },
            click(index, item) {
                this.$emit('click:tag', item);

                if (this.hyphen) {
                    this.$emit('input', this.value.map((v, i) => {
                        if (i === index)
                            return this.returnObject ? { ...v, name: toggleHyphen(v.name) } : toggleHyphen(v);

                        return v;
                    }));
                }
            },
            remove(index) {
                this.$emit('input', this.value.filter((v, i) => i !== index));
            },
            getTags() {
                const params = {
                    with_tags: this.returnObject ? this.value.map(tag => tag.name) : this.value,
                };
                axios.get('/api/v1/tags', { params }).then(response => {
                    this.tags = response.data.data;
                });
            },
            input(value) {
                this.$emit('input', value.map(tag => {
                    return (this.returnObject && typeof tag === 'string' ? this.getOrCreateTag(tag) : tag);
                }));
                this.search = '';
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
