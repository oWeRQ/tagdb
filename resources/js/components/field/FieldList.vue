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
                <v-toolbar-title>Fields</v-toolbar-title>
            </v-toolbar>
        </template>
        <template v-slot:item.tag="{ item }">
            <TagChip @click="editTag(item.tag)" :tag="item.tag" small></TagChip>
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
                Add Field
            </v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import { mapState } from 'vuex';
    import api from '../../api';
    import cloneDeep from 'clone-deep';
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
            }
        },
        computed: {
            ...mapState([
                'tags',
            ]),
            serverItemsLength() {
                return Math.max(this.items.length, this.total);
            },
            headers() {
                return [
                    { text: 'Tag', value: 'tag', sortable: false },
                    { text: 'Name', value: 'name' },
                    { text: 'Code', value: 'code' },
                    { text: 'Type', value: 'type' },
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
                    value: cloneDeep(item),
                }, {
                    input: this.saveItem,
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
                    value: cloneDeep(item),
                }, {
                    input: this.getItems,
                    delete: this.getItems,
                });
            },
        },
    };
</script>
