<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="total"
        :loading="loading"
        :items-per-page="100"
        :footer-props="{
            itemsPerPageOptions: [10, 20, 50, 100],
        }"
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat color="white">
                <v-toolbar-title>{{ title }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn dark color="indigo" @click="editItem(defaultItem)">
                    <v-icon dark left>mdi-plus</v-icon>
                    Add
                </v-btn>
                <v-dialog v-model="dialog" max-width="500px">
                    <v-form v-model="editedValid" @submit.prevent="save">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ editedIndex > -1 ? 'Update' : 'Create' }}</span>
                            </v-card-title>
                            <v-card-text>
                                <component :is="form" :editable="editable" v-model="editedItem" @submit="save"></component>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text type="submit" :disabled="!editedValid">Save</v-btn>
                                <v-btn color="grey darken-1" text @click="close">Cancel</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon @click="editItem(item)" color="grey darken-1" class="mr-2">
                mdi-pencil
            </v-icon>
            <v-icon @click="deleteItem(item)" color="grey darken-1">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import axios from 'axios';
    import cloneDeep from 'clone-deep';
    import CrudForm from './CrudForm';

    function queryPaginate(options) {
        return {
            page: options.page,
            per_page: options.itemsPerPage,
        };
    }

    export default {
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
        },
        data() {
            return {
                dialog: false,
                loading: true,
                total: 0,
                items: [],
                options: {},
                editedValid: false,
                editedIndex: -1,
                editedItem: {},
            }
        },
        computed: {
            headers() {
                return [
                    ...this.columns,
                    { text: 'Actions', value: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            query() {
                return new URLSearchParams({ ...queryPaginate(this.options) }).toString();
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
            // this.editedReset();
            this.getItems();
        },
        methods: {
            getItems() {
                this.loading = true;
                this.items = [];
                this.total = 0;
                axios.get(this.resource + '?' + this.query).then(response => {
                    this.items = response.data.data;
                    this.total = response.data.meta.total;
                    this.loading = false;
                });
            },
            save() {
                if (!this.editedValid)
                    return;

                if (this.editedIndex > -1) {
                    axios.put(this.resource + '/' + this.editedItem.id, this.editedItem).then(response => {
                        console.log('response', response);
                        Object.assign(this.items[this.editedIndex], this.editedItem);
                        this.close();
                    });
                } else {
                    axios.post(this.resource, this.editedItem).then(response => {
                        console.log('response', response);
                        this.items.push(response.data.data);
                        this.close();
                    });
                }
            },
            deleteItem(item) {
                const index = this.items.indexOf(item);
                if (confirm('Are you sure you want to delete this item?')) {
                    axios.delete(this.resource + '/' + item.id).then(response => {
                        console.log('response', response);
                        this.items.splice(index, 1);
                    });
                }
            },
            editItem(item) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = cloneDeep(item);
                this.dialog = true;
            },
            close() {
                this.dialog = false;
                // this.$nextTick(this.editedReset);
            },
            editedReset() {
                this.editedItem = cloneDeep(this.defaultItem);
                this.editedIndex = -1;
            },
        },
    };
</script>
