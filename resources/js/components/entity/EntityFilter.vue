<template>
    <v-menu ref="menu" offset-y :close-on-content-click="false">
        <template v-slot:activator="{ on, attrs }">
            <span>
                <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-filter-variant</v-icon>
                </v-btn>
                <span
                    v-for="(filter, i) in valueFilters"
                    :key="i"
                >
                    <v-chip
                        v-if="filter.value"
                        class="mx-1"
                        close
                        @click:close="remove(filter, i)"
                    >
                        <span class="grey--text text--darken-1 mr-1">
                            {{ filter.text }}:
                        </span>
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
                <v-card-text>
                    <v-row v-for="(filter, i) in filters" :key="i" no-gutters>
                        <v-col>
                            <v-text-field v-model="filter.value" :label="filter.text" hide-details></v-text-field>
                        </v-col>
                        <v-col cols="3">
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
export default {
    props: {
        value: {},
        fields: {},
    },
    data() {
        return {
            filters: [],
        };
    },
    computed: {
        rules() {
            return {
                required: v => !!v,
            };
        },
        operators() {
            return [
                { text: 'Equal', value: 'eq' },
                { text: 'Contains', value: 'like' },
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
        valueFilters() {
            return this.fields.map(field => {
                for (const operator in this.value[field.value]) {
                    const value = this.value[field.value][operator];
                    return {
                        text: field.text,
                        name: field.value,
                        operator,
                        value,
                    };
                }
            }).filter(Boolean);
        },
    },
    watch: {
        fields: 'refresh',
    },
    methods: {
        getOperatorIcon(filter) {
            return {

            }[filter.operator];
        },
        refresh() {
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
            this.refresh();
            this.close();
        },
        close() {
            this.$refs.menu.isActive = false;
        },
    },
};
</script>
