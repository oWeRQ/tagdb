<template>
    <v-data-table-server
        v-model="selected"
        :show-select="true"
        :return-object="true"
        :headers="headers"
        :items="displayItems"
        :options="options"
        @update:options="updateOptions"
        :items-length="serverItemsLength"
        :loading="loading"
        :items-per-page="100"
        :footer-props="{
            itemsPerPageOptions: [100, 500, 1000],
        }"
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <EntitySelectionToolbar v-if="selected.length"
                v-model="selected"
                @update="getItems"
                :query-tag-names="allQueryTagNames"
                class="flex-grow-0"
            ></EntitySelectionToolbar>
            <v-toolbar v-show="!selected.length" flat color="white" class="flex-grow-0">
                <v-toolbar-title class="mr-2 flex-grow-0" :style="{'flex-basis': 'auto'}" v-title>{{ title }}</v-toolbar-title>
                <v-btn v-if="!isPreset" icon @click="addPreset" class="mr-2">
                    <v-icon>mdi-database-plus</v-icon>
                </v-btn>
                <v-btn v-if="isPreset" icon @click="editPreset(preset)" class="mr-2">
                    <v-icon>mdi-database-edit</v-icon>
                </v-btn>
                <EntityQuery
                    v-model="query"
                    :hidden-tags="presetQueryTagNames"
                    :fields="filterFields"
                ></EntityQuery>
                <v-btn icon @click="getItems">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-toolbar>
        </template>
        <template v-slot:item="{ item, internalItem, isSelected, toggleSelect }">
            <EntityRow
                :tags="allQueryTagNames"
                @click:tag="addQueryTag"
                :item="item"
                :headers="headers"
                :isSelected="isSelected([internalItem])"
                :select="() => toggleSelect(internalItem)"
                @edit="editItem(item, $event)"
            ></EntityRow>
        </template>
        <template v-slot:footer.prepend>
            <v-btn variant="text" size="large" color="blue-darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add Entity
            </v-btn>
            <v-btn variant="text" size="large" color="blue-darken-3" @click="showExport()">
                <v-icon left>mdi-export</v-icon>
                Export
            </v-btn>
            <v-btn variant="text" size="large" color="blue-darken-3" @click="showImport()">
                <v-icon left>mdi-import</v-icon>
                Import
            </v-btn>
            <v-spacer></v-spacer>
        </template>
    </v-data-table-server>
</template>

