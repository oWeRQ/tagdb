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

            <CrudDialog
                ref="crudDialog"
                :title="singularTitle"
                :form="form"
                :resource="resource"
                :editable="editable"
                :processValue="processItem"
                :value="editedItem"
                @input="saveItem"
                @delete="deleteItem"
            ></CrudDialog>
        </template>
    </v-data-table>
</template>

<script>
    import axios from 'axios';
    import cloneDeep from 'clone-deep';
    import stringifySort from '../functions/stringifySort';
    import CrudDialog from './CrudDialog';
    import CrudForm from './CrudForm';

    export default {
        components: {
            CrudDialog,
        },
        props: {
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
            resource: {
                type: String,
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
                editedIndex: -1,
                editedItem: {},
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
            resource: 'getItems',
            options: {
                handler: 'getItems',
                deep: true,
            },
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
                this.items = [];
                this.total = 0;
                axios.get(this.resource, { params }).then(response => {
                    this.items = response.data.data.map(this.processItem);
                    this.total = response.data.meta.total;
                    this.loading = false;
                });
            },
            deleteItem(item) {
                if (this.editedIndex > -1) {
                    this.items.splice(this.editedIndex, 1);
                }
            },
            addItem() {
                this.editItem(this.defaultItem);
            },
            editItem(item) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = cloneDeep(item);
                this.$refs.crudDialog.show();
            },
            saveItem(item) {
                if (this.editedIndex > -1) {
                    Object.assign(this.items[this.editedIndex], item);
                } else {
                    this.items.unshift(item);
                }
            },
        },
    };
</script>
