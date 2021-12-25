<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :server-items-length="serverItemsLength"
        :loading="loading"
        :items-per-page="100"
        :footer-props="{
            itemsPerPageOptions: [10, 20, 50, 100],
        }"
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <v-toolbar flat color="white" class="flex-grow-0">
                <v-toolbar-title>Tags</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon @click="editItem(item)" color="grey">
                mdi-pencil
            </v-icon>
        </template>
        <template v-slot:footer.prepend>
            <v-btn text large color="blue darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add Tag
            </v-btn>
            <v-btn text large color="blue darken-3" @click="exportTags">
                <v-icon left>mdi-export</v-icon>
                Export
            </v-btn>
            <v-btn text large color="blue darken-3" @click="$refs.importDialog.show()">
                <v-icon left>mdi-import</v-icon>
                Import
            </v-btn>

            <TagDialog
                ref="dialog"
                :value="editedItem"
                @input="saveItem"
                @delete="deleteItem"
            ></TagDialog>

            <TagImportDialog
                ref="importDialog"
                @done="getItems"
            ></TagImportDialog>
        </template>
    </v-data-table>
</template>

<script>
    import api from '../../api';
    import cloneDeep from 'clone-deep';
    import stringifySort from '../../functions/stringifySort';
    import toQueryString from '../../functions/toQueryString';
    import TagDialog from './TagDialog.vue';
    import TagImportDialog from './TagImportDialog.vue';

    export default {
        components: {
            TagDialog,
            TagImportDialog,
        },
        data() {
            return {
                loading: true,
                total: 0,
                items: [],
                options: {},
                editedIndex: -1,
                editedItem: {},
            }
        },
        computed: {
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            headers() {
                return [
                    { text: 'ID', value: 'id' },
                    { text: 'Name', value: 'name' },
                    { text: 'Color', value: 'color' },
                    { text: 'Fields', value: 'fields.length', sortable: false },
                    { text: 'Entities Count', value: 'entities_count' },
                    { text: 'Actions', value: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy, this.options.sortDesc);
            },
        },
        watch: {
            options: 'getItems',
        },
        methods: {
            getItems() {
                const params = {
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                api.tags.index(params).then(items => {
                    this.items = items;
                    this.total = items.meta.total;
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
                    name: '',
                    fields: [],
                });
            },
            editItem(item) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = cloneDeep(item);
                this.$refs.dialog.show();
            },
            saveItem(item) {
                item = this.processItem(item);
                if (this.editedIndex > -1) {
                    Object.assign(this.items[this.editedIndex], item);
                } else {
                    this.items.unshift(item);
                }
            },
            exportTags() {
                const params = {
                    export: this.$store.state.currentProject.name + '.json',
                };
                window.open(api.tags.resource + '?' + toQueryString(params));
            },
        },
    };
</script>