<script>
    import { mapState } from 'vuex';
    import api from '../../api';
    import cancelSignalFactory from '../../functions/cancelSignalFactory';
    import updateItem from '../../functions/updateItem';
    import parseSort from '../../functions/parseSort';
    import stringifySort from '../../functions/stringifySort';
    import ucwords from '../../functions/ucwords';
    import isObjectChange from '../../functions/isObjectChange';

    import EntityDialog from './EntityDialog.vue';
    import EntityQuery from './EntityQuery.vue';
    import EntityRow from './EntityRow.vue';
    import EntitySelectionToolbar from './EntitySelectionToolbar.vue';
    import ExportDialog from './ExportDialog.vue';
    import ImportDialog from './ImportDialog.vue';
    import PresetDialog from '../preset/PresetDialog.vue';

    export default {
        components: {
            EntityRow,
            EntityQuery,
            EntitySelectionToolbar,
        },
        data() {
            return {
                loading: false,
                total: 0,
                items: [],
                options: {
                    sortBy: [
                        {
                            key: 'created_at',
                            order: 'desc',
                        },
                    ],
                    page: undefined,
                    itemsPerPage: undefined,
                },
                selected: [],
                query: this.defaultQuery(),
            };
        },
        computed: {
            ...mapState({
                currentProject: state => state.auth.currentProject,
                tags: state => state.project.tags,
                presets: state => state.project.presets,
            }),
            title() {
                return (this.isPreset ? this.presetName : this.currentProject?.name);
            },
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            isPreset() {
                return !!this.preset;
            },
            preset() {
                const name = this.$route.params.name;
                return this.presets.find(preset => preset.name === name);
            },
            presetName() {
                return this.preset?.name;
            },
            routeQuery() {
                return {
                    ...this.defaultQuery(),
                    ...(this.$route.query.query ? JSON.parse(this.$route.query.query.toString()) : undefined),
                };
            },
            routeSort() {
                return this.$route.query.sort;
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
                return this.presetQueryTags.concat(this.queryTags).filter(tag => tag.name[0] !== '-');
            },
            queryTags() {
                return this.tags.filter(tag => this.queryTagNames.includes(tag.name));
            },
            queryTagNames() {
                return this.query.tags;
            },
            queryTagsString() {
                return ucwords(this.queryTagNames.join(' ')).replace(/\B-/g, 'Without ');
            },
            displayFields() {
                return this.allQueryTags.flatMap(item => item.fields).map((field) => {
                    return { title: field.name, key: 'contents.' + field.id, type: field.type };
                });
            },
            displayItems() {
                return this.items.map(item => {
                    const contents = {};
                    for (let value of item.values) {
                        contents[value.field?.id] = value.content;
                    }
                    return { ...item, contents };
                });
            },
            headers() {
                return [
                    { title: 'Tags', key: 'tags', sortable: false, width: '1%' },
                    { title: 'Name', key: 'name' },
                    ...this.displayFields,
                    { title: 'Created', key: 'created_at', type: 'date', width: '120px' },
                    { title: 'Actions', key: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            filterFields() {
                return [
                    { title: 'Name', key: 'name', type: 'text' },
                    ...this.displayFields,
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy);
            },
        },
        watch: {
            preset: {
                handler() {
                    this.resetItems();
                    this.resetQuery();
                },
            },
            query: {
                handler() {
                    this.pushQuery(this.query, this.presetName, this.sort);
                    this.getItems();
                },
            },
            sort: {
                handler() {
                    this.pushQuery(this.query, this.presetName, this.sort);
                },
            },
        },
        mounted() {
            this.options = { sortBy: parseSort(this.routeSort), page: undefined, itemsPerPage: undefined };
            this.query = this.routeQuery;
        },
        methods: {
            defaultQuery() {
                return {
                    tags: [],
                    filter: {},
                    search: '',
                };
            },
            pushQuery(query, presetName, sort) {
                const route = (presetName ? {name: 'preset', params: { name: presetName }} : {name: 'index'});
                this.pushState({...route, query: {query: JSON.stringify(query), sort}})
            },
            pushState(route) {
                const { href } = this.$router.resolve(route);
                window.history.pushState({}, null, href);
            },
            addQueryTag(tag) {
                this.query = {
                    ...this.query,
                    tags: [
                        ...this.query.tags,
                        tag.name,
                    ],
                };
            },
            updateOptions(options) {
                const isChange = isObjectChange(this.options, options);
                this.options = options;
                if (isChange) {
                    this.getItems();
                }
            },
            cancelGetItems: cancelSignalFactory(),
            getItems() {
                const params = {
                    query: this.query,
                    preset: this.presetName,
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                console.time('get items');
                api.entities.index(params, { signal: this.cancelGetItems() }).then(items => {
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
            resetItems() {
                this.selected = [];
                this.items = [];
                this.total = 0;
            },
            resetQuery() {
                this.options = { sortBy: parseSort(this.preset?.sort) };
                this.query = this.defaultQuery();
            },
            addItem() {
                this.editItem({
                    tags: this.allQueryTags,
                    name: this.query.search,
                    values: [],
                });
            },
            editItem(item, field) {
                this.$root.showDialog(EntityDialog, {
                    value: item,
                    focus: field,
                }, {
                    save: this.saveItem,
                    delete: this.deleteItem,
                });
            },
            saveItem(result) {
                updateItem(this.items, result);
            },
            deleteItem(result) {
                this.items = this.items.filter(item => item.id !== result.id)
            },
            addPreset() {
                this.editPreset({
                    name: this.queryTagsString,
                    sort: this.sort || '-created_at',
                    query: JSON.stringify(this.query),
                });
            },
            editPreset(preset) {
                this.$root.showDialog(PresetDialog, {
                    value: preset,
                }, {
                    save: this.savePreset,
                    delete: this.deletePreset,
                });
            },
            savePreset(rawPreset) {
                if (this.presetName !== rawPreset.name) {
                    this.$router.push({name: 'preset', params: { name: rawPreset.name }});
                }
            },
            deletePreset() {
                this.$router.push({name: 'index'});
            },
            showExport() {
                this.$root.showDialog(ExportDialog, {
                    filename: this.title + (this.queryTagsString && ' - ' + this.queryTagsString),
                    headers: this.headers.slice(0, -2),
                    params: {
                        query: JSON.stringify(this.query),
                        preset: this.presetName,
                        sort: this.sort,
                    },
                });
            },
            showImport() {
                this.$root.showDialog(ImportDialog, {
                    params: {
                        tags: this.queryTagNames,
                        preset: this.presetName,
                    },
                }, {
                    done: this.getItems,
                });
            },
        },
    };
</script>
