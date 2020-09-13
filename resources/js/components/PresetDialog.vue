<template>
    <v-dialog v-if="value" v-model="visible" max-width="500px">
        <v-form ref="form" v-model="isValid" @submit.prevent="save">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ isUpdate ? 'Update' : 'Create' }}</span>
                </v-card-title>
                <v-card-text>
                    <crud-form v-model="value" :editable="editable"></crud-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text type="submit" :disabled="!isValid">Save</v-btn>
                    <v-btn color="grey darken-1" text @click="close">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import axios from 'axios';
    import CrudForm from './CrudForm';

    export default {
        components: {
            CrudForm,
        },
        props: {
            resource: {
                type: String,
                default: '/api/v1/presets',
            },
            value: {
                type: Object,
                default: () => ({}),
            },
            isUpdate: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                visible: false,
                isValid: false,
            };
        },
        computed: {
            editable() {
                return [
                    { text: 'Name', value: 'name' },
                    { text: 'Sort', value: 'sort' },
                    { text: 'Query', value: 'query' },
                ];
            },
        },
        methods: {
            save() {
                if (!this.isValid)
                    return;

                if (this.isUpdate) {
                    axios.put(this.resource + '/' + this.value.id, this.value).then(response => {
                        this.$emit('save', response.data.data);
                        this.close();
                    });
                } else {
                    axios.post(this.resource, this.value).then(response => {
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
