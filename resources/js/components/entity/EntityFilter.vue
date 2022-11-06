<template>
    <v-menu v-model="isShow" offset-y :close-on-content-click="false" max-width="360px">
        <template v-slot:activator="{ on, attrs }">
            <span>
                <v-btn icon v-bind="attrs" v-on="on" @click="focus = 0">
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
                        <v-icon size="14" class="mr-1" v-text="getOperatorIcon(filter)"></v-icon>
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
        value: {
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
                { text: 'Contains', value: 'like' },
                { text: 'Equal', value: 'eq' },
                { text: 'Greater', value: 'gt' },
                { text: 'Greater Equal', value: 'gte' },
                { text: 'Less', value: 'lt' },
                { text: 'Less Equal', value: 'lte' },
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
        value: {
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
            const field = this.fields.find(field => field.value === fieldName);
            if (field) {
                return field.text;
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

            for (const fieldName in this.value) {
                for (const operator in this.value[fieldName]) {
                    const value = this.value[fieldName][operator];
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
                for (const operator in this.value[field.value]) {
                    const value = this.value[field.value][operator];
                    return {
                        text: field.text,
                        name: field.value,
                        operator,
                        value,
                    };
                }

                return {
                    text: field.text,
                    name: field.value,
                    operator: this.typeOperators[field.type] || 'eq',
                    value: '',
                };
            });
        },
        remove(filter, i) {
            this.$emit('input', {
                ...this.value,
                [filter.name]: undefined,
            });
        },
        apply() {
            this.$emit('input', this.filters.reduce((a, c) => {
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
