<template>
    <v-dialog v-model="visible" max-width="320px" scrollable>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    Export
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <div class="d-flex">
                        Columns
                        <v-btn @click="selectAll" text x-small color="blue darken-1" class="ml-auto">Select All</v-btn>
                    </div>
                    <v-checkbox
                        v-for="header in headersExtended"
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
import { mapState } from 'vuex';
import api from '../api';
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
            fields: [],
        };
    },
    computed: {
        ...mapState([
            'presets',
        ]),
        queryTags() {
            const queryJson = (
                this.params.preset
                ? this.presets.find(preset => preset.name === this.params.preset).query
                : this.params.query
            );

            return (queryJson ? JSON.parse(queryJson).tags : []);
        },
        headersExtended() {
            return [
                ...this.headers,
                ...this.fields.map(field => ({ text: field.name, value: 'contents.' + field.id })),
                { text: 'Created At', value: 'created_at' },
                { text: 'Updated At', value: 'updated_at' },
            ];
        },
    },
    methods: {
        selectAll() {
            this.columns = this.headersExtended.map(header => header.value);
        },
        fetchFields() {
            const params = {
                with_tags: this.queryTags,
            };

            this.fields = [];
            api.tags.index(params).then(response => {
                this.fields = response.data.data.filter(tag => tag.entities_count).flatMap(tag => tag.fields);
            });
        },
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
            this.fetchFields();
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
