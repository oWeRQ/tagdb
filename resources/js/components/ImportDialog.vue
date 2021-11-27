<template>
    <v-dialog v-model="visible" max-width="500px">
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
                                :label="header"
                                v-model="fieldsMap[header]"
                                :items="fields"
                                item-text="name"
                                item-value="id"
                                clearable
                                hide-details
                            >
                                <template v-slot:selection="{ item }">
                                    <TagChip :tag="item.tag" class="mr-2"></TagChip>
                                    {{ item.name }}
                                </template>
                                <template v-slot:item="{ item }">
                                    <TagChip :tag="item.tag" class="mr-2"></TagChip>
                                    {{ item.name }}
                                </template>
                            </v-autocomplete>
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
                preview: 0,
            });

            axios.post('/api/v1/import', data).then(response => {
                console.log('preview response', response);
                this.previewData = response.data;
            });
        },
        import() {
            const data = toFormData({
                importFile: this.importFile,
                fields: this.fieldsMap,
                ...this.params,
            });

            axios.post('/api/v1/import', data).then(response => {
                console.log('import response', response);
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
