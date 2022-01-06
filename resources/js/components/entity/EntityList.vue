<template>
    <v-data-table
        v-model="selected"
        :show-select="true"
        :headers="headers"
        :items="displayItems"
        :options.sync="options"
        :server-items-length="serverItemsLength"
        :loading="loading"
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
                <v-toolbar-title class="mr-2">{{ title }}</v-toolbar-title>
                <v-btn icon @click="addPreset" class="mr-2">
                    <v-icon>mdi-database-plus</v-icon>
                </v-btn>
                <TagsField
                    v-model="queryTags"
                    return-object
                    solo
                    hyphen
                    class="shrink mr-2"
                    prepend-inner-icon="mdi-tag-multiple-outline"
                ></TagsField>
                <EntityFilter v-model="query.filter" :fields="filterFields"></EntityFilter>
                <EntitySearch v-model="query.search"></EntitySearch>
                <v-btn icon @click="getItems">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-toolbar>
        </template>
        <template v-slot:item="{ item, headers, isSelected, isMobile, select }">
            <EntityRow
                :tags="queryTagNames"
                @click:tag="addQueryTag"
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
                @input="savePreset"
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
    import EntityFilter from './EntityFilter';
    import EntitySearch from './EntitySearch';
    import PresetDialog from '../preset/PresetDialog';
    import EntityDialog from './EntityDialog';
    import ExportDialog from './ExportDialog';
    import ImportDialog from './ImportDialog';
    import TagsField from './TagsField';
    import EntitySelectionToolbar from './EntitySelectionToolbar';
    import EntityRow from './EntityRow';

    export default {
        components: {
            EntityFilter,
            EntitySearch,
            PresetDialog,
            EntityDialog,
            ExportDialog,
            ImportDialog,
            TagsField,
            EntitySelectionToolbar,
            EntityRow,
        },
        data() {
            return {
                loading: true,
                total: 0,
                items: [],
                options: {
                    sortBy: ['created_at'],
                    sortDesc: [true],
                },
                selected: [],
                editedItem: null,
                editedPreset: null,
                queryTags: [],
                query: {
                    tags: [],
                    filter: {},
                    search: '',
                },
            }
        },
        computed: {
            ...mapState([
                'currentProject',
                'tags',
            ]),
            title() {
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
            queryTagNames() {
                return this.queryTags.map(tag => tag.name);
            },
            displayFields() {
                return this.queryTags.flatMap(item => item.fields);
            },
            displayItems() {
                return this.items.map(item => {
                    const contents = {};
                    for (let value of item.values) {
                        contents[value.field.id] = value.content;
                    }
                    return { ...item, contents };
                });
            },
            headers() {
                const before = [
                    { text: 'Tags', value: 'tags', sortable: false, width: '1%' },
                    { text: 'Name', value: 'name', type: 'text' },
                ];
                const after = [
                    { text: 'Created', value: 'created_at', type: 'date', width: '120px' },
                    { text: 'Actions', value: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
                const fields = this.displayFields.map((field) => {
                    return { text: field.name, value: 'contents.' + field.id, type: field.type };
                });

                return [...before, ...fields, ...after];
            },
            filterFields() {
                return [
                    { text: 'Name', value: 'name', type: 'text' },
                    ...this.displayFields.map(field => ({ text: field.name, value: 'contents.' + field.id, type: field.type })),
                ];
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
                return (this.queryTagNames.join(' ') || this.title) + '.csv';
            },
            exportParams() {
                return {
                    query: JSON.stringify(this.query),
                    sort: this.sort,
                };
            },
            importParams() {
                return {
                    tags: this.queryTagNames,
                };
            },
        },
        watch: {
            'query.search'() {
                clearTimeout(this._timeout_search);
                this._timeout_search = setTimeout(this.getItems, 500);
            },
            'query.filter'() {
                this.getItems();
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
            queryTags() {
                this.query.tags = this.queryTagNames;
            },
            options: 'getItems',
        },
        methods: {
            addQueryTag(tag) {
                this.queryTags.push(tag);
            },
            getItems() {
                const params = {
                    query: this.query,
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                console.time('get items');
                api.entities.index(params).then(items => {
                    console.timeEnd('get items');
                    console.time('render items');
                    this.items = items;
                    this.total = items.meta.total;
                    this.loading = false;
                    this.$nextTick(() => {
                        console.timeEnd('render items');
                    });
                });
            },
            addItem() {
                this.editItem({
                    tags: this.queryTags,
                    contents: {}
                });
            },
            editItem(item) {
                this.editedItem = cloneDeep(item);
                this.$refs.entityDialog.show();
            },
            saveItem(entity) {
                const item = this.items.find(item => item.id === entity.id);
                if (item) {
                    Object.assign(item, entity);
                } else {
                    this.items.unshift(entity);
                }
            },
            deleteItem(entity) {
                this.items = this.items.filter(item => item.id !== entity.id)
            },
            addPreset() {
                this.editedPreset = {
                    name: ucwords(this.queryTagNames.join(' ')),
                    sort: this.sort || '-created_at',
                    query: JSON.stringify(this.query),
                };
                this.$refs.presetDialog.show();
            },
            savePreset(rawPreset) {
                this.$router.push({name: 'preset', params: { name: rawPreset.name }});
            },
        },
    };
</script>
