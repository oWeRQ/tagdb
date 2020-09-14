<template>
    <v-dialog v-if="editedItem" v-model="visible" max-width="500px">
        <v-form ref="form" v-model="isValid" @submit.prevent="save">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ isUpdate ? 'Update' : 'Create' }}</span>
                </v-card-title>
                <v-card-text>
                    <entity-form v-model="editedItem"></entity-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit" :disabled="!isValid">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import axios from 'axios';
    import EntityForm from './EntityForm';

    export default {
        components: {
            EntityForm,
        },
        props: {
            resource: {
                type: String,
            },
            editedItem: {
                type: Object,
            },
            isUpdate: {
                type: Boolean,
            },
        },
        data() {
            return {
                visible: false,
                isValid: false,
            };
        },
        methods: {
            save() {
                if (!this.isValid)
                    return;

                if (this.isUpdate) {
                    axios.put(this.resource + '/' + this.editedItem.id, this.editedItem).then(response => {
                        this.$emit('save', response.data.data);
                        this.close();
                    });
                } else {
                    axios.post(this.resource, this.editedItem).then(response => {
                        this.$emit('save', response.data.data);
                        this.close();
                    });
                }
            },
            show() {
                this.resetValidation();
                this.visible = true;
            },
            close() {
                this.visible = false;
            },
            resetValidation() {
                this.$refs.form && this.$refs.form.resetValidation();
            },
        },
    };
</script>
