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
                :query-tags="allQueryTags"
                class="flex-grow-0"
            ></EntitySelectionToolbar>
            <v-toolbar v-show="!selected.length" flat color="white" class="flex-grow-0">
                <v-toolbar-title class="mr-2">{{ title }}</v-toolbar-title>
                <v-btn icon @click="editPreset" class="mr-2">
                    <v-icon>mdi-database-edit</v-icon>
                </v-btn>
                <TagsField
                    v-model="queryTags"
                    return-object
                    :hidden-tags="presetQueryTagNames"
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
                :tags="allQueryTagNames"
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
            <v-btn text large color="blue darken-3" @click="showExport()">
                <v-icon left>mdi-export</v-icon>
                Export
            </v-btn>
            <v-btn text large color="blue darken-3" @click="showImport()">
                <v-icon left>mdi-import</v-icon>
                Import
            </v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import { mapState } from 'vuex';
    import api from '../../api';
    import _ from 'lodash';
    import cloneDeep from 'clone-deep';
    import stringifySort from '../../functions/stringifySort';

    import EntityDialog from './EntityDialog';
    import EntityFilter from './EntityFilter';
    import EntityRow from './EntityRow';
    import EntitySearch from './EntitySearch';
    import EntitySelectionToolbar from './EntitySelectionToolbar';
    import ExportDialog from './ExportDialog';
    import ImportDialog from './ImportDialog';
    import PresetDialog from '../preset/PresetDialog';
    import TagsField from './TagsField';

    export default {
        components: {
            EntityFilter,
            EntityRow,
            EntitySearch,
            EntitySelectionToolbar,
            TagsField,
        },
        data() {
            return {
                loading: true,
                total: 0,
                items: [],
                options: {},
                selected: [],
                editedPreset: null,
                queryTags: [],
                query: {
                    tags: [],
                    filter: {},
                    search: '',
                },
            };
        },
        computed: {
            ...mapState([
                'tags',
                'presets',
            ]),
            title() {
                return this.preset?.name;
            },
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            preset() {
                const name = this.$route.params.name;
                return this.presets.find(preset => preset.name === name);
            },
            presetQuery() {
                return (this.preset ? JSON.parse(this.preset.query) : undefined);
            },
            presetQueryTagNames() {
                return (this.presetQuery ? this.presetQuery.tags : []);
            },
            presetQueryTags() {
                return this.tags.filter(tag => this.presetQueryTagNames.includes(tag.name));
            },
            allQueryTagNames() {
                return this.presetQueryTagNames.concat(this.queryTagNames);
            },
            allQueryTags() {
                return this.tags.filter(tag => this.allQueryTagNames.includes(tag.name));
            },
            queryTagNames() {
                return this.queryTags.map(tag => tag.name);
            },
            displayFields() {
                return this.allQueryTags.flatMap(item => item.fields).map((field) => {
                    return { text: field.name, value: 'contents.' + field.id, type: field.type };
                });
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
                return [
                    { text: 'Tags', value: 'tags', sortable: false, width: '1%' },
                    { text: 'Name', value: 'name' },
                    ...this.displayFields,
                    { text: 'Created', value: 'created_at', type: 'date', width: '120px' },
                    { text: 'Actions', value: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            filterFields() {
                return [
                    { text: 'Name', value: 'name', type: 'text' },
                    ...this.displayFields,
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy, this.options.sortDesc);
            },
            exportHeaders() {
                return this.headers.slice(0, -2);
            },
            exportFilename() {
                return this.title;
            },
            exportParams() {
                return {
                    query: JSON.stringify(this.query),
                    preset: this.preset?.name,
                    sort: this.sort,
                };
            },
            importParams() {
                return {
                    tags: this.allQueryTagNames,
                    preset: this.preset?.name,
                };
            },
        },
        watch: {
            preset() {
                this.items = [];
                this.total = 0;
                this.options = {};
                this.query = {
                    tags: [],
                    filter: {},
                    search: '',
                };
            },
            queryTags() {
                this.query.tags = this.queryTagNames;
            },
            query: {
                deep: true,
                handler: 'getItems',
            },
            options: 'getItems',
        },
        methods: {
            addQueryTag(tag) {
                this.queryTags.push(tag);
            },
            getItems: _.debounce(function() {
                if (!this.preset)
                    return;

                const params = {
                    query: this.query,
                    preset: this.preset.name,
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
            }, 500),
            addItem() {
                this.editItem({
                    tags: this.allQueryTags,
                    contents: {}
                });
            },
            editItem(item) {
                this.$root.showDialog(EntityDialog, {
                    value: cloneDeep(item),
                }, {
                    input: this.saveItem,
                    delete: this.deleteItem,
                });
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
            editPreset() {
                this.$root.showDialog(PresetDialog, {
                    value: cloneDeep(this.preset),
                }, {
                    input: this.savePreset,
                    delete: this.deletePreset,
                });
            },
            savePreset(rawPreset) {
                if (this.preset.name !== rawPreset.name) {
                    this.$router.push({name: 'preset', params: { name: rawPreset.name }});
                }
            },
            deletePreset() {
                this.$router.push({name: 'index'});
            },
            showExport() {
                this.$root.showDialog(ExportDialog, {
                    filename: this.exportFilename,
                    headers: this.exportHeaders,
                    params: this.exportParams,
                });
            },
            showImport() {
                this.$root.showDialog(ImportDialog, {
                    params: this.importParams,
                }, {
                    done: this.getItems,
                });
            },
        },
    }
</script>
