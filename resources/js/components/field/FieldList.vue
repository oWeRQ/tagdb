<template>
    <v-data-table-server
        :headers="headers"
        :items="items"
        v-model:options="options"
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
            <v-toolbar flat color="white" class="flex-grow-0">
                <v-toolbar-title v-title>Fields</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-slot:item.tag="{ item }">
            <TagChip v-if="item.tag" @click="editTag(item.tag)" :tag="item.tag" small></TagChip>
        </template>
        <template v-slot:item.name="{ item }">
            <v-chip
                label
                outlined
                small
                @click="editItem(item)"
            >{{ item.name }}</v-chip>
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
            <v-btn variant="text" size="large" color="blue darken-3" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Add Field
            </v-btn>
            <v-spacer></v-spacer>
        </template>
    </v-data-table-server>
</template>

<script>
    import { mapState } from 'vuex';
    import api from '../../api';
    import updateItem from '../../functions/updateItem';
    import date from '../../functions/date';
    import stringifySort from '../../functions/stringifySort';
    import TagChip from '../tag/TagChip.vue';
    import TagDialog from '../tag/TagDialog.vue';
    import FieldDialog from './FieldDialog.vue';

    export default {
        components: {
            TagChip,
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
            }
        },
        computed: {
            ...mapState({
                tags: state => state.project.tags,
            }),
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            headers() {
                return [
                    { title: 'Tag', key: 'tag', sortable: false },
                    { title: 'Name', key: 'name' },
                    { title: 'Code', key: 'code' },
                    { title: 'Type', key: 'type' },
                    { title: 'Created', key: 'created_at' },
                    { title: 'Actions', key: 'actions', sortable: false, width: '120px', align: 'center' },
                ];
            },
            sort() {
                return stringifySort(this.options.sortBy, this.options.sortDesc);
            },
        },
        watch: {
            options: 'getItems',
        },
        mounted() {
            this.getItems();  
        },
        methods: {
            date,
            processItem(value) {
                return {
                    ...value,
                    tag: this.tags.find(tag => tag.id === value.tag_id),
                };
            },
            getItems() {
                const params = {
                    sort: this.sort,
                    page: this.options.page,
                    per_page: this.options.itemsPerPage,
                };

                this.loading = true;
                api.fields.index(params).then(items => {
                    this.items = items.map(this.processItem);
                    this.total = items.meta.total;
                    this.loading = false;
                });
            },
            addItem() {
                this.editItem({});
            },
            editItem(item) {
                this.$root.showDialog(FieldDialog, {
                    value: item,
                }, {
                    save: this.saveItem,
                    delete: this.deleteItem,
                });
            },
            saveItem(result) {
                updateItem(this.items, this.processItem(result));
            },
            deleteItem(result) {
                this.items = this.items.filter(item => item.id !== result.id);
            },
            editTag(item) {
                this.$root.showDialog(TagDialog, {
                    value: item,
                }, {
                    save: this.getItems,
                    delete: this.getItems,
                });
            },
        },
    };
</script>
