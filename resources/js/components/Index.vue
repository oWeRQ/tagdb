<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :items-per-page="15"
        :server-items-length="total"
        :loading="loading"
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat color="white">
                <v-toolbar-title>{{ title }}</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-autocomplete
                    v-model="queryTags"
                    :items="tags"
                    chips
                    multiple
                    clearable
                    dense
                    solo
                    single-line
                    color="blue-grey lighten-2"
                    label="Tags"
                    item-text="name"
                    item-value="name"
                    :return-object="false"
                    :hide-details="true"
                    :hide-selected="true"
                    :deletable-chips="true"
                ></v-autocomplete>
                <v-spacer></v-spacer>
                <v-dialog v-if="editedItem" v-model="dialog" max-width="500px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn fab dark color="indigo" v-bind="attrs" v-on="on">
                            <v-icon dark>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ editedIndex > -1 ? 'Update' : 'Create' }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-autocomplete
                                v-model="editedItem.tags"
                                :items="tags"
                                chips
                                multiple
                                color="blue darken-1"
                                label="Tags"
                                item-text="name"
                                item-value="name"
                                :return-object="true"
                                :hide-selected="true"
                                :deletable-chips="true"
                                :autofocus="true"
                            ></v-autocomplete>
                            <v-text-field v-model="editedItem.name" label="Name"></v-text-field>
                            <v-text-field v-for="field in editedFields" :key="field.id"
                                v-model="editedItem.contents[field.id]"
                                :label="field.name"
                            ></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                            <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.tags="{ item }">
            <v-chip v-for="tag in item.tags" :key="tag.name" class="mr-1" @click="addTag(tag)">{{ tag.name }}</v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon small @click="editItem(item)" class="mr-2">
                mdi-pencil
            </v-icon>
            <v-icon small @click="deleteItem(item)">
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

    function getFields(items) {
        let fields = [];
        for (let item of items) {
            fields = [...fields, ...item.fields];
        }
        return fields;
    }

    export default {
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
                editedIndex: null,
                editedItem: null,
                tags: [],
                queryTags: [],
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
                return getFields(this.itemsTags);
                // return getFields(this.tags.filter(tag => this.queryTags.includes(tag.name)));
            },
            headers() {
                const before = [
                    { text: 'ID', value: 'id', width: '70px' },
                    { text: 'Tags', value: 'tags', sortable: false },
                    { text: 'Name', value: 'name' },
                ];
                const after = [
                    { text: 'Actions', value: 'actions', sortable: false },
                ];
                const fields = this.displayFields.map((field) => {
                    return { text: field.name, value: 'contents.' + field.id, sortable: false };
                });

                return [...before, ...fields, ...after];
            },
            query() {
                return 'query=' + JSON.stringify({
                    tags: this.queryTags,
                });
            },
        },
        watch: {
            queryTags: 'getItems',
            options: {
                handler: 'getItems',
                deep: true,
            },
        },
        mounted() {
            this.editedReset();
            this.getTags();
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
                if (this.editedIndex > -1) {
                    axios.put(this.resource + '/' + this.editedItem.id, this.editedItem).then(response => {
                        console.log('response', response);
                        Object.assign(this.items[this.editedIndex], this.processItem(response.data.data));
                        this.close();
                    });
                } else {
                    axios.post(this.resource, this.editedItem).then(response => {
                        console.log('response', response);
                        this.items.push(this.processItem(response.data.data));
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
                this.$nextTick(this.editedReset);
            },
            editedReset() {
                this.editedItem = {
                    tags: [],
                    contents: {}
                };
                this.editedIndex = -1;
            },
            reset() {
                this.queryTags = [];
            },
            addTag(tag) {
                this.queryTags.includes(tag.name) || this.queryTags.push(tag.name);
            },
            removeTag(tag) {
                this.queryTags = this.queryTags.filter(item => item !== tag.name);
            },
        },
    };
</script>
