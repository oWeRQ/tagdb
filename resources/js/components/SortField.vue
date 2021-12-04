<template>
    <v-select
        :label="label"
        :items="items"
        v-model="sort.sortBy"
        item-text="name"
        item-value="value"
        multiple
        :menuProps="{ closeOnContentClick: true }"
    >
        <template v-slot:selection="{ item, index }">
            <v-chip @click.stop="reverse(index)">
                {{ item.name }}
                <v-icon color="grey darken-1" size="18">
                    mdi-arrow-{{ sort.sortDesc[index] ? 'down' : 'up' }}
                </v-icon>
            </v-chip>
        </template>
    </v-select>
</template>

<script>
    import parseSort from '../functions/parseSort'
    import stringifySort from '../functions/stringifySort'

    export default {
        props: {
            value: {
                type: String,
            },
            label: {
                type: String,
            },
            fields: {
                type: Array,
            },
        },
        data() {
            return {
                sort: {
                    sortBy: [],
                    sortDesc: [],
                },
            };
        },
        computed: {
            items() {
                return [
                    { name: 'Name', value: 'name' },
                    { name: 'Created At', value: 'created_at' },
                    ...this.fields.map(field => ({
                        name: field.name,
                        value: 'contents.' + field.id,
                    })),
                ];
            },
        },
        watch: {
            value: {
                handler() {
                    this.sort = this.value ? parseSort(this.value) : {
                        sortBy: [],
                        sortDesc: [],
                    };
                },
                immediate: true,
            },
            sort: {
                handler: 'input',
                deep: true,
            },
        },
        methods: {
            input() {
                this.$emit('input', stringifySort(this.sort.sortBy, this.sort.sortDesc));
            },
            reverse(index) {
                this.sort.sortDesc[index] = !this.sort.sortDesc[index];
                this.input();
            },
        },
    }
</script>
