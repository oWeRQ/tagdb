<template>
    <v-dialog v-model="visible" max-width="720px" scrollable>
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    Import
                </v-card-title>
                <v-card-text>
                    <v-file-input ref="importFile" v-model="importFile" label="Import File"></v-file-input>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">Import</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
import api from '../../api';
import toFormData from '../../functions/toFormData';

export default {
    data() {
        return {
            visible: false,
            importFile: null,
        };
    },
    methods: {
        submit() {
            const data = toFormData({
                importFile: this.importFile,
            });

            api.tagsImport.store(data).then(response => {
                this.$emit('done');
                this.$store.dispatch('fetchTagsAndFields');
                this.close();
            });
        },
        show() {
            this.visible = true;
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
