<template>
    <v-dialog v-model="visible" max-width="600px" scrollable>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    <span class="headline">Import</span>
                </v-card-title>
                <v-card-text>
                    <div v-if="!previewData">
                        <v-file-input ref="importFile" v-model="importFile" label="Import File"></v-file-input>
                    </div>
                    <div v-if="previewData">
                        <v-row>
                            <v-col>
                                <div class="text-overline mb-2">Fields</div>
                                <div v-for="(header, i) in previewData.headers" :key="i">
                                    <v-autocomplete
                                        :label='"Import \"" + header + "\" as"'
                                        v-model="fieldsMap[header]"
                                        @change="fieldChange(header)"
                                        :items="fieldItems"
                                        item-text="name"
                                        item-value="id"
                                        clearable
                                        outlined
                                        dense
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
                            </v-col>
                            <v-col>
                                <div class="text-overline mb-2">Tags</div>
                                <TagChip
                                    v-for="tag in previewTags"
                                    :key="tag.name"
                                    :tag="tag"
                                    class="mr-1 mb-1"
                                    @click="showTag(tag)"
                                ></TagChip>
                            </v-col>
                        </v-row>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">{{ submitText }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>

        <CrudDialog
            ref="tagDialog"
            title="Tag"
            :form="TagForm"
            resource="/api/v1/tags"
            :value="editedTag"
            @input="saveTag"
            @delete="deleteTag"
        ></CrudDialog>
    </v-dialog>
</template>

<script>
import cloneDeep from 'clone-deep';
import axios from 'axios';
import toFormData from '../functions/toFormData';
import CrudDialog from './CrudDialog.vue';
import TagForm from './TagForm';
import TagChip from './TagChip.vue';

export default {
    components: {
        CrudDialog,
        TagChip,
    },
    props: {
        params: Object,
    },
    data() {
        return {
            TagForm,
            editedTag: null,
            visible: false,
            importFile: null,
            previewData: null,
            previewTags: [],
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
        updatePreviewTags() {
            this.previewTags = this.previewData.tags.map(name => (this.tags.find(tag => tag.name === name) || { name, id: null, color: null, fields: [] }));
        },
        showTag(tag) {
            this.originalTag = tag;
            this.editedTag = cloneDeep(tag);
            this.$refs.tagDialog.show();
        },
        saveTag(tag) {
            Object.assign(this.originalTag, tag);
            this.fetchFields();
        },
        deleteTag() {
            delete this.originalTag.id;
            delete this.originalTag.color;
            this.originalTag.fields = [];
            this.fetchFields();
        },
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
        fetchTags() {
            this.$root.getTags();
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
                this.updatePreviewTags();
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
            this.previewData = null;
            this.fieldsMap = {};
            this.$nextTick(() => {
                console.log('importFile', this.$refs.importFile);
                this.$refs.importFile.$refs.input.click();
            });
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
