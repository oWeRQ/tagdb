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
                        <v-btn @click="selectAll" variant="text" size="x-small" color="blue-darken-1" class="ml-auto">Select All</v-btn>
                    </div>
                    <v-checkbox
                        v-for="header in headersExtended"
                        :key="header.value"
                        v-model="columns"
                        :value="header.key"
                        :label="header.title"
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
import api from '../../api';
import toQueryString from '../../functions/toQueryString';

export default {
    props: {
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
        ...mapState({
            presets: state => state.project.presets,
        }),
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
                ...this.fields.map(field => ({ title: field.name, key: 'contents.' + field.id })),
                { title: 'Created At', key: 'created_at' },
                { title: 'Updated At', key: 'updated_at' },
            ];
        },
    },
    watch: {
        visible(value) {
            if (!value) {
                this.$emit('close');
            }
        },
    },
    methods: {
        selectAll() {
            this.columns = this.headersExtended.map(header => header.key);
        },
        fetchFields() {
            const params = {
                with_tags: this.queryTags,
            };

            this.fields = [];
            api.tags.index(params).then(tags => {
                this.fields = tags.filter(tag => tag.entities_count).flatMap(tag => tag.fields);
            });
        },
        submit() {
            const params = {
                export: this.filename + '.csv',
                columns: this.columns,
                ...this.params,
            };
            window.open(api.entities.resource + '?' + toQueryString(params));
            this.close();
        },
        show() {
            this.columns = this.headers.map(header => header.key);
            this.fetchFields();
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
