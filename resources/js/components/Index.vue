<template>
    <v-data-table
        v-model="selected"
        :show-select="true"
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
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <EntitySelectionToolbar v-if="selected.length"
                v-model="selected"
                :resource="resource"
                class="flex-grow-0"
            ></EntitySelectionToolbar>
            <v-toolbar v-show="!selected.length" flat color="white" class="flex-grow-0">
                <v-toolbar-title>{{ title }}</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-autocomplete
                    v-model="query.tags"
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
                <v-text-field
                    v-model="query.search"
                    label="Search"
                    dense
                    solo
                    single-line
                    hide-details
                    clearable
                    class="shrink mr-2"
                ></v-text-field>
                <v-btn icon @click="getItems" class="mr-2">
                    <v-icon>mdi-magnify</v-icon>
                </v-btn>
                <v-btn icon @click="addPreset" class="mr-2">
                    <v-icon>mdi-database-plus</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn text large color="grey darken-2" @click="addItem">
                    <v-icon left>mdi-plus</v-icon>
                    Add
                </v-btn>
                <EntityDialog
                    ref="entityDialog"
                    :resource="resource"
                    :isUpdate="editedIndex > -1"
                    :editedItem="editedItem"
                    @save="saveItem"
                ></EntityDialog>
                <PresetDialog
                    ref="presetDialog"
                    :value="editedPreset"
                    @save="savePreset"
                ></PresetDialog>
            </v-toolbar>
        </template>
        <template v-slot:item="{ item, headers, isSelected, select }">
            <EntityRow
                :query="query"
                :item="item"
                :headers="headers"
                :isSelected="isSelected"
                :select="select"
                @edit="editItem(item)"
                @delete="deleteItem(item)"
            ></EntityRow>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="resetQuery">Reset</v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import axios from 'axios';
    import cloneDeep from 'clone-deep';
    import stringifySort from '../functions/stringifySort';
    import EntitySelectionToolbar from './EntitySelectionToolbar';
    import EntityRow from './EntityRow';
    import EntityDialog from './EntityDialog';
    import PresetDialog from './PresetDialog';

    export default {
        components: {
            EntitySelectionToolbar,
            EntityRow,
            EntityDialog,
            PresetDialog,
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
                loading: true,
                total: 0,
                items: [],
                options: {},
                selected: [],
                editedIndex: null,
                editedItem: null,
                editedPreset: null,
                multiSort: false,
                query: {
                    tags: [],
                    search: '',
                },
            }
        },
        computed: {
            tags() {
                return this.$root.tags;
            },
            availableTags() {
                const tags = [];
                for (let item of this.items) {
                    for (let tag of item.tags) {
                        if (!tags.some(t => t.id === tag.id))
                            tags.push(tag);
                    }
                }
                return tags;
            },
            queryTags() {
                return this.tags.filter(tag => this.query.tags.includes(tag.name));
            },
            displayFields() {
                return this.queryTags.flatMap(item => item.fields);
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
                const fields = this.loading ? [] : this.displayFields.map((field) => {
                    return { text: field.name, value: 'contents.' + field.id, type: field.type };
                });

                return [...before, ...fields, ...after];
            },
            sortable() {
                return this.headers.filter(header => header.sortable !== false).map(header => header.value);
            },
            sort() {
                return stringifySort(this.options.sortBy, this.options.sortDesc);
            },
        },
        watch: {
            'query.search'() {
                clearTimeout(this._timeout_search);
                this._timeout_search = setTimeout(this.getItems, 500);
            },
            'query.tags'() {
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
        methods: {
            processItem(item) {
                const contents = {};
                for (let value of item.values) {
                    contents[value.field.id] = value.content;
                }
                return { ...item, contents };
            },
            getItems() {
                const params = {
                    query: this.query,
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                // this.items = [];
                // this.total = 0;
                axios.get(this.resource, { params }).then(response => {
                    this.items = response.data.data.map(this.processItem);
                    this.total = response.data.meta.total;
                    this.loading = false;
                });
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
            addItem() {
                this.editItem({
                    tags: this.queryTags,
                    contents: {}
                });
            },
            editItem(item) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = cloneDeep(item);
                this.$refs.entityDialog.show();
            },
            saveItem(rawItem) {
                if (this.editedIndex > -1) {
                    Object.assign(this.items[this.editedIndex], this.processItem(rawItem));
                } else {
                    this.items.unshift(this.processItem(rawItem));
                }
            },
            resetQuery() {
                this.query.search = '';
                this.query.tags = [];
            },
            addPreset() {
                this.editedPreset = {
                    name: this.query.tags.join(' '),
                    sort: this.sort,
                    query: JSON.stringify(this.query),
                };
                this.$refs.presetDialog.show();
            },
            savePreset(rawPreset) {
                this.$root.getPresets();
            },
        },
    };
</script>
