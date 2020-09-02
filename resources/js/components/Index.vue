<template>
    <v-data-table
        :headers="headers"
        :items="items"
        :options.sync="options"
        :items-per-page="15"
        :server-items-length="total"
        :loading="loading"
        class="elevation-1"
    ></v-data-table>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                loading: true,
                total: 0,
                items: [],
                options: {},
                headers: [
                    { text: 'ID', value: 'id' },
                    { text: 'Name', value: 'name' },
                ],
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
                axios.get('/api/v1/entities').then(response => {
                    this.items = response.data.data;
                    this.total = response.data.meta.total;
                    this.loading = false;
                });
            },
        },
    }
</script>
