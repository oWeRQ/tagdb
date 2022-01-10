<template>
    <v-dialog v-model="visible" max-width="720px" scrollable>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    Import
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
                                        :filter="fieldFilter"
                                        item-text="name"
                                        item-value="id"
                                        clearable
                                        outlined
                                        dense
                                    >
                                        <template v-slot:selection="{ item }">
                                            <TagChip v-if="item.tag" :tag="item.tag" small class="mr-2"></TagChip>
                                            {{ item.name }}
                                        </template>
                                        <template v-slot:item="{ item }">
                                            <TagChip v-if="item.tag" :tag="item.tag" small class="mr-2"></TagChip>
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
                                    class="mr-2 mb-2"
                                    @click="editTag(tag)"
                                ></TagChip>
                                <div class="mt-2">
                                    <v-btn @click="addTag" text x-small color="blue darken-1">
                                        <v-icon left>mdi-plus</v-icon>
                                        Add Tag
                                    </v-btn>
                                </div>
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
    </v-dialog>
</template>

<script>
import { mapState } from 'vuex';
import cloneDeep from 'clone-deep';
import api from '../../api';
import toFormData from '../../functions/toFormData';
import fuzzyMatch from '../../functions/fuzzyMatch';
import TagDialog from '../tag/TagDialog.vue';
import TagChip from '../tag/TagChip.vue';

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
            previewTags: [],
            fieldsMap: {},
        };
    },
    computed: {
        ...mapState([
            'tags',
            'fields',
        ]),
        fieldItems() {
            return [
                { id: 'tags', name: 'Tags' },
                { id: 'name', name: 'Name' },
                { id: 'created_at', name: 'Created At' },
                { id: 'updated_at', name: 'Updated At' },
                ...this.fields.map(field => ({
                    ...field,
                    tag: this.tags.find(tag => tag.id == field.tag_id),
                })),
            ];
        },
        submitText() {
            if (!this.previewData)
                return 'Preview';
            else
                return 'Import';
        },
    },
    methods: {
        fieldFilter(item, queryText) {
            return fuzzyMatch(`${item.tag?.name} ${item.name}`, queryText);
        },
        updatePreviewTags() {
            this.previewTags = this.previewData.tags.map(name => (this.tags.find(tag => tag.name === name) || { name, id: null, color: null, fields: [] }));
        },
        addTag() {
            this.editTag({
                name: '',
                id: null,
                color: null,
                fields: [],
            });
        },
        editTag(tag) {
            this.$root.showDialog(TagDialog, {
                value: cloneDeep(tag),
            }, {
                input: this.saveTag,
                delete: this.deleteTag,
            });
        },
        saveTag(tag) {
            const previewTag = this.previewTags.find(item => item.id === tag.id) || this.previewTags.find(item => !item.id && item.name === tag.name);
            if (previewTag) {
                Object.assign(previewTag, tag);
            } else {
                this.previewTags.push(tag);
            }
        },
        deleteTag(tag) {
            const previewTag = this.previewTags.find(item => item.id === tag.id);
            if (previewTag) {
                delete previewTag.id;
                delete previewTag.color;
                previewTag.fields = [];
            }
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

            api.import.store(data).then(response => {
                this.previewData = response;
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

            api.import.store(data).then(response => {
                this.$emit('done');
                this.close();
            });
        },
        show() {
            this.visible = true;
            this.previewData = null;
            this.fieldsMap = {};
            this.$nextTick(() => {
                this.$refs.importFile.$refs.input.click();
            });
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
