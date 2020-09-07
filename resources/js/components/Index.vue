<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="total"
        :loading="loading"
        :multi-sort="multiSort"
        :items-per-page="100"
        :footer-props="{
            itemsPerPageOptions: [10, 20, 50, 100],
        }"
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat color="white">
                <v-toolbar-title>{{ title }}</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-text-field
                    v-model="search"
                    label="Search"
                    dense
                    solo
                    single-line
                    hide-details
                    clearable
                    class="shrink mr-3"
                ></v-text-field>
                <v-autocomplete
                    v-model="queryTags"
                    :items="tags"
                    color="blue-grey lighten-2"
                    label="Tags"
                    item-text="name"
                    item-value="name"
                    chips
                    multiple
                    clearable
                    dense
                    solo
                    single-line
                    hide-details
                    hide-selected
                    hide-no-data
                    deletable-chips
                    class="shrink mr-3"
                ></v-autocomplete>
                <v-icon @click="getItems" class="mr-3">mdi-refresh</v-icon>
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
        <template v-for="slot in displaySlots" v-slot:[slot.name]="{ value }">
            <span :key="slot.name">
                <a v-if="value && slot.type === 'url'" :href="value" :title="value" target="_blank">{{ value | truncate }}</a>

                <v-chip v-else-if="value && slot.type === 'color'">
                    <v-avatar left :color="value"></v-avatar>
                    {{ value }}
                </v-chip>

                <template v-else>{{ value | truncate }}</template>
            </span>
        </template>
        <template v-slot:item.tags="{ item }">
            <v-chip-group multiple active-class="primary--text" v-model="queryTags">
                <v-chip v-for="tag in item.tags" :key="tag.name" :value="tag.name" small outlined>
                    {{ tag.name }}
                    <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
                </v-chip>
            </v-chip-group>
        </template>
        <template v-slot:item.created_at="{ item, value }">
            {{ value | date }}
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon @click="editItem(item)" color="grey darken-1" class="mr-2" :title="'ID: ' + item.id">
                mdi-pencil
            </v-icon>
            <v-icon @click="deleteItem(item)" color="grey darken-1">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="reset">Reset</v-btn>
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
            title: {
                type: String,
                default: 'Items',
            },
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
                multiSort: false,
                tags: [],
                queryTags: [],
                search: '',
                defaultItem: {
                    tags: [],
                    contents: {}
                },
            }
        },
        computed: {
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
                return getFields(this.tags.filter(tag => this.queryTags.includes(tag.name)));
            },
            displaySlots() {
                return this.displayFields.map((field) => {
                    return { name: 'item.contents.' + field.id, type: field.type };
                });
            },
            headers() {
                const before = [
                    { text: 'Tags', value: 'tags', sortable: false, width: '1%' },
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
            sortable() {
                return this.headers.filter(header => header.sortable !== false).map(header => header.value);
            },
            query() {
                const query = JSON.stringify({
                    tags: this.queryTags,
                    search: this.search,
                });

                return new URLSearchParams({ query, ...queryPaginate(this.options) }).toString();
            },
        },
        watch: {
            search() {
                clearTimeout(this._timeout_search);
                this._timeout_search = setTimeout(this.getItems, 500);
            },
            queryTags() {
                const sortable = this.options.sortBy.map(value => this.sortable.includes(value));
                if (!sortable.every(Boolean)) {
                    this.options.sortBy = this.options.sortBy.filter((v, i) => sortable[i]);
                    this.options.sortDesc = this.options.sortDesc.filter((v, i) => sortable[i]);
                } else {
                    this.getItems();
                }
            },
            'options.sortBy'(value) {
                if (value.length > 2) {
                    this.options.sortBy = this.options.sortBy.slice(value.length - 2);
                    this.options.sortDesc = this.options.sortDesc.slice(value.length - 2);
                }
            },
            options: 'getItems',
        },
        mounted() {
            this.getTags();
        },
        methods: {
            processItem(item) {
                const contents = {};
                for (let value of item.values) {
                    contents[value.field.id] = value.content;
                }
                return { ...item, contents };
            },
            getTags() {
                axios.get('/api/v1/tags').then(response => {
                    this.tags = response.data.data;
                });
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
                        Object.assign(this.items[this.editedIndex], this.processItem(response.data.data));
                        this.close();
                    });
                } else {
                    axios.post(this.resource, this.editedItem).then(response => {
                        console.log('response', response);
                        this.items.splice(0, 0, this.processItem(response.data.data));
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
                if (this.editedIndex < 0) {
                    this.editedItem.tags = this.tags.filter(tag => this.queryTags.includes(tag.name));
                }
                this.dialog = true;
            },
            close() {
                this.dialog = false;
            },
            editedReset() {
                this.editedItem = cloneDeep(this.defaultItem);
                this.editedIndex = -1;
            },
            reset() {
                this.search = '';
                this.queryTags = [];
            },
        },
    };
</script>
