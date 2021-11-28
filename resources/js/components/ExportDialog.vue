<template>
    <v-dialog v-model="visible" max-width="250px">
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    Export
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <div>Columns</div>
                    <v-checkbox
                        v-for="header in headers"
                        :key="header.value"
                        v-model="columns"
                        :value="header.value"
                        :label="header.text"
                        hide-details
                    ></v-checkbox>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">Download</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
import toQueryString from '../functions/toQueryString';

export default {
    props: {
        resource: String,
        filename: String,
        headers: Array,
        params: Object,
    },
    data() {
        return {
            visible: false,
            columns: [],
        };
    },
    methods: {
        submit() {
            const params = {
                sort: this.sort,
                export: this.filename,
                columns: this.columns,
                ...this.params,
            };
            window.open(this.resource + '?' + toQueryString(params));
            this.close();
        },
        show() {
            this.columns = this.headers.map(header => header.value);
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
