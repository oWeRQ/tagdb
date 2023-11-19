<template>
    <v-data-table-server
        :headers="headers"
        :items="items"
        v-model:options="options"
        :items-length="serverItemsLength"
        :loading="loading"
        :items-per-page="100"
        :items-per-page-options="[100, 500, 1000]"
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <v-toolbar flat color="white" class="flex-grow-0">
                <v-toolbar-title v-title>{{ title }}</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-for="column in slotColumn" v-slot:[column.slot]="{ item }">
            <component :is="column.component" :item="item" :value="item[column.key]" />
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon @click="editItem(item)" color="grey">
                mdi-pencil
            </v-icon>
        </template>
        <template v-slot:footer.prepend>
            <v-btn variant="text" size="large" color="blue darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add {{ singularTitle }}
            </v-btn>
            <v-spacer></v-spacer>
        </template>
    </v-data-table-server>
</template>

<script>
    import updateItem from '../../functions/updateItem';
    import stringifySort from '../../functions/stringifySort';
    import CrudDialog from './CrudDialog.vue';
    import CrudForm from './CrudForm.vue';
    import { markRaw } from 'vue';

    export default {
        props: {
            dialog: {
                type: Object,
                default: () => markRaw(CrudDialog),
            },
            form: {
                type: Object,
                default: () => markRaw(CrudForm),
            },
            defaultItem: {
                type: Object,
                default: () => {},
            },
            title: {
                type: String,
                default: 'Items',
            },
            api: {
                type: Object,
                required: true,
            },
            columns: {
                type: Array,
                default: () => [
                    { title: 'ID', key: 'id' },
                    { title: 'Name', key: 'name' },
                ],
            },
            editable: {
                type: Array,
                default: () => [
                    { text: 'Name', value: 'name' },
                ],
            },
            processItem: {
                type: Function,
                default: value => value,
            },
        },
        data() {
            return {
                loading: true,
                total: 0,
                items: [],
                options: {},
            }
        },
        computed: {
            slotColumn() {
                return this.columns.filter(column => column.component).map(column => ({
                    ...column,
                    slot: 'item.' + column.key,
                    component: column.component,
                }));
            },
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            singularTitle() {
                return this.title.replace(/ies$/, 'y').replace(/s$/, '');
            },
            headers() {
                return [
                    ...this.columns,
                    { title: 'Actions', key: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy);
            },
        },
        watch: {
            api() {
                this.items = [];
                this.total = 0;
                this.getItems();
            },
            options: 'getItems',
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                const params = {
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                this.api.index(params).then(items => {
                    this.items = items.map(this.processItem);
                    this.total = items.meta.total;
                    this.loading = false;
                });
            },
            addItem() {
                this.editItem(this.defaultItem);
            },
            editItem(item) {
                this.$root.showDialog(this.dialog, {
                    title: this.singularTitle,
                    form: markRaw(this.form),
                    api: this.api,
                    editable: this.editable,
                    processItem: this.processItem,
                    value: item,
                }, {
                    save: this.saveItem,
                    delete: this.deleteItem,
                });
            },
            saveItem(result) {
                updateItem(this.items, result);
            },
            deleteItem(result) {
                this.items = this.items.filter(item => item.id !== result.id);
            },
        },
    };
</script>
