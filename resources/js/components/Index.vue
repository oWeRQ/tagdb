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
                <v-toolbar-title>Entities</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-autocomplete
                    v-model="selectedTags"
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
                <v-dialog v-model="dialog" max-width="500px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn fab dark color="indigo" v-bind="attrs" v-on="on">
                            <v-icon dark>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">Entity</span>
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

    function getFields(items) {
        let fields = [];
        for (let item of items) {
            fields = [...fields, ...item.fields];
        }
        return fields;
    }

    export default {
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
                selectedTags: [],
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
            headers() {
                const before = [
                    { text: 'ID', value: 'id', width: '70px' },
                    { text: 'Tags', value: 'tags', sortable: false },
                    { text: 'Name', value: 'name' },
                ];
                const after = [
                    { text: 'Actions', value: 'actions', sortable: false },
                ];
                const fields = getFields(this.itemsTags).map((field) => {
                    return { text: field.name, value: 'contents.' + field.id, sortable: false };
                });

                return [...before, ...fields, ...after];
            },
            query() {
                return 'query=' + JSON.stringify({
                    tags: this.selectedTags,
                });
            },
        },
        watch: {
            selectedTags: 'getItems',
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
                axios.get('/api/v1/entities?' + this.query).then(response => {
                    this.items = response.data.data.map(this.processItem);
                    this.total = response.data.meta.total;
                    this.loading = false;
                });
            },
            save() {
                if (this.editedIndex > -1) {
                    axios.put('/api/v1/entities/' + this.editedItem.id, this.editedItem).then(response => {
                        console.log('response', response);
                        Object.assign(this.items[this.editedIndex], this.editedItem);
                        this.close();
                    });
                } else {
                    axios.post('/api/v1/entities', this.editedItem).then(response => {
                        console.log('response', response);
                        this.items.push(response.data.data);
                        this.close();
                    });
                }
            },
            deleteItem(item) {
                const index = this.items.indexOf(item);
                if (confirm('Are you sure you want to delete this item?')) {
                    axios.delete('/api/v1/entities/' + item.id).then(response => {
                        console.log('response', response);
                        this.items.splice(index, 1);
                    });
                }
            },
            editItem(item) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = Object.assign({}, item);
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
                this.selectedTags = [];
            },
            addTag(tag) {
                this.selectedTags.includes(tag.name) || this.selectedTags.push(tag.name);
            },
            removeTag(tag) {
                this.selectedTags = this.selectedTags.filter(item => item !== tag.name);
            },
        },
    };
</script>
