<template>
    <v-data-table
        v-model="selected"
        :show-select="true"
        :headers="headers"
        :items="items"
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
                :resource="resource"
                :query-tags="queryTags"
                class="flex-grow-0"
            ></EntitySelectionToolbar>
            <v-toolbar v-show="!selected.length" flat color="white" class="flex-grow-0">
                <v-toolbar-title class="mr-2">{{ title }}</v-toolbar-title>
                <v-btn icon @click="editPreset">
                    <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon @click="getItems">
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-toolbar>

            <CrudDialog
                ref="presetDialog"
                title="Preset"
                :form="presetForm"
                :resource="presetResource"
                :value="editedPreset"
                @input="savePreset"
                @delete="deleteItem"
            ></CrudDialog>
        </template>
        <template v-slot:item="{ item, headers, isSelected, isMobile, select }">
            <EntityRow
                :query="query"
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

            <CrudDialog
                ref="entityDialog"
                title="Entity"
                :form="form"
                :resource="resource"
                :processValue="processItem"
                :value="editedItem"
                @input="saveItem"
            ></CrudDialog>

            <ExportDialog
                ref="exportDialog"
                :resource="resource"
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
    import axios from 'axios';
    import cloneDeep from 'clone-deep';
    import stringifySort from '../functions/stringifySort';
    import CrudDialog from './CrudDialog';
    import ExportDialog from './ExportDialog';
    import ImportDialog from './ImportDialog';
    import EntitySelectionToolbar from './EntitySelectionToolbar';
    import EntityRow from './EntityRow';
    import EntityForm from './EntityForm';
    import PresetForm from './PresetForm';

    export default {
        components: {
            CrudDialog,
            ExportDialog,
            ImportDialog,
            EntitySelectionToolbar,
            EntityRow,
        },
        props: {
            form: {
                type: Object,
                default: () => EntityForm,
            },
            resource: {
                type: String,
                default: '/api/v1/entities',
            },
            presetForm: {
                type: Object,
                default: () => PresetForm,
            },
            presetResource: {
                type: String,
                default: '/api/v1/presets',
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
            };
        },
        computed: {
            ...mapState([
                'tags',
                'presets',
            ]),
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            preset() {
                const name = this.$route.params.name;
                return this.presets.find(preset => preset.name === name);
            },
            title() {
                return this.preset && this.preset.name;
            },
            query() {
                return (this.preset ? JSON.parse(this.preset.query) : undefined);
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
                return (this.query ? this.tags.filter(tag => this.query.tags.includes(tag.name)) : []);
            },
            displayFields() {
                // return this.availableTags.flatMap(item => item.fields);
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
                const fields = this.displayFields.map((field) => {
                    return { text: field.name, value: 'contents.' + field.id, type: field.type };
                });

                return [...before, ...fields, ...after];
            },
            sort() {
                return stringifySort(this.options.sortBy, this.options.sortDesc);
            },
            exportHeaders() {
                return this.headers.slice(0, -2);
            },
            exportFilename() {
                return this.preset?.name + '.csv';
            },
            exportParams() {
                return {
                    preset: this.preset?.name,
                    sort: this.sort,
                };
            },
            importParams() {
                return {
                    preset: this.preset?.name,
                };
            },
        },
        watch: {
            preset: 'getItems',
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
                if (!this.preset)
                    return;

                const params = {
                    preset: this.preset.name,
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                this.items = [];
                this.total = 0;
                axios.get(this.resource, { params }).then(response => {
                    this.items = response.data.data.map(this.processItem);
                    this.total = response.data.meta.total;
                    this.loading = false;
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
                if (this.editedIndex > -1) {
                    Object.assign(this.items[this.editedIndex], item);
                } else {
                    this.items.unshift(item);
                }
            },
            editPreset() {
                this.editedPreset = cloneDeep(this.preset);
                this.$refs.presetDialog.show();
            },
            savePreset(rawPreset) {
                if (this.preset.name !== rawPreset.name) {
                    this.$router.push({name: 'preset', params: { name: rawPreset.name }});
                }

                this.$store.dispatch('getPresets');
            },
        },
    }
</script>
