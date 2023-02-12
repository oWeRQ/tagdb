<template>
    <v-menu v-model="isShow" offset-y :close-on-content-click="false" max-width="360px">
        <template v-slot:activator="{ props }">
            <span>
                <v-btn icon v-bind="props" @click="focus = 0">
                    <v-icon>mdi-filter-variant</v-icon>
                </v-btn>
                <span
                    v-for="(filter, i) in valueFilters"
                    :key="i"
                >
                    <v-chip
                        v-if="filter.value"
                        class="mr-1"
                        close
                        @click="edit(filter, i)"
                        @click:close="remove(filter, i)"
                    >
                        <span class="grey--text text--darken-1 mr-1">
                            {{ filter.text }}
                        </span>
                        <v-icon size="14" class="mr-1" :icon="getOperatorIcon(filter)"></v-icon>
                        {{ filter.value }}
                    </v-chip>
                </span>
            </span>
        </template>
        <v-form ref="form" @submit.prevent="apply">
            <v-card>
                <v-card-title class="pb-0">
                    Filter
                </v-card-title>
                <v-card-text v-if="isShow">
                    <v-row v-for="(filter, i) in filters" :key="i" no-gutters>
                        <v-col>
                            <v-text-field v-model="filter.value" :label="filter.text" v-autoselect="focus === i" hide-details clearable></v-text-field>
                        </v-col>
                        <v-col cols="5">
                            <v-select :items="operators" v-model="filter.operator" hide-details class="ml-2"></v-select>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="cancel">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">Apply</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-menu>
</template>

<script>
import { mapState } from 'vuex';

export default {
    props: {
        modelValue: {
            type: Object,
            default: () => ({}),
        },
        fields: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            isShow: false,
            focus: 0,
            valueFilters: [],
            filters: [],
        };
    },
    computed: {
        ...mapState({
            allFields: state => state.project.fields,
        }),
        rules() {
            return {
                required: v => !!v,
            };
        },
        operators() {
            return [
                { title: 'Contains', value: 'like' },
                { title: 'Equal', value: 'eq' },
                { title: 'Greater', value: 'gt' },
                { title: 'Greater Equal', value: 'gte' },
                { title: 'Less', value: 'lt' },
                { title: 'Less Equal', value: 'lte' },
            ];
        },
        typeOperators() {
            return {
                string: 'like',
                text: 'like',
                url: 'like',
                color: 'like',
                rating: 'gte',
            };
        },
    },
    watch: {
        allFields: {
            immediate: true,
            handler: 'valueUpdate',
        },
        modelValue: {
            immediate: true,
            handler: 'valueUpdate',
        },
        fields: {
            immediate: true,
            handler: 'fieldsUpdate',
        },
    },
    methods: {
        getFieldText(fieldName) {
            const field = this.fields.find(field => field.key === fieldName);
            if (field) {
                return field.title;
            }

            const match = fieldName.match(/^contents\.(\d+)$/);
            if (match) {
                const fieldId = +match[1];
                const field = this.allFields.find(field => field.id === fieldId);
                if (field) {
                    return field.name;
                }
            }

            return fieldName;
        },
        getOperatorIcon(filter) {
            return {
                eq: 'mdi-equal',
                like: 'mdi-contain',
                gt: 'mdi-greater-than',
                gte: 'mdi-greater-than-or-equal',
                lt: 'mdi-less-than',
                lte: 'mdi-less-than-or-equal',
            }[filter.operator];
        },
        valueUpdate() {
            const filters = [];

            for (const fieldName in this.modelValue) {
                for (const operator in this.modelValue[fieldName]) {
                    const value = this.modelValue[fieldName][operator];
                    filters.push({
                        text: this.getFieldText(fieldName),
                        name: fieldName,
                        operator,
                        value,
                    });
                }
            }

            this.valueFilters = filters;
        },
        fieldsUpdate() {
            this.focus = 0;
            this.filters = this.fields.map(field => {
                for (const operator in this.modelValue[field.key]) {
                    const value = this.modelValue[field.key][operator];
                    return {
                        text: field.title,
                        name: field.key,
                        operator,
                        value,
                    };
                }

                return {
                    text: field.title,
                    name: field.key,
                    operator: this.typeOperators[field.type] || 'eq',
                    value: '',
                };
            });
        },
        remove(filter, i) {
            this.$emit('update:modelValue', {
                ...this.modelValue,
                [filter.name]: undefined,
            });
        },
        apply() {
            this.$emit('update:modelValue', this.filters.reduce((a, c) => {
                if (c.value) {
                    a[c.name] = { [c.operator]: c.value };
                }
                return a;
            }, {}));
            this.close();
        },
        cancel() {
            this.fieldsUpdate();
            this.close();
        },
        edit(filter, i) {
            this.focus = this.filters.findIndex(item => item.name === filter.name);
            this.isShow = true;
        },
        close() {
            this.isShow = false;
        },
    },
};
</script>
