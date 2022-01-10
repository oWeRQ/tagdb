<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="serverItemsLength"
        :loading="loading"
        :items-per-page="100"
        :footer-props="{
            itemsPerPageOptions: [10, 20, 50, 100],
        }"
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <v-toolbar flat color="white" class="flex-grow-0">
                <v-toolbar-title>{{ title }}</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon @click="editItem(item)" color="grey">
                mdi-pencil
            </v-icon>
        </template>
        <template v-slot:footer.prepend>
            <v-btn text large color="blue darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add {{ singularTitle }}
            </v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import cloneDeep from 'clone-deep';
    import updateItem from '../../functions/updateItem';
    import stringifySort from '../../functions/stringifySort';
    import CrudDialog from './CrudDialog';
    import CrudForm from './CrudForm';

    export default {
        props: {
            dialog: {
                type: Object,
                default: () => CrudDialog,
            },
            form: {
                type: Object,
                default: () => CrudForm,
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
                    { text: 'ID', value: 'id' },
                    { text: 'Name', value: 'name' },
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
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            singularTitle() {
                return this.title.replace(/ies$/, 'y').replace(/s$/, '');
            },
            headers() {
                return [
                    ...this.columns,
                    { text: 'Actions', value: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy, this.options.sortDesc);
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
                    form: this.form,
                    api: this.api,
                    editable: this.editable,
                    value: cloneDeep(item),
                }, {
                    input: this.saveItem,
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
