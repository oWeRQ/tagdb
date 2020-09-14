<template>
    <v-data-table
        v-model="selected"
        :show-select="true"
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="total"
        :loading="loading"
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
                <v-btn icon @click="editPreset" class="ml-2">
                    <v-icon>mdi-cog</v-icon>
                </v-btn>
                <v-btn icon @click="getItems" class="mr-2">
                    <v-icon>mdi-refresh</v-icon>
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
                    :isUpdate="true"
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
            <v-btn color="primary" @click="initialize">Reset</v-btn>
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
            };
        },
        computed: {
            tags() {
                return this.$root.tags;
            },
            preset() {
                const name = this.$route.params.name;
                return this.$root.presets.find(preset => preset.name === name);
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
                return this.tags.filter(tag => this.query.tags.includes(tag.name));
            },
            displayFields() {
                return this.availableTags.flatMap(item => item.fields);
            },
            displaySlots() {
                return this.displayFields.map((field) => {
                    return { name: 'item.contents.' + field.id, type: field.type };
                });
            },
            headers() {
                const before = [
                    { text: 'Tags', value: 'tags' },
                    { text: 'Name', value: 'name' },
                ];
                const after = [
                    { text: 'Created', value: 'created_at', width: '120px' },
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
            editPreset() {
                this.editedPreset = cloneDeep(this.preset);
                this.$refs.presetDialog.show();
            },
            savePreset(rawPreset) {
                if (this.preset.name !== rawPreset.name) {
                    this.$router.push({name: 'preset', params: { name: rawPreset.name }});
                }

                this.$root.getPresets();
            },
        },
    }
</script>
