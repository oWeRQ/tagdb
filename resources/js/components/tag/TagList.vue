<template>
    <v-data-table-server
        :headers="headers"
        :items="items"
        v-model:options="options"
        :items-length="serverItemsLength"
        :loading="loading"
        :items-per-page="100"
        :items-per-page-options="[100, 500, 1000]"
        :fixed-header="true"
        class="fill-height d-flex flex-column"
    >
        <template v-slot:top>
            <v-toolbar flat color="white" class="flex-grow-0">
                <v-toolbar-title class="mr-2 flex-grow-0" :style="{'flex-basis': 'auto'}" v-title>Tags</v-toolbar-title>
                <div class="d-flex align-center">
                    <ToolbarSearch v-model="search"></ToolbarSearch>
                </div>
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
            {{ date(item.created_at) }}
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon @click="editItem(item)" color="grey">
                mdi-pencil
            </v-icon>
        </template>
        <template v-slot:footer.prepend>
            <v-btn variant="text" size="large" color="blue-darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add Tag
            </v-btn>
            <v-btn variant="text" size="large" color="blue-darken-3" @click="exportTags">
                <v-icon left>mdi-export</v-icon>
                Export
            </v-btn>
            <v-btn variant="text" size="large" color="blue-darken-3" @click="importTags">
                <v-icon left>mdi-import</v-icon>
                Import
            </v-btn>
            <v-spacer></v-spacer>
        </template>
    </v-data-table-server>
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
    import ToolbarSearch from '../common/ToolbarSearch.vue';

    export default {
        components: {
            TagChip,
            ToolbarSearch,
        },
        data() {
            return {
                loading: true,
                search: '',
                total: 0,
                items: [],
                options: {
                    sortBy: [{ key: 'name', order: 'asc' }],
                },
            }
        },
        computed: {
            currentProject() {
                return this.$store.state.auth.currentProject;
            },
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            headers() {
                return [
                    { title: 'Tag', key: 'name' },
                    { title: 'Fields', key: 'fields', sortable: false },
                    { title: 'Entities Count', key: 'entities_count' },
                    { title: 'Created', key: 'created_at' },
                    { title: 'Actions', key: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy);
            },
        },
        watch: {
            search: 'getItems',
            options: 'getItems',
        },
        mounted() {
            this.getItems();
        },
        methods: {
            date,
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
            editField(item) {
                this.$root.showDialog(FieldDialog, {
                    value: item,
                }, {
                    save: this.getItems,
                    delete: this.getItems,
                });
            },
            exportTags() {
                const params = {
                    export: this.currentProject.name + '.json',
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
