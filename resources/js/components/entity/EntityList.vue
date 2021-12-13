<template>
    <v-data-table
        v-model="selected"
        :show-select="true"
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="serverItemsLength"
        :loading="loading"
        :multi-sort="multiSort"
        :items-per-page="100"
        :footer-props="{
            itemsPerPageOptions: [10, 20, 50, 100, 500, 1000],
        }"
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <EntitySelectionToolbar v-if="selected.length"
                v-model="selected"
                @update="getItems"
                :query-tags="queryTags"
                class="flex-grow-0"
            ></EntitySelectionToolbar>
            <v-toolbar v-show="!selected.length" flat color="white" class="flex-grow-0">
                <v-toolbar-title class="mr-4">{{ projectName }}</v-toolbar-title>
                <TagsField
                    v-model="query.tags"
                    solo
                    hyphen
                    class="shrink mr-3"
                ></TagsField>
                <v-text-field
                    v-model="query.search"
                    label="Search"
                    dense
                    solo
                    single-line
                    hide-details
                    clearable
                    class="shrink mr-3"
                ></v-text-field>
                <v-btn icon @click="addPreset">
                    <v-icon>mdi-database-plus</v-icon>
                </v-btn>
                <v-btn icon @click="getItems">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-toolbar>
        </template>
        <template v-slot:item="{ item, headers, isSelected, isMobile, select }">
            <EntityRow
                :query="query"
                @click:tag="query.tags.push($event.name)"
                :item="item"
                :headers="headers"
                :isSelected="isSelected"
                :isMobile="isMobile"
                :select="select"
                @edit="editItem(item)"
            ></EntityRow>
        </template>
        <template v-slot:footer.prepend>
            <v-btn text large color="blue darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add Entity
            </v-btn>
            <v-btn text large color="blue darken-3" @click="$refs.exportDialog.show()">
                <v-icon left>mdi-export</v-icon>
                Export
            </v-btn>
            <v-btn text large color="blue darken-3" @click="$refs.importDialog.show()">
                <v-icon left>mdi-import</v-icon>
                Import
            </v-btn>

            <PresetDialog
                ref="presetDialog"
                :value="editedPreset"
            ></PresetDialog>

            <EntityDialog
                ref="entityDialog"
                :value="editedItem"
                @input="saveItem"
                @delete="deleteItem"
            ></EntityDialog>

            <ExportDialog
                ref="exportDialog"
                :filename="exportFilename"
                :headers="exportHeaders"
                :params="exportParams"
            ></ExportDialog>

            <ImportDialog
                ref="importDialog"
                :params="importParams"
                @done="getItems"
            ></ImportDialog>
        </template>
    </v-data-table>
</template>

<script>
    import { mapState } from 'vuex';
    import api from '../../api';
    import cloneDeep from 'clone-deep';
    import stringifySort from '../../functions/stringifySort';
    import ucwords from '../../functions/ucwords';
    import PresetDialog from '../preset/PresetDialog';
    import EntityDialog from './EntityDialog';
    import ExportDialog from './ExportDialog';
    import ImportDialog from './ImportDialog';
    import TagsField from './TagsField';
    import EntitySelectionToolbar from './EntitySelectionToolbar';
    import EntityRow from './EntityRow';
    import EntityForm from './EntityForm';
    import PresetForm from '../preset/PresetForm';

    export default {
        components: {
            PresetDialog,
            EntityDialog,
            ExportDialog,
            ImportDialog,
            TagsField,
            EntitySelectionToolbar,
            EntityRow,
        },
        props: {
            entityForm: {
                type: Object,
                default: () => EntityForm,
            },
            presetForm: {
                type: Object,
                default: () => PresetForm,
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
            ...mapState([
                'currentProject',
                'tags',
            ]),
            projectName() {
                return this.currentProject?.name;
            },
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
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
                    { text: 'Created', value: 'created_at', type: 'date', width: '120px' },
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
            exportHeaders() {
                return this.headers.slice(0, -2);
            },
            exportFilename() {
                return (this.query.tags.join(' ') || this.projectName) + '.csv';
            },
            exportParams() {
                return {
                    query: JSON.stringify(this.query),
                    sort: this.sort,
                };
            },
            importParams() {
                return {
                    tags: this.query.tags,
                };
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
                console.time('get items');
                api.entities.index(params).then(items => {
                    console.timeEnd('get items');
                    console.time('render items');
                    this.items = items.map(this.processItem);
                    this.total = items.meta.total;
                    this.loading = false;
                    this.$nextTick(() => {
                        console.timeEnd('render items');
                    });
                });
            },
            deleteItem(item) {
                if (this.editedIndex > -1) {
                    this.items.splice(this.editedIndex, 1);
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
            saveItem(item) {
                item = this.processItem(item);
                if (this.editedIndex > -1) {
                    Object.assign(this.items[this.editedIndex], item);
                } else {
                    this.items.unshift(item);
                }
            },
            resetQuery() {
                this.query.search = '';
                this.query.tags = [];
            },
            addPreset() {
                this.editedPreset = {
                    name: ucwords(this.query.tags.join(' ')),
                    sort: this.sort || '-created_at',
                    query: JSON.stringify(this.query),
                };
                this.$refs.presetDialog.show();
            },
        },
    };
</script>
