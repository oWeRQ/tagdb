<template>
    <v-combobox
        ref="combobox"
        @input="input"
        @change="change"
        :search-input.sync="search"
        :loading="loading"
        :value="value"
        :rules="rules"
        :items="filteredTags"
        :label="label"
        item-text="name"
        item-value="name"
        no-filter
        chips
        multiple
        hide-selected
        hide-no-data
        deletable-chips
        auto-select-first
        :clearable="solo"
        :dense="solo"
        :solo="solo"
        :hide-details="solo"
        :return-object="returnObject"
        :autofocus="autofocus"
        :prepend-icon="prependIcon"
        :prepend-inner-icon="prependInnerIcon"
    >
        <template v-slot:selection="{ item, index }">
            <v-chip close @click.stop="click(index, item)" @click:close="remove(index)" class="lighten-2" :color="item.color" :dark="!!item.color">
                <v-icon left v-if="returnObject && !item.id" @click.stop="plus(index, item)">
                    mdi-plus
                </v-icon>
                <span :style="{'text-decoration': isHyphen(item) ? 'line-through' : 'none'}">
                    {{ itemText(item) }}
                </span>
                <sup v-if="returnObject && item.fields.length">{{ item.fields.length }}</sup>
            </v-chip>
        </template>
        <template v-slot:item="{ item }">
            <v-icon @click="keepActive" class="mr-2" :class="iconColor(item)">
                mdi-tag-plus-outline
            </v-icon>
            <span :class="textColor(item)">{{ item.name }}</span>
            <span v-if="item.entities_count" class="caption grey--text text--lighten-1 ml-2">({{ item.entities_count }})</span>
        </template>
    </v-combobox>
</template>

<script>
    import api from '../../api';
    import fuzzyFilter from '../../functions/fuzzyFilter';

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
            hiddenTags: {
                type: Array,
                default: () => [],
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
            prependIcon: {
                type: String,
            },
            prependInnerIcon: {
                type: String,
            },
        },
        data() {
            return {
                tags: [],
                search: '',
                loading: false,
                isKeepActive: false,
            };
        },
        computed: {
            filteredTags() {
                return fuzzyFilter(this.search, this.tags, 'name');
            },
        },
        watch: {
            hiddenTags: 'fetchTags',
            value: 'fetchTags',
        },
        mounted() {
            this.fetchTags();
        },
        methods: {
            iconColor(item) {
                const prefix = (item.color || 'grey') + '--text text--';
                if (item.color) {
                    return prefix + (item.entities_count ? 'lighten-1' : 'lighten-3');
                } else {
                    return prefix + (item.entities_count ? 'darken-1' : 'lighten-1');
                }
            },
            textColor(item) {
                const prefix = (item.color || 'grey') + '--text text--';
                if (item.color) {
                    return prefix + (item.entities_count ? 'darken-2' : 'lighten-2');
                } else {
                    return prefix + (item.entities_count ? 'darken-3' : 'lighten-0');
                }
            },
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
            plus(index, item) {
                this.$emit('click:plus', item);
            },
            remove(index) {
                this.$emit('input', this.value.filter((v, i) => i !== index));
            },
            fetchTags() {
                const params = {
                    with_tags: (this.returnObject ? this.value.map(tag => tag.name) : this.value).concat(this.hiddenTags),
                };
                this.loading = true;
                api.tags.index(params).then(tags => {
                    this.tags = tags;
                    this.loading = false;
                });
            },
            keepActive() {
                this.isKeepActive = true;
            },
            change() {
                if (!this.isKeepActive) {
                    this.$refs.combobox.isMenuActive = false;
                } else {
                    this.isKeepActive = false;
                }
            },
            input(value) {
                this.$emit('input', value.map(item => {
                    if (this.returnObject && typeof item === 'string') {
                        const name = item.replace(/^[+-]/, '');
                        const tag = this.tags.find(tag => tag.name === name);
                        return { fields: [], ...tag, name: item };
                    }

                    return item;
                }));

                this.search = '';
            },
        },
    }
</script>
