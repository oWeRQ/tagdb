<template>
    <v-dialog v-model="visible" max-width="500px">
        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    <span class="headline">Import</span>
                </v-card-title>
                <v-card-text>
                    <v-file-input v-model="importFile" label="Import File"></v-file-input>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">Run</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
export default {
    props: {
        params: Object,
    },
    data() {
        return {
            visible: false,
            importFile: null,
        };
    },
    methods: {
        submit() {
            const formData = new FormData;
            formData.append('importFile', this.importFile);
            for (const param in this.params) {
                formData.append(param, this.params[param]);
            }

            const headers = {
                'Content-Type': 'multipart/form-data',
            };

            axios.post('/api/v1/import', formData, { headers }).then(response => {
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
        },
    },
};
</script>
