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
                <v-toolbar-title class="mr-2" v-title>Tags</v-toolbar-title>
                <EntitySearch v-model="search"></EntitySearch>
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
            <v-btn text large color="blue darken-3" @click="importTags">
                <v-icon left>mdi-import</v-icon>
                Import
            </v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import api from '../../api';
    import updateItem from '../../functions/updateItem';
    import date from '../../functions/date';
    import stringifySort from '../../functions/stringifySort';
    import toQueryString from '../../functions/toQueryString';
    import TagChip from './TagChip.vue';
    import TagDialog from './TagDialog.vue';
    import TagImportDialog from './TagImportDialog.vue';
    import FieldDialog from '../field/FieldDialog.vue';
    import EntitySearch from '../entity/EntitySearch.vue';

    export default {
        components: {
            TagChip,
            EntitySearch,
        },
        filters: {
            date,
        },
        data() {
            return {
                loading: true,
                search: '',
                total: 0,
                items: [],
                options: {
                    sortBy: ['name'],
                    sortDesc: [false],
                },
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
            search: 'getItems',
            options: 'getItems',
        },
        methods: {
            getItems() {
                const params = {
                    search: this.search,
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
                this.$root.showDialog(TagDialog, {
                    value: item,
                }, {
                    input: this.saveItem,
                    delete: this.deleteItem,
                });
            },
            saveItem(result) {
                updateItem(this.items, result);
            },
            deleteItem(result) {
                this.items = this.items.filter(item => item.id !== result.id)
            },
            editField(item) {
                this.$root.showDialog(FieldDialog, {
                    value: item,
                }, {
                    input: this.getItems,
                    delete: this.getItems,
                });
            },
            exportTags() {
                const params = {
                    export: this.$store.state.currentProject.name + '.json',
                };
                window.open(api.tags.resource + '?' + toQueryString(params));
            },
            importTags() {
                this.$root.showDialog(TagImportDialog, {}, {
                    done: this.getItems,
                });
            },
        },
    };
</script>
