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
                <v-dialog v-if="editedItem" v-model="dialog" max-width="500px">
                    <v-form ref="form" v-model="editedValid" @submit.prevent="save">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ editedIndex > -1 ? 'Update' : 'Create' }}</span>
                            </v-card-title>
                            <v-card-text>
                                <entity-form v-model="editedItem" :editedFields="editedFields" :tags="tags"></entity-form>
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
    import date from '../functions/date';
    import truncate from '../functions/truncate';
    import EntityForm from './EntityForm';

    function getFields(items) {
        let fields = [];
        for (let item of items) {
            fields = [...fields, ...item.fields];
        }
        return fields;
    }

    function queryPaginate(options) {
        if (!options.sortBy)
            return;

        return {
            page: options.page,
            per_page: options.itemsPerPage,
            sort: options.sortBy.map((v, i) => (options.sortDesc[i] ? '-' : '') + v).join(','),
        };
    }

    export default {
        components: {
            EntityForm,
        },
        filters: {
            date,
            truncate,
        },
        props: {
            resource: {
                type: String,
                required: true,
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
                editedIndex: null,
                editedItem: null,
                defaultItem: {
                    tags: [],
                    contents: {}
                },
            };
        },
        computed: {
            tags() {
                return this.$root.tags;
            },
            title() {
                return this.$route.params.name;
            },
            itemsTags() {
                const tags = [];
                for (let item of this.items) {
                    for (let tag of item.tags) {
                        if (!tags.some(t => t.id === tag.id))
                            tags.push(tag);
                    }
                }
                return tags;
            },
            editedFields() {
                return getFields(this.editedItem.tags);
            },
            displayFields() {
                return getFields(this.itemsTags);
            },
            displaySlots() {
                return this.displayFields.map((field) => {
                    return { name: 'item.contents.' + field.id, type: field.type };
                });
            },
            headers() {
                const before = [
                    { text: 'Name', value: 'name' },
                ];
                const after = [
                    { text: 'Created', value: 'created_at', width: '120px' },
                    { text: 'Actions', value: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
                const fields = this.displayFields.map((field) => {
                    return { text: field.name, value: 'contents.' + field.id };
                });

                return [...before, ...fields, ...after];
            },
            query() {
                return new URLSearchParams({
                    preset: this.$route.params.name,
                    ...queryPaginate(this.options)
                }).toString();
            },
        },
        mounted() {
            this.getItems();
        },
        methods: {
            processItem(item) {
                const contents = {};
                for (let value of item.values) {
                    contents[value.field.id] = value.content;
                }
                return { ...item, contents };
            },
            getItems() {
                this.loading = true;
                axios.get(this.resource + '?' + this.query).then(response => {
                    this.items = response.data.data.map(this.processItem);
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
                this.$refs.form && this.$refs.form.resetValidation();
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
    }
</script>
