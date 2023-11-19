<template>
    <v-select
        :label="label"
        :items="items"
        v-model="selected"
        item-title="name"
        item-value="value"
        multiple
        :menuProps="{ closeOnContentClick: true }"
    >
        <template v-slot:selection="{ item: { raw: item }, index }">
            <v-chip @click.stop="reverse(index)">
                {{ item.name }}
                <v-icon color="grey-darken-1" size="18">
                    mdi-arrow-{{ getDirection(index) }}
                </v-icon>
            </v-chip>
        </template>
    </v-select>
</template>

<script>
    import parseSort from '../../functions/parseSort';
    import stringifySort from '../../functions/stringifySort';

    export default {
        props: {
            modelValue: {
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
            selected: {
                get() {
                    return this.sort.sortBy.map(item => item.key);
                },
                set(selected) {
                    const sortBy = selected.map(key => ({ key, order: this.getOrder(key) }));
                    this.sort = { sortBy };
                },
            },
        },
        watch: {
            value: {
                handler() {
                    this.sort = { sortBy: parseSort(this.modelValue) };
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
                this.$emit('update:modelValue', stringifySort(this.sort.sortBy));
            },
            reverse(index) {
                this.sort.sortBy[index].order = this.sort.sortBy[index].order === 'desc' ? 'asc' : 'desc';
                this.input();
            },
            getDirection(index) {
                return this.sort.sortBy[index].order === 'desc' ? 'down' : 'up';
            },
            getOrder(key) {
                return this.sort.sortBy.find(item => item.key === key)?.order ?? 'asc';
            },
        },
    }
</script>
