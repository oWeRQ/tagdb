<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :items-per-page="15"
        :server-items-length="total"
        :loading="loading"
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar flat color="white">
                <v-toolbar-title>Tags</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="500px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn fab dark color="indigo" v-bind="attrs" v-on="on">
                            <v-icon dark>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">Tag</span>
                        </v-card-title>
                        <v-card-text>
                            <v-text-field v-model="editedItem.name" label="Name" :autofocus="true"></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                            <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon small @click="editItem(item)" class="mr-2">
                mdi-pencil
            </v-icon>
            <v-icon small @click="deleteItem(item)">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
    </v-data-table>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                dialog: false,
                loading: true,
                total: 0,
                items: [],
                options: {},
                headers: [
                    { text: 'ID', value: 'id' },
                    { text: 'Name', value: 'name' },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
                editedIndex: -1,
                editedItem: {
                    name: '',
                },
                defaultItem: {
                    name: '',
                },
            }
        },
        watch: {
            options: {
                handler: 'getItems',
                deep: true,
            },
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                this.loading = true;
                axios.get('/api/v1/tags').then(response => {
                    this.items = response.data.data;
                    this.total = response.data.meta.total;
                    this.loading = false;
                });
            },
            editItem(item) {
                this.editedIndex = this.items.indexOf(item);
                this.editedItem = Object.assign({}, item);
                this.dialog = true;
            },
            deleteItem(item) {
                const index = this.items.indexOf(item);
                if (confirm('Are you sure you want to delete this item?')) {
                    axios.delete('/api/v1/tags/' + item.id).then(response => {
                        console.log('response', response);
                        this.items.splice(index, 1);
                    });
                }
            },
            close() {
                this.dialog = false;
                this.$nextTick(() => {
                    this.editedItem = Object.assign({}, this.defaultItem);
                    this.editedIndex = -1;
                });
            },
            save() {
                if (this.editedIndex > -1) {
                    axios.put('/api/v1/tags/' + this.editedItem.id, this.editedItem).then(response => {
                        console.log('response', response);
                        Object.assign(this.items[this.editedIndex], this.editedItem);
                        this.close();
                    });
                } else {
                    axios.post('/api/v1/tags', this.editedItem).then(response => {
                        console.log('response', response);
                        this.items.push(response.data.data);
                        this.close();
                    });
                }
            },
        },
    };
</script>
