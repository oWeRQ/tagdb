<template>
    <v-dialog v-model="visible" max-width="500px" scrollable>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    <span class="headline">Import</span>
                </v-card-title>
                <v-card-text>
                    <div v-if="!previewData">
                        <v-file-input v-model="importFile" label="Import File"></v-file-input>
                    </div>
                    <div v-if="previewData">
                        <div v-for="(header, i) in previewData.headers" :key="i">
                            <v-autocomplete
                                :label='"Import \"" + header + "\" as"'
                                v-model="fieldsMap[header]"
                                @change="fieldChange(header)"
                                :items="fieldItems"
                                item-text="name"
                                item-value="id"
                                clearable
                                hide-details
                            >
                                <template v-slot:selection="{ item }">
                                    <TagChip v-if="item.tag" :tag="item.tag" class="mr-2"></TagChip>
                                    {{ item.name }}
                                </template>
                                <template v-slot:item="{ item }">
                                    <TagChip v-if="item.tag" :tag="item.tag" class="mr-2"></TagChip>
                                    {{ item.name }}
                                </template>
                            </v-autocomplete>
                        </div>
                        <div class="mt-4">
                            <TagChip v-for="tag in previewTags" :key="tag.name" :tag="tag" class="mr-1 mb-1"></TagChip>
                        </div>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">{{ submitText }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
import axios from 'axios';
import toFormData from '../functions/toFormData';
import TagChip from './TagChip.vue';

export default {
    components: {
        TagChip,
    },
    props: {
        params: Object,
    },
    data() {
        return {
            visible: false,
            importFile: null,
            previewData: null,
            fieldsMap: {},
            fields: [],
        };
    },
    computed: {
        fieldItems() {
            return [
                { id: 'tags', name: 'Tags' },
                { id: 'name', name: 'Name' },
                { id: 'created_at', name: 'Created At' },
                { id: 'updated_at', name: 'Updated At' },
                ...this.fields,
            ];
        },
        tags() {
            return this.$root.tags;
        },
        previewTags() {
            return this.previewData.tags.map(name => (this.tags.find(tag => tag.name === name) || { name, fields: [] }));
        },
        submitText() {
            if (!this.previewData)
                return 'Preview';
            else
                return 'Import';
        },
    },
    mounted() {
        this.fetchFields();
    },
    methods: {
        fieldChange(header) {
            const field = this.fieldsMap[header];
            if (field === 'tags') {
                this.preview();
            }
        },
        autoFieldsMap() {
            for (const header of this.previewData.headers) {
                this.fieldsMap[header] = this.fieldItems.find(field => field.name === header)?.id;
            }
        },
        fetchFields() {
            axios.get('/api/v1/fields').then(response => {
                this.fields = response.data.data.map(field => ({
                    ...field,
                    tag: this.tags.find(tag => tag.id == field.tag_id),
                }));
            });
        },
        submit() {
            if (!this.previewData)
                this.preview();
            else
                this.import();
        },
        preview() {
            const data = toFormData({
                importFile: this.importFile,
                fields: this.fieldsMap,
                preview: 0,
            });

            axios.post('/api/v1/import', data).then(response => {
                this.previewData = response.data;
                this.autoFieldsMap();
            });
        },
        import() {
            const data = toFormData({
                importFile: this.importFile,
                fields: this.fieldsMap,
                ...this.params,
            });

            axios.post('/api/v1/import', data).then(response => {
                this.$emit('done');
                this.close();
            });
        },
        show() {
            this.visible = true;
        },
        close() {
            this.visible = false;
            this.previewData = null;
            this.fieldsMap = {};
        },
    },
};
</script>
