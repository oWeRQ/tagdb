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
        <template v-slot:item.name="{ item }">
            <TagChip @click="editItem(item)" :tag="item" small></TagChip>
        </template>
        <template v-slot:item.fields="{ item }">
            <v-chip
                v-for="field in item.fields"
                :key="field.id"
                label
                outlined
                small
                class="mr-1"
                @click="editField(field)"
            >{{ field.name }}</v-chip>
        </template>
        <template v-slot:item.created_at="{ item }">
            {{ item.created_at | date }}
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

            <FieldDialog
                ref="fieldDialog"
                :value="editedField"
                @input="getItems"
                @delete="getItems"
            ></FieldDialog>
        </template>
    </v-data-table>
</template>

<script>
    import api from '../../api';
    import cloneDeep from 'clone-deep';
    import date from '../../functions/date';
    import stringifySort from '../../functions/stringifySort';
    import toQueryString from '../../functions/toQueryString';
    import TagChip from './TagChip.vue';
    import TagDialog from './TagDialog.vue';
    import TagImportDialog from './TagImportDialog.vue';
    import FieldDialog from '../field/FieldDialog.vue';

    export default {
        components: {
            TagChip,
            TagDialog,
            TagImportDialog,
            FieldDialog,
        },
        filters: {
            date,
        },
        data() {
            return {
                loading: true,
                total: 0,
                items: [],
                options: {
                    sortBy: ['name'],
                    sortDesc: [false],
                },
                editedItem: {},
                editedField: {},
            }
        },
        computed: {
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            headers() {
                return [
                    { text: 'Tag', value: 'name' },
                    { text: 'Fields', value: 'fields', sortable: false },
                    { text: 'Entities Count', value: 'entities_count' },
                    { text: 'Created', value: 'created_at' },
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
            addItem() {
                this.editItem({
                    name: '',
                    fields: [],
                });
            },
            editItem(item) {
                this.editedItem = cloneDeep(item);
                this.$refs.dialog.show();
            },
            saveItem(result) {
                const item = this.items.find(item => item.id === result.id);
                if (item) {
                    Object.assign(item, result);
                } else {
                    this.items.unshift(result);
                }
            },
            deleteItem(result) {
                this.items = this.items.filter(item => item.id !== result.id)
            },
            editField(item) {
                this.editedField = cloneDeep(item);
                this.$refs.fieldDialog.show();
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
